<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\Classroom;
use App\Helpers\NotificationHelper;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class AssessmentController extends Controller
{
    public function index(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $assessments = $class->assessments()->latest()->paginate(5);
        return view('instructor.classes.assessments.index', compact('class', 'assessments'));
    }

    public function create(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        return view('instructor.classes.assessments.create', compact('class'));
    }

    public function store(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'due_date' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        $validated['class_id'] = $class->id;
        $validated['is_published'] = $request->has('is_published');

        $assessment = Assessment::create($validated);
        ActivityLogHelper::log('created', 'assessment', "created assessment '{$assessment->title}' in '{$class->name}'");

        if ($assessment->is_published) {
            NotificationHelper::sendToClass(
                $class->id,
                '📋 New Assessment: ' . $assessment->title,
                'Due: ' . ($assessment->due_date ? $assessment->due_date->format('M d, Y') : 'No deadline') . ' | ' . $assessment->points . ' points',
                route('dashboard.classes.assessments.show', [$class, $assessment])
            );
        }

        return redirect()->route('instructor.classes.assessments.index', $class)
            ->with('success', 'Assessment created!');
    }

    public function edit(Classroom $class, Assessment $assessment)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($assessment->class_id !== $class->id) {
            abort(404);
        }
        return view('instructor.classes.assessments.edit', compact('class', 'assessment'));
    }

    public function update(Request $request, Classroom $class, Assessment $assessment)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($assessment->class_id !== $class->id) {
            abort(404);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'due_date' => 'nullable|date',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');
        $assessment->update($validated);

        if ($assessment->wasChanged('is_published') && $assessment->is_published) {
            NotificationHelper::sendToClass(
                $class->id,
                'New Assessment: ' . $assessment->title,
                'Due: ' . ($assessment->due_date ? $assessment->due_date->format('M d, Y') : 'No deadline') . ' | ' . $assessment->points . ' points',
                route('dashboard.classes.assessments.show', [$class, $assessment])
            );
        }

        return redirect()->route('instructor.classes.assessments.index', $class)
            ->with('success', 'Assessment updated!');
    }

    public function show(Classroom $class, Assessment $assessment)
    {
        if ($assessment->class_id !== $class->id) {
            abort(404);
        }

        if (auth()->user()->isAdministrator()) {
            $submission = AssessmentSubmission::where('assessment_id', $assessment->id)
                ->where('user_id', auth()->id())
                ->first();
            return view('user.classes.assessments.show', compact('class', 'assessment', 'submission'));
        }
        if (auth()->user()->isInstructor()) {
            if ($class->instructor_id !== auth()->id()) {
                abort(403);
            }
            $submission = AssessmentSubmission::where('assessment_id', $assessment->id)
                ->where('user_id', auth()->id())
                ->first();
            return view('user.classes.assessments.show', compact('class', 'assessment', 'submission'));
        }
        
        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $submission = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('user_id', auth()->id())
            ->first();

        return view('user.classes.assessments.show', compact('class', 'assessment', 'submission'));
    }

    public function submit(Request $request, Classroom $class, Assessment $assessment)
    {
        if ($assessment->class_id !== $class->id) {
            abort(404);
        }

        // Instructors can't submit their own assessment
        if (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) {
            abort(403);
        }

        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }
        
        if ($assessment->due_date && now()->isAfter($assessment->due_date)) {
            return back()->with('error', 'This assessment is past the due date.');
        }

        // Block re-submit if already graded
        $existing = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existing && $existing->status === 'graded') {
            return back()->with('error', 'This assessment has already been graded.');
        }

        $validated = $request->validate([
            'content' => 'required|string',
        ]);

        AssessmentSubmission::updateOrCreate(
            ['assessment_id' => $assessment->id, 'user_id' => auth()->id()],
            [
                'content' => $validated['content'],
                'status' => 'submitted',
                'submitted_at' => now(),
            ]
        );

        NotificationHelper::send(
            $class->instructor_id,
            '📥 ' . $class->name . ($class->section ? ' (Block ' . $class->section . ')' : ''),
            auth()->user()->name . ' submitted "' . $assessment->title . '"',
            route('instructor.classes.assessments.submissions', [$class, $assessment])
        );

        return back()->with('success', 'Submission sent!');
    }

    public function submissions(Classroom $class, Assessment $assessment)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($assessment->class_id !== $class->id) {
            abort(404);
        }
        $submissions = $assessment->submissions()->with('user')->get();
        return view('instructor.classes.assessments.submissions', compact('class', 'assessment', 'submissions'));
    }

    public function grade(Request $request, Classroom $class, Assessment $assessment, AssessmentSubmission $submission)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($assessment->class_id !== $class->id) {
            abort(404);
        }
        if ($submission->assessment_id !== $assessment->id) {
            abort(404);
        }
        $validated = $request->validate([
            'score' => 'required|integer|min:0|max:' . $assessment->points,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'score' => $validated['score'],
            'feedback' => $validated['feedback'],
            'status' => 'graded',
        ]);

        NotificationHelper::send(
            $submission->user_id,
            '✅ Assessment Graded: ' . $assessment->title,
            'You scored ' . $validated['score'] . '/' . $assessment->points,
            route('dashboard.classes.assessments.show', [$class, $assessment])
        );

        return back()->with('success', 'Submission graded!');
    }

    public function destroy(Classroom $class, Assessment $assessment)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        if ($assessment->class_id !== $class->id) {
            abort(404);
        }
        $assessment->delete();
        ActivityLogHelper::log('deleted', 'assessment', "deleted assessment '{$assessment->title}'");
        return back()->with('success', 'Assessment deleted!');
    }

    public function import(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $otherClasses = Classroom::where('instructor_id', auth()->id())
            ->where('id', '!=', $class->id)
            ->get();
        return view('instructor.classes.assessments.import', compact('class', 'otherClasses'));
    }

    public function copyAssessments(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) {
            abort(403);
        }
        $request->validate([
            'from_class' => 'required|exists:classes,id',
            'assessments' => 'required|array',
        ]);

        $sourceClass = Classroom::where('instructor_id', auth()->id())
            ->findOrFail($request->from_class);

        $assessments = $sourceClass->assessments()->whereIn('id', $request->assessments)->get();

        foreach ($assessments as $assessment) {
            Assessment::create([
                'class_id' => $class->id,
                'title' => $assessment->title,
                'description' => $assessment->description,
                'instructions' => $assessment->instructions,
                'points' => $assessment->points,
                'due_date' => $assessment->due_date,
                'is_published' => false,
            ]);
        }

        return redirect()->route('instructor.classes.assessments.index', $class)
            ->with('success', count($assessments) . ' assessments imported!');
    }

    public function studentIndex(Classroom $class)
    {
        if (auth()->user()->isAdministrator()) {
            $assessments = $class->assessments()->where('is_published', true)->latest()->paginate(10);
            return view('user.classes.assessments.index', compact('class', 'assessments'));
        }
        if (auth()->user()->isInstructor()) {
            if ($class->instructor_id !== auth()->id()) {
                abort(403);
            }
            $assessments = $class->assessments()->where('is_published', true)->latest()->paginate(10);
            return view('user.classes.assessments.index', compact('class', 'assessments'));
        }
        
        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();

        if (!$enrolled) {
            abort(403);
        }

        $assessments = $class->assessments()->where('is_published', true)->latest()->paginate(10);
        return view('user.classes.assessments.index', compact('class', 'assessments'));
    }
}