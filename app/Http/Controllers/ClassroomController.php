<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\AssessmentSubmission;
use App\Models\QuizSubmission;
use App\Models\ClassResource;
use App\Models\User;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $query = Classroom::where('instructor_id', auth()->id());

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('section', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $classes = $query->latest()->paginate(3)->withQueryString();
        return view('instructor.classes.index', compact('classes'));
    }

    public function create()
    {
        return view('instructor.classes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $validated['instructor_id'] = auth()->id();
        $validated['code'] = Classroom::generateCode();

        Classroom::create($validated);
        ActivityLogHelper::log('created', 'class', "created a new class '{$validated['name']}'");

        return redirect()->route('instructor.classes.index')
            ->with('success', 'Class created successfully! Code: ' . $validated['code']);
    }

    public function show(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        return view('instructor.classes.show', compact('class'));
    }

    public function edit(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        return view('instructor.classes.edit', compact('class'));
    }

    public function update(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'section' => 'nullable|string|max:50',
            'description' => 'nullable|string',
        ]);

        $class->update($validated);

        return redirect()->route('instructor.classes.show', $class)
            ->with('success', 'Class updated successfully!');
    }

    public function destroy(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }

        // Capture enrolled students BEFORE deletion
        $enrolledStudents = $class->students()
            ->wherePivot('status', 'approved')
            ->get();

        $class->students()->detach();
        $class->delete();

        // Demote anyone no longer in any approved class
        foreach ($enrolledStudents as $student) {
            if ($student->role !== 'student') continue;

            $stillInAClass = $student->classes()
                ->wherePivot('status', 'approved')
                ->exists();

            if (!$stillInAClass) {
                $student->update(['role' => 'user']);
            }
        }

        ActivityLogHelper::log('deleted', 'class', "deleted class '{$class->name}'");
        return redirect()->route('instructor.classes.index')
            ->with('success', 'Class deleted successfully!');
    }

    public function join(Request $request)
    {
        // Enforce: one class per student (blocks a second join attempt)
        if (auth()->user()->classes()->exists()) {
            return back()->with('error', 'You can only join one class.');
        }

        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $class = Classroom::whereRaw('UPPER(code) = ?', [strtoupper($request->code)])->first();

        if (!$class) {
            return back()->with('error', 'Invalid class code. Please check and try again.');
        }

        if ($class->students()->where('user_id', auth()->id())->exists()) {
            return back()->with('error', 'You are already enrolled in this class.');
        }

        $class->students()->attach(auth()->id(), ['status' => 'pending']);

        return redirect()->route('dashboard.classes.index')
            ->with('success', 'Join request sent! Wait for your instructor to approve.');
    }

    public function studentClasses()
    {
        $classes = auth()->user()->classes()->wherePivot('status', 'approved')->latest()->get();
        return view('user.classes.index', compact('classes'));
    }

    public function studentShow(Classroom $class)
    {
        $enrollment = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->first();
            
        if (!$enrollment) {
            return redirect()->route('dashboard.classes.index')
                ->with('error', 'You must be approved to view this class.');
        }
        
        $assessments = $class->assessments()->where('is_published', true)->get();
        $quizzes = $class->quizzes()->where('is_published', true)->get();
        
        $assessmentSubmissions = AssessmentSubmission::where('user_id', auth()->id())
            ->whereIn('assessment_id', $assessments->pluck('id'))
            ->get()
            ->keyBy('assessment_id');
        
        $quizSubmissions = QuizSubmission::where('user_id', auth()->id())
            ->whereIn('quiz_id', $quizzes->pluck('id'))
            ->get()
            ->keyBy('quiz_id');
        
        $assessmentPoints = $assessmentSubmissions->sum('score');
        $quizPoints = $quizSubmissions->sum('score');
        $totalAssessmentPoints = $assessments->sum('points');
        $totalQuizPoints = $quizzes->sum('points');
        $totalPoints = $assessmentPoints + $quizPoints;
        $totalPossible = $totalAssessmentPoints + $totalQuizPoints;
        
        return view('user.classes.show', compact(
            'class', 'assessments', 'quizzes', 'assessmentSubmissions',
            'quizSubmissions', 'assessmentPoints', 'quizPoints',
            'totalAssessmentPoints', 'totalQuizPoints', 'totalPoints', 'totalPossible'
        ));
    }

    public function approve(Classroom $class, $userId)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $class->students()->updateExistingPivot($userId, ['status' => 'approved']);
        
        $user = User::find($userId);
        if ($user && $user->role === 'user') {
            $user->update(['role' => 'student']);
        }
        
        return back()->with('success', 'Student approved!');
    }

    public function reject(Classroom $class, $userId)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $class->students()->detach($userId);

        // Demote to 'user' if no longer in any approved class
        $user = User::find($userId);
        if ($user && $user->role === 'student') {
            $hasOtherClasses = $user->classes()
                ->wherePivot('status', 'approved')
                ->exists();
            if (!$hasOtherClasses) {
                $user->update(['role' => 'user']);
            }
        }

        return back()->with('success', 'Student removed!');
    }
    public function leaderboard(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $students = $class->students()->wherePivot('status', 'approved')->get();
        $assessments = $class->assessments()->where('is_published', true)->get();
        $quizzes = $class->quizzes()->where('is_published', true)->get();
        $totalAssessments = $assessments->count();
        $totalQuizzes = $quizzes->count();
        
        // Batch load ALL submissions (fixes N+1)
        $allAssessmentSubs = AssessmentSubmission::whereIn('assessment_id', $assessments->pluck('id'))->get();
        $allQuizSubs = QuizSubmission::whereIn('quiz_id', $quizzes->pluck('id'))->get();

        $leaderboard = $students->map(function ($student) use ($assessments, $quizzes, $totalAssessments, $totalQuizzes, $allAssessmentSubs, $allQuizSubs) {
            $submissions = $allAssessmentSubs->where('user_id', $student->id);
            $quizSubmissions = $allQuizSubs->where('user_id', $student->id);
            
            $assessmentPoints = $submissions->sum('score');
            $quizPoints = $quizSubmissions->sum('score');
            $totalPoints = $assessmentPoints + $quizPoints;
            
            // Use already-loaded submissions (no more DB queries)
            $submittedAssessmentIds = $submissions->pluck('assessment_id')->toArray();
            $submittedQuizIds = $quizSubmissions->pluck('quiz_id')->toArray();

            $pending = $assessments->filter(function ($assessment) use ($submittedAssessmentIds) {
                return !in_array($assessment->id, $submittedAssessmentIds)
                    && (!$assessment->due_date || now()->lessThanOrEqualTo($assessment->due_date));
            })->count();
            $pending += $quizzes->filter(function ($quiz) use ($submittedQuizIds) {
                return !in_array($quiz->id, $submittedQuizIds)
                    && (!$quiz->due_date || now()->lessThanOrEqualTo($quiz->due_date));
            })->count();

            $overdue = $assessments->filter(function ($assessment) use ($submittedAssessmentIds) {
                return !in_array($assessment->id, $submittedAssessmentIds)
                    && $assessment->due_date && now()->isAfter($assessment->due_date);
            })->count();
            $overdue += $quizzes->filter(function ($quiz) use ($submittedQuizIds) {
                return !in_array($quiz->id, $submittedQuizIds)
                    && $quiz->due_date && now()->isAfter($quiz->due_date);
            })->count();
            
            return [
                'student' => $student, 'total_points' => $totalPoints,
                'graded_assessments' => $submissions->whereNotNull('score')->count(),
                'total_assessments' => $totalAssessments,
                'graded_quizzes' => $quizSubmissions->count(),
                'total_quizzes' => $totalQuizzes, 'pending' => $pending, 'overdue' => $overdue,
            ];
        })->sortByDesc('total_points')->values();
        
        return view('instructor.classes.leaderboard', compact('class', 'leaderboard'));
    }

    public function gradebook(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }

        $students = $class->students()
            ->wherePivot('status', 'approved')
            ->orderBy('name')
            ->get();

        $assessments = $class->assessments()
            ->where('is_published', true)
            ->orderBy('created_at')
            ->get();

        $quizzes = $class->quizzes()
            ->where('is_published', true)
            ->orderBy('created_at')
            ->get();

        $assessmentSubs = AssessmentSubmission::whereIn('assessment_id', $assessments->pluck('id'))
            ->whereNotNull('score')
            ->get()
            ->groupBy('user_id');

        $quizSubs = QuizSubmission::whereIn('quiz_id', $quizzes->pluck('id'))
            ->whereNotNull('score')
            ->get()
            ->groupBy('user_id');

        return view('instructor.classes.gradebook', compact(
            'class', 'students', 'assessments', 'quizzes', 'assessmentSubs', 'quizSubs'
        ));
    }

    public function exportGradebook(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }

        $students = $class->students()
            ->wherePivot('status', 'approved')
            ->orderBy('name')
            ->get();

        $assessments = $class->assessments()
            ->where('is_published', true)
            ->orderBy('created_at')
            ->get();

        $quizzes = $class->quizzes()
            ->where('is_published', true)
            ->orderBy('created_at')
            ->get();

        $assessmentSubs = AssessmentSubmission::whereIn('assessment_id', $assessments->pluck('id'))
            ->whereNotNull('score')
            ->get()
            ->groupBy('user_id');

        $quizSubs = QuizSubmission::whereIn('quiz_id', $quizzes->pluck('id'))
            ->whereNotNull('score')
            ->get()
            ->groupBy('user_id');

        // Build the CSV
        $filename = 'gradebook-' . $class->id . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($students, $assessments, $quizzes, $assessmentSubs, $quizSubs) {
            $file = fopen('php://output', 'w');

            // Header row
            $headerRow = ['Student'];
            foreach ($assessments as $a) {
                $headerRow[] = $a->title . ' (' . $a->points . ' pts)';
            }
            $headerRow[] = 'Assess Total';
            foreach ($quizzes as $q) {
                $headerRow[] = $q->title . ' (' . $q->points . ' pts)';
            }
            $headerRow[] = 'Quiz Total';
            $headerRow[] = 'Grand Total';
            fputcsv($file, $headerRow);

            // Data rows
            foreach ($students as $student) {
                $studentAssessSubs = $assessmentSubs->get($student->id, collect())->keyBy('assessment_id');
                $studentQuizSubs = $quizSubs->get($student->id, collect())->keyBy('quiz_id');

                $row = [$student->name];

                foreach ($assessments as $a) {
                    $sub = $studentAssessSubs->get($a->id);
                    $row[] = $sub?->score ?? '';
                }
                $row[] = $studentAssessSubs->sum('score');

                foreach ($quizzes as $q) {
                    $sub = $studentQuizSubs->get($q->id);
                    $row[] = $sub?->score ?? '';
                }
                $row[] = $studentQuizSubs->sum('score');
                $row[] = $studentAssessSubs->sum('score') + $studentQuizSubs->sum('score');

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function analytics(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }

        $students = $class->students()->wherePivot('status', 'approved')->get();
        $studentCount = $students->count();

        $assessments = $class->assessments()->where('is_published', true)->get();
        $quizzes = $class->quizzes()->where('is_published', true)->get();

        $assessmentCount = $assessments->count();
        $quizCount = $quizzes->count();

        $totalAssessmentPoints = $assessments->sum('points');
        $totalQuizPoints = $quizzes->sum('points');

        $assessmentIds = $assessments->pluck('id');
        $quizIds = $quizzes->pluck('id');
        $studentIds = $students->pluck('id');

        // Load ALL submissions once
        $allAssessmentSubs = AssessmentSubmission::whereIn('assessment_id', $assessmentIds)
            ->whereNotNull('score')->get();
        $allQuizSubs = QuizSubmission::whereIn('quiz_id', $quizIds)
            ->whereNotNull('score')->get();

        // Averages (divide by max points × student count so the % is correct)
        $assessmentAvg = ($totalAssessmentPoints > 0 && $studentCount > 0)
            ? round(($allAssessmentSubs->sum('score') / ($totalAssessmentPoints * $studentCount)) * 100, 1) : 0;
        $quizAvg = ($totalQuizPoints > 0 && $studentCount > 0)
            ? round(($allQuizSubs->sum('score') / ($totalQuizPoints * $studentCount)) * 100, 1) : 0;

        // Assessment Breakdown
        $assessmentBreakdown = [];
        foreach ($assessments as $assessment) {
            $subs = $allAssessmentSubs->where('assessment_id', $assessment->id);
            $submittedCount = $subs->count();
            $avg = $submittedCount > 0 ? round($subs->avg('score') / $assessment->points * 100, 1) : 0;
            $assessmentBreakdown[] = [
                'title' => $assessment->title, 'average' => $avg,
                'submitted' => $submittedCount, 'total' => $studentCount,
                'submission_rate' => $studentCount > 0 ? round(($submittedCount / $studentCount) * 100) : 0,
            ];
        }

        // Quiz Breakdown
        $quizBreakdown = [];
        foreach ($quizzes as $quiz) {
            $subs = $allQuizSubs->where('quiz_id', $quiz->id);
            $submittedCount = $subs->count();
            $avg = $submittedCount > 0 ? round($subs->avg('score') / $quiz->points * 100, 1) : 0;
            $quizBreakdown[] = [
                'title' => $quiz->title, 'average' => $avg,
                'submitted' => $submittedCount, 'total' => $studentCount,
                'submission_rate' => $studentCount > 0 ? round(($submittedCount / $studentCount) * 100) : 0,
            ];
        }

        // Timeline - group by date in memory
        $assessmentByDate = $allAssessmentSubs->groupBy(fn($s) => $s->submitted_at->format('Y-m-d'));
        $quizByDate = $allQuizSubs->groupBy(fn($s) => $s->submitted_at->format('Y-m-d'));
        
        $submissionTimeline = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $submissionTimeline[] = [
                'date' => $date,
                'assessments' => $assessmentByDate->get($date, collect())->count(),
                'quizzes' => $quizByDate->get($date, collect())->count(),
            ];
        }

        return view('instructor.classes.analytics', compact(
            'class', 'studentCount', 'assessmentCount', 'quizCount',
            'assessmentAvg', 'quizAvg', 'assessmentBreakdown', 'quizBreakdown',
            'submissionTimeline'
        ));
    }

    public function resources(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        $resources = $class->resources()->get();
        $sensors = \App\Models\Sensor::where('is_active', true)->get();
        $projects = \App\Models\Project::where('is_active', true)->get();
        $videos = \App\Models\Video::where('is_active', true)->get();
        return view('instructor.classes.resources', compact('class', 'resources', 'sensors', 'projects', 'videos'));
    }

    public function storeResource(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        $validated = $request->validate(['resource_type' => 'required|in:sensor,project,video', 'resource_id' => 'required|integer']);
        $exists = $class->resources()->where('resource_type', $validated['resource_type'])->where('resource_id', $validated['resource_id'])->exists();
        if (!$exists) {
            $class->resources()->create(['resource_type' => $validated['resource_type'], 'resource_id' => $validated['resource_id']]);
        }
        return back()->with('success', 'Resource added!');
    }

    public function destroyResource(Classroom $class, ClassResource $resource)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        $resource->delete();
        return back()->with('success', 'Resource removed.');
    }

    public function approveAll(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        $pendingStudents = $class->students()->wherePivot('status', 'pending')->get();
        foreach ($pendingStudents as $student) {
            $class->students()->updateExistingPivot($student->id, ['status' => 'approved']);
            if ($student->role === 'user') $student->update(['role' => 'student']);
        }
        return back()->with('success', count($pendingStudents) . ' students approved!');
    }
}
