<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\QuizOption;
use App\Models\QuizAnswer;
use App\Models\QuizSubmission;
use App\Models\Classroom;
use App\Helpers\NotificationHelper;
use App\Helpers\ActivityLogHelper;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    // Instructor: List Quizzes
    public function index(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        $quizzes = $class->quizzes()->latest()->paginate(5);
        return view('instructor.classes.quizzes.index', compact('class', 'quizzes'));
    }

    // Instructor: Create Form
    public function create(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        return view('instructor.classes.quizzes.create', compact('class'));
    }

    // Instructor: Store Quiz
    public function store(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'due_date' => 'nullable|date',
            'time_limit' => 'nullable|integer|min:1|max:600',
            'is_published' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.options.*.is_correct' => 'nullable|boolean',
        ]);

        $quiz = Quiz::create([
            'class_id' => $class->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'points' => $validated['points'],
            'passing_score' => $validated['passing_score'],
            'due_date' => $validated['due_date'] ?? null,
            'time_limit' => $validated['time_limit'] ?? null,
            'is_published' => $request->has('is_published'),
        ]);

        foreach ($validated['questions'] as $qIndex => $qData) {
            $question = $quiz->questions()->create([
                'question' => $qData['question'],
                'order' => $qIndex + 1,
            ]);

            foreach ($qData['options'] as $oIndex => $oData) {
                $question->options()->create([
                    'option_text' => $oData['text'],
                    'is_correct' => $oData['is_correct'] ?? false,
                    'order' => $oIndex + 1,
                ]);
            }
        }

        ActivityLogHelper::log('created', 'quiz', "created quiz '{$quiz->title}' in '{$class->name}'");

        if ($quiz->is_published) {
            NotificationHelper::sendToClass($class->id, '📝 New Quiz: ' . $quiz->title, $quiz->points . ' points', route('dashboard.classes.quizzes.show', [$class, $quiz]));
        }

        return redirect()->route('instructor.classes.quizzes.index', $class)->with('success', 'Quiz created!');
    }

    // Instructor: Edit Form
    public function edit(Classroom $class, Quiz $quiz)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        if ($quiz->class_id !== $class->id) abort(404);
        $quiz->load('questions.options');
        return view('instructor.classes.quizzes.edit', compact('class', 'quiz'));
    }

    // Instructor: Update Quiz
    public function update(Request $request, Classroom $class, Quiz $quiz)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        if ($quiz->class_id !== $class->id) abort(404);

        // Prevent editing while a student is still taking the quiz
        if ($quiz->submissions()->where('status', 'in_progress')->exists()) {
            return back()->with('error', 'A student is currently taking this quiz. Please wait until they finish before editing.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'due_date' => 'nullable|date',
            'time_limit' => 'nullable|integer|min:1|max:600',
            'is_published' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.options' => 'required|array|min:2',
            'questions.*.options.*.text' => 'required|string',
            'questions.*.options.*.is_correct' => 'nullable|boolean',
        ]);

        $quiz->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'instructions' => $validated['instructions'] ?? null,
            'points' => $validated['points'],
            'passing_score' => $validated['passing_score'],
            'due_date' => $validated['due_date'] ?? null,
            'time_limit' => $validated['time_limit'] ?? null,
            'is_published' => $request->has('is_published'),
        ]);

        // Notify students when quiz gets published
        if ($quiz->wasChanged('is_published') && $quiz->is_published) {
            NotificationHelper::sendToClass(
                $class->id,
                '📝 New Quiz: ' . $quiz->title,
                $quiz->points . ' points',
                route('dashboard.classes.quizzes.show', [$class, $quiz])
            );
        }

        // Delete old questions and options
        $quiz->questions()->delete();

        // Create new ones
        foreach ($validated['questions'] as $qIndex => $qData) {
            $question = $quiz->questions()->create([
                'question' => $qData['question'],
                'order' => $qIndex + 1,
            ]);

            foreach ($qData['options'] as $oIndex => $oData) {
                $question->options()->create([
                    'option_text' => $oData['text'],
                    'is_correct' => $oData['is_correct'] ?? false,
                    'order' => $oIndex + 1,
                ]);
            }
        }
        return redirect()->route('instructor.classes.quizzes.index', $class)->with('success', 'Quiz updated!');
    }

    // Instructor: Delete Quiz
    public function destroy(Classroom $class, Quiz $quiz)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        if ($quiz->class_id !== $class->id) abort(404);
        $quiz->delete();
        ActivityLogHelper::log('deleted', 'quiz', "deleted quiz '{$quiz->title}'");
        return back()->with('success', 'Quiz deleted!');
    }

    // Student/Instructor: Show Quiz
    public function show(Classroom $class, Quiz $quiz)
    {
        if ($quiz->class_id !== $class->id) abort(404);

        // Students must be enrolled
        if (!auth()->user()->isInstructor() && !auth()->user()->isAdministrator()) {
            $enrolled = $class->students()
                ->where('user_id', auth()->id())
                ->wherePivot('status', 'approved')
                ->exists();
            if (!$enrolled) abort(403);
        }

        $quiz->load('questions.options');
        $submission = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->first();

        // If time limit expired and student never submitted, auto-finalize from saved answers
        if ($submission && $submission->status === 'in_progress'
            && $quiz->time_limit && $submission->started_at
            && now()->greaterThan($submission->started_at->copy()->addMinutes($quiz->time_limit))) {

            $totalQuestions = $quiz->questions->count();
            $savedAnswers = QuizAnswer::where('user_id', auth()->id())
                ->whereIn('quiz_question_id', $quiz->questions->pluck('id'))
                ->get();
            $correctCount = $savedAnswers->where('is_correct', true)->count();
            $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * $quiz->points) : 0;

            $submission->update([
                'score' => $score,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctCount,
                'status' => 'graded',
                'submitted_at' => now(),
            ]);

            $passed = ($correctCount / max($totalQuestions, 1)) * 100 >= $quiz->passing_score;
            $emoji = $passed ? '🎉' : '📚';
            NotificationHelper::send(
                $class->instructor_id,
                '📥 ' . $class->name . ($class->section ? ' (Block ' . $class->section . ')' : ''),
                auth()->user()->name . ' submitted "' . $quiz->title . '" — ' . $correctCount . '/' . $totalQuestions . ' correct',
                route('instructor.classes.quizzes.submissions', [$class, $quiz])
            );
            NotificationHelper::send(auth()->id(), "{$emoji} Quiz Result: {$quiz->title}", "{$correctCount}/{$totalQuestions} correct · {$score} points", route('dashboard.classes.quizzes.show', [$class, $quiz]));

            return redirect()->route('dashboard.classes.quizzes.show', [$class, $quiz])
                ->with('success', 'Time expired — your saved answers were submitted automatically.');
        }

        // Only treat as "submitted" if it's actually been graded
        $completedSubmission = $submission && $submission->status === 'graded' ? $submission : null;

        // Load any previously saved answers (for autosave / refresh recovery)
        $savedAnswers = QuizAnswer::where('user_id', auth()->id())
            ->whereIn('quiz_question_id', $quiz->questions->pluck('id'))
            ->pluck('selected_option_id', 'quiz_question_id')
            ->toArray();

        return view('user.classes.quizzes.show', compact('class', 'quiz', 'submission', 'completedSubmission', 'savedAnswers'));
    }

    // Student: Start Quiz (marks started_at for time limit)
    public function start(Classroom $class, Quiz $quiz)
    {
        if ($quiz->class_id !== $class->id) abort(404);

        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();
        if (!$enrolled) abort(403);

        // If already submitted, do nothing
        $existing = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->first();
        if ($existing) {
            if ($existing->status === 'graded') {
                return response()->json(['already_submitted' => true]);
            }
            return response()->json([
                'started_at' => $existing->started_at
                    ? $existing->started_at->toIso8601String()
                    : null,
            ]);
        }

        // Create a "in_progress" submission row if not exists
        QuizSubmission::firstOrCreate(
            ['quiz_id' => $quiz->id, 'user_id' => auth()->id()],
            [
                'status' => 'in_progress',
                'started_at' => now(),
                'tab_switches' => 0,
            ]
        );

        return response()->json(['started_at' => now()->toIso8601String()]);
    }

    // Student: Autosave a single answer
    public function saveAnswer(Request $request, Classroom $class, Quiz $quiz)
    {
        if ($quiz->class_id !== $class->id) abort(404);

        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();
        if (!$enrolled) abort(403);

        // Don't allow saving if already graded
        $submission = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->first();
        if ($submission && $submission->status === 'graded') {
            return response()->json(['error' => 'already_submitted'], 400);
        }

        $request->validate([
            'question_id' => 'required|integer',
            'option_id' => 'required|integer',
        ]);

        $questionId = $request->input('question_id');
        $optionId = $request->input('option_id');

        // Verify the question belongs to this quiz
        $question = $quiz->questions()->find($questionId);
        if (!$question) {
            return response()->json(['error' => 'invalid_question'], 400);
        }

        // Verify option belongs to this question
        $option = $question->options()->find($optionId);
        if (!$option) {
            return response()->json(['error' => 'invalid_option'], 400);
        }

        $isCorrect = $option->is_correct;

        // Upsert: replace any existing answer for this question + user
        QuizAnswer::updateOrCreate(
            [
                'quiz_question_id' => $questionId,
                'user_id' => auth()->id(),
            ],
            [
                'selected_option_id' => $optionId,
                'is_correct' => $isCorrect,
            ]
        );

        return response()->json(['saved' => true]);
    }

    // Student: Sync tab switch count without submitting
    public function syncSwitches(Request $request, Classroom $class, Quiz $quiz)
    {
        if ($quiz->class_id !== $class->id) abort(404);

        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();
        if (!$enrolled) abort(403);

        $submission = QuizSubmission::where('quiz_id', $quiz->id)
            ->where('user_id', auth()->id())
            ->first();

        if (!$submission || $submission->status !== 'in_progress') {
            return response()->json(['synced' => false]);
        }

        $reported = (int) $request->input('tab_switches', 0);
        $newCount = max($reported, $submission->tab_switches ?? 0);

        $submission->update(['tab_switches' => $newCount]);

        return response()->json(['synced' => true, 'tab_switches' => $newCount]);
    }

    // Student: Submit Quiz
    public function submit(Request $request, Classroom $class, Quiz $quiz)
    {
        if ($quiz->class_id !== $class->id) abort(404);

        // Students must be enrolled
        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();
        if (!$enrolled) abort(403);

        if ($quiz->due_date && now()->isAfter($quiz->due_date)) {
            return back()->with('error', 'This quiz is past the due date.');
        }

        $existing = QuizSubmission::where('quiz_id', $quiz->id)->where('user_id', auth()->id())->first();
        if ($existing && $existing->status === 'graded') {
            return back()->with('error', 'You already submitted this quiz.');
        }

        // If time limit expired, grade from saved answers instead of trusting the POST
        if ($quiz->time_limit && $existing && $existing->started_at
            && now()->greaterThan($existing->started_at->copy()->addMinutes($quiz->time_limit))) {

            $totalQuestions = $quiz->questions->count();
            $savedAnswers = QuizAnswer::where('user_id', auth()->id())
                ->whereIn('quiz_question_id', $quiz->questions->pluck('id'))
                ->get();
            $correctCount = $savedAnswers->where('is_correct', true)->count();
            $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * $quiz->points) : 0;

            QuizSubmission::updateOrCreate(
                ['quiz_id' => $quiz->id, 'user_id' => auth()->id()],
                [
                    'score' => $score,
                    'total_questions' => $totalQuestions,
                    'correct_answers' => $correctCount,
                    'status' => 'graded',
                    'submitted_at' => now(),
                    'tab_switches' => (int) $request->input('tab_switches', 0),
                ]
            );

            $passed = ($correctCount / max($totalQuestions, 1)) * 100 >= $quiz->passing_score;
            $emoji = $passed ? '🎉' : '📚';
            NotificationHelper::send(
                $class->instructor_id,
                '📥 ' . $class->name . ($class->section ? ' (Block ' . $class->section . ')' : ''),
                auth()->user()->name . ' submitted "' . $quiz->title . '" — ' . $correctCount . '/' . $totalQuestions . ' correct',
                route('instructor.classes.quizzes.submissions', [$class, $quiz])
            );
            NotificationHelper::send(auth()->id(), "{$emoji} Quiz Result: {$quiz->title}", "{$correctCount}/{$totalQuestions} correct · {$score} points", route('dashboard.classes.quizzes.show', [$class, $quiz]));

            return redirect()->route('dashboard.classes.quizzes.show', [$class, $quiz])
                ->with('success', 'Time expired — your saved answers were submitted automatically.');
        }

        $answers = $request->input('answers', []);
        $questions = $quiz->questions()->with('options')->get();
        $correctCount = 0;
        $totalQuestions = $questions->count();

        foreach ($answers as $questionId => $optionId) {
            $question = $questions->find($questionId);
            if (!$question) continue;

            $correctOption = $question->options->where('is_correct', true)->first();
            $isCorrect = $correctOption && $correctOption->id == $optionId;

            if ($isCorrect) $correctCount++;

            QuizAnswer::updateOrCreate(
                [
                    'quiz_question_id' => $questionId,
                    'user_id' => auth()->id(),
                ],
                [
                    'selected_option_id' => $optionId,
                    'is_correct' => $isCorrect,
                ]
            );
        }

        $score = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * $quiz->points) : 0;

        QuizSubmission::updateOrCreate(
            ['quiz_id' => $quiz->id, 'user_id' => auth()->id()],
            [
                'score' => $score,
                'total_questions' => $totalQuestions,
                'correct_answers' => $correctCount,
                'status' => 'graded',
                'submitted_at' => now(),
                'tab_switches' => (int) $request->input('tab_switches', 0),
            ]
        );

        $passed = ($correctCount / max($totalQuestions, 1)) * 100 >= $quiz->passing_score;
        $emoji = $passed ? '🎉' : '📚';
        NotificationHelper::send(
            $class->instructor_id,
            '📥 ' . $class->name . ($class->section ? ' (Block ' . $class->section . ')' : ''),
            auth()->user()->name . ' submitted "' . $quiz->title . '" — ' . $correctCount . '/' . $totalQuestions . ' correct',
            route('instructor.classes.quizzes.submissions', [$class, $quiz])
        );
        NotificationHelper::send(auth()->id(), "{$emoji} Quiz Result: {$quiz->title}", "{$correctCount}/{$totalQuestions} correct · {$score} points", route('dashboard.classes.quizzes.show', [$class, $quiz]));

        return redirect()->route('dashboard.classes.quizzes.show', [$class, $quiz])->with('success', 'Quiz submitted! ' . $correctCount . '/' . $totalQuestions . ' correct.');
    }

    // Student: List Quizzes
    public function studentIndex(Classroom $class)
    {
        // Instructor/Admin can view
        if (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) {
            $quizzes = $class->quizzes()->where('is_published', true)->latest()->paginate(10);
            return view('user.classes.quizzes.index', compact('class', 'quizzes'));
        }

        // Students must be enrolled
        $enrolled = $class->students()
            ->where('user_id', auth()->id())
            ->wherePivot('status', 'approved')
            ->exists();
        if (!$enrolled) abort(403);

        $quizzes = $class->quizzes()->where('is_published', true)->latest()->paginate(10);
        return view('user.classes.quizzes.index', compact('class', 'quizzes'));
    }

    // Instructor: View Submissions
    public function submissions(Classroom $class, Quiz $quiz)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        if ($quiz->class_id !== $class->id) abort(404);
        $submissions = $quiz->submissions()->where('status', 'graded')->with('user')->latest()->get();
        return view('instructor.classes.quizzes.submissions', compact('class', 'quiz', 'submissions'));
    }

    // Instructor: Import Form
    public function import(Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        $otherClasses = Classroom::where('instructor_id', auth()->id())
            ->where('id', '!=', $class->id)
            ->get();
        return view('instructor.classes.quizzes.import', compact('class', 'otherClasses'));
    }

    // Instructor: Copy Quizzes
    public function copyQuizzes(Request $request, Classroom $class)
    {
        if ($class->instructor_id !== auth()->id()) abort(403);
        
        $request->validate([
            'from_class' => 'required|exists:classes,id',
            'quizzes' => 'required|array',
        ]);

        $sourceClass = Classroom::where('instructor_id', auth()->id())
            ->findOrFail($request->from_class);

        $quizzes = $sourceClass->quizzes()->whereIn('id', $request->quizzes)->with('questions.options')->get();

        foreach ($quizzes as $quiz) {
            $newQuiz = Quiz::create([
                'class_id' => $class->id,
                'title' => $quiz->title,
                'description' => $quiz->description,
                'instructions' => $quiz->instructions,
                'points' => $quiz->points,
                'passing_score' => $quiz->passing_score,
                'due_date' => null,
                'time_limit' => $quiz->time_limit,
                'is_published' => false,
            ]);

            foreach ($quiz->questions as $question) {
                $newQuestion = $newQuiz->questions()->create([
                    'question' => $question->question,
                    'order' => $question->order,
                ]);

                foreach ($question->options as $option) {
                    $newQuestion->options()->create([
                        'option_text' => $option->option_text,
                        'is_correct' => $option->is_correct,
                        'order' => $option->order,
                    ]);
                }
            }
        }

        return redirect()->route('instructor.classes.quizzes.index', $class)
            ->with('success', count($quizzes) . ' quizzes imported!');
    }
}