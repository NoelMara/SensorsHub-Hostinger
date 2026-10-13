@extends('layouts.app')

@section('title', $quiz->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? route('instructor.classes.quizzes.index', $class) : route('dashboard.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Quizzes
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Quiz
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4 break-words">
            {{ $quiz->title }}
        </h1>

        {{-- Meta line --}}
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-star text-xs"></i>
                {{ $quiz->points }} points
            </span>
            <span class="text-gray-300 dark:text-gray-700">·</span>
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-check text-xs"></i>
                Pass: {{ $quiz->passing_score }}%
            </span>
            <span class="text-gray-300 dark:text-gray-700">·</span>
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-question-circle text-xs"></i>
                {{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }}
            </span>
            @if($quiz->time_limit)
                <span class="text-gray-300 dark:text-gray-700">·</span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-clock text-xs"></i>
                    {{ $quiz->time_limit }} min limit
                </span>
            @endif
            @if($quiz->due_date)
                <span class="text-gray-300 dark:text-gray-700">·</span>
                <span>Due {{ $quiz->due_date->format('M d · h:i A') }}</span>
            @endif
        </div>
    </div>

    {{-- Description + Instructions --}}
    @if($quiz->description || $quiz->instructions)
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-8">
            @if($quiz->description)
                <div @if($quiz->instructions) class="mb-8" @endif>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Description
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $quiz->description }}
                    </p>
                </div>
            @endif

            @if($quiz->instructions)
                <div @if($quiz->description) class="pt-8 border-t border-gray-100 dark:border-gray-800" @endif>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Instructions
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $quiz->instructions }}
                    </p>
                </div>
            @endif
        </section>
    @endif

    {{-- ============================================================= --}}
    {{-- Already Submitted — results view --}}
    {{-- ============================================================= --}}
    @if($completedSubmission)
        @php $percent = ($completedSubmission->correct_answers / max($completedSubmission->total_questions, 1)) * 100; @endphp
        @php $passed = $percent >= $quiz->passing_score; @endphp

        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-8">
            <div class="text-center mb-8">
                @if($passed)
                    <i class="fas fa-trophy text-emerald-500 text-4xl mb-4 block"></i>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">
                        You passed!
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Submitted {{ $completedSubmission->submitted_at->diffForHumans() }}
                    </p>
                @else
                    <i class="fas fa-book text-red-500 text-4xl mb-4 block"></i>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">
                        Not quite
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Review the material and try again. Submitted {{ $completedSubmission->submitted_at->diffForHumans() }}
                    </p>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center">
                    <p class="text-3xl font-semibold text-gray-900 dark:text-white tabular-nums mb-1">
                        {{ $completedSubmission->score }}<span class="text-lg text-gray-400 dark:text-gray-600">/{{ $quiz->points }}</span>
                    </p>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Score
                    </p>
                </div>
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 text-center">
                    <p class="text-3xl font-semibold text-gray-900 dark:text-white tabular-nums mb-1">
                        {{ $completedSubmission->correct_answers }}<span class="text-lg text-gray-400 dark:text-gray-600">/{{ $completedSubmission->total_questions }}</span>
                    </p>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Correct
                    </p>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-800 pt-6">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        Your result
                    </span>
                    <span class="text-sm font-semibold tabular-nums {{ $passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                        {{ round($percent) }}% · Passing {{ $quiz->passing_score }}%
                    </span>
                </div>
                <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden relative">
                    <div class="{{ $passed ? 'bg-emerald-500' : 'bg-red-500' }} h-1.5 rounded-full transition-all"
                         style="width: {{ min(100, round($percent)) }}%"></div>
                    <div class="absolute top-0 bottom-0 w-px bg-gray-400 dark:bg-gray-600"
                         style="left: {{ min(100, $quiz->passing_score) }}%"></div>
                </div>
            </div>
        </section>

    {{-- ============================================================= --}}
    {{-- Not submitted + not overdue — student takes the quiz --}}
    {{-- ============================================================= --}}
    @elseif(!$quiz->due_date || now()->lessThanOrEqualTo($quiz->due_date))

        @if(!auth()->user()->isInstructor() && !auth()->user()->isAdministrator())

    {{-- Sticky countdown bar (only shows if timed) --}}
    @if($quiz->time_limit)
        <div id="countdownBar" class="sticky top-16 z-30 mb-6 border border-gray-200 dark:border-gray-800 rounded-lg p-4 bg-white dark:bg-gray-900 shadow-sm">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <i id="countdownIcon" class="fas fa-clock text-emerald-500 text-lg"></i>
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Time remaining</p>
                        <p id="countdownText" class="text-2xl font-semibold tabular-nums text-gray-900 dark:text-white">--:--</p>
                    </div>
                </div>
                <p id="countdownWarning" class="hidden text-xs font-medium text-amber-600 dark:text-amber-400 text-right max-w-xs">
                    Less than 1 minute remaining!
                </p>
            </div>
        </div>
    @endif

    {{-- Monitoring banner --}}
    <div class="mb-6 flex items-start gap-3 border border-amber-200 dark:border-amber-900 bg-amber-50 dark:bg-amber-950/30 rounded-lg p-4">
        <i class="fas fa-shield-halved text-amber-500 text-base shrink-0 mt-0.5"></i>
        <div class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
            <p class="font-semibold mb-0.5">This quiz is monitored.</p>
            <p>Do not switch tabs, copy questions, or use AI tools. Your activity is being recorded for review.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('dashboard.classes.quizzes.submit', [$class, $quiz]) }}" id="quizForm" onsubmit="return confirmQuizSubmit(this);">
        <input type="hidden" name="tab_switches" id="tabSwitchesInput" value="0">
        @csrf

        <div class="space-y-4">
            @foreach($quiz->questions as $index => $question)
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                    <div class="flex items-start gap-3 mb-4">
                        <span class="text-xs font-semibold text-gray-400 dark:text-gray-600 tabular-nums flex-shrink-0 mt-0.5">
                            Q{{ $index + 1 }}
                        </span>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $question->question }}
                        </h3>
                    </div>

                    <div class="space-y-2 ml-7">
                        @foreach($question->options as $option)
                            <label class="flex items-center gap-3 p-3 border border-gray-200 dark:border-gray-800 rounded-lg hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 cursor-pointer transition has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/50 dark:has-[:checked]:bg-emerald-950/20">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}"
                                data-question-id="{{ $question->id }}"
                                data-option-id="{{ $option->id }}"
                                {{ (isset($savedAnswers[$question->id]) && $savedAnswers[$question->id] == $option->id) ? 'checked' : '' }}
                                required
                                class="h-4 w-4 text-emerald-500 focus:ring-emerald-500 border-gray-300 dark:border-gray-700 flex-shrink-0">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-paper-plane text-xs"></i>
                Submit quiz
            </button>
        </div>
    </form>

    {{-- Honor-code modal --}}
    <div id="honorModal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-black/60 backdrop-blur-sm p-4">
        <div class="max-w-md w-full bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-2xl shadow-xl p-6 sm:p-8">
            <div class="flex items-start gap-3 mb-5">
                <i class="fas fa-shield-halved text-emerald-500 text-lg shrink-0 mt-0.5"></i>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Academic honesty</h3>
                    <p class="text-xs uppercase tracking-widest text-gray-500 dark:text-gray-400">Before you start</p>
                </div>
            </div>
            <ul class="text-sm text-gray-700 dark:text-gray-300 space-y-2 mb-6">
                <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] mt-2 text-gray-400"></i> Complete this quiz on your own.</li>
                <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] mt-2 text-gray-400"></i> No AI tools, no notes, no other tabs.</li>
                <li class="flex items-start gap-2"><i class="fas fa-circle text-[6px] mt-2 text-gray-400"></i> Tab switches and copy attempts are logged.</li>
                @if($quiz->time_limit)
                    <li class="flex items-start gap-2 text-amber-700 dark:text-amber-400"><i class="fas fa-clock text-[10px] mt-1"></i> <strong>This quiz has a {{ $quiz->time_limit }}-minute limit.</strong> The timer starts when you click below.</li>
                @endif
            </ul>
            <button type="button" id="honorAccept"
                class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                I understand, start quiz
            </button>
        </div>
    </div>

    <script>
    (function() {
        const form = document.getElementById('quizForm');
        const modal = document.getElementById('honorModal');
        const acceptBtn = document.getElementById('honorAccept');
        const tabInput = document.getElementById('tabSwitchesInput');
        const storageKey = 'quiz_tab_switches_' + form.id;
        const startUrl = "{{ route('dashboard.classes.quizzes.start', [$class, $quiz]) }}";
        const timeLimitMinutes = {{ $quiz->time_limit ?? 'null' }};
        const existingStartedAt = @json($submission && $submission->started_at ? $submission->started_at->toIso8601String() : null);
        let switches = parseInt(sessionStorage.getItem(storageKey) || 0, 10);
        let locked = false;
        let timerInterval = null;

        tabInput.value = switches;

        function syncSwitches() {
            fetch("{{ route('dashboard.classes.quizzes.sync-switches', [$class, $quiz]) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ tab_switches: switches })
            }).catch(() => {});
        }

        // Show honor modal on load
        modal.classList.remove('hidden');
        modal.classList.add('flex');

        // If the quiz was already started, sync immediately 
        if (existingStartedAt) {
            syncSwitches();
        }

        // Attach autosave to every radio button (runs on page load)
        form.querySelectorAll('input[type="radio"][data-question-id]').forEach(radio => {
            radio.addEventListener('change', async function() {
                if (!locked) return;
                try {
                    await fetch("{{ route('dashboard.classes.quizzes.save-answer', [$class, $quiz]) }}", {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            question_id: this.dataset.questionId,
                            option_id: this.dataset.optionId,
                        })
                    });
                } catch (e) {
                    console.error('Autosave failed', e);
                }
            });
        });

        // Honor modal accept handler
        acceptBtn.addEventListener('click', async () => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            locked = true;

            // Start the timer if this quiz is timed
            if (timeLimitMinutes) {
                if (existingStartedAt) {
                    startCountdown(new Date(existingStartedAt));
                } else {
                    try {
                        const res = await fetch(startUrl, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await res.json();
                        if (data.started_at) {
                            startCountdown(new Date(data.started_at));
                        }
                    } catch (e) {
                        console.error('Failed to start quiz timer', e);
                    }
                }
            }
        });

        function startCountdown(startedAt) {
            const endTime = new Date(startedAt.getTime() + timeLimitMinutes * 60 * 1000);

            function tick() {
                const now = new Date();
                const remaining = Math.max(0, Math.floor((endTime - now) / 1000));
                const mins = Math.floor(remaining / 60);
                const secs = remaining % 60;

                document.getElementById('countdownText').textContent =
                    String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');

                if (remaining <= 60 && remaining > 0) {
                    document.getElementById('countdownWarning').classList.remove('hidden');
                    document.getElementById('countdownIcon').classList.remove('text-emerald-500');
                    document.getElementById('countdownIcon').classList.add('text-amber-500');
                }

                if (remaining === 0) {
                    clearInterval(timerInterval);
                    autoSubmit();
                }
            }

            tick();
            timerInterval = setInterval(tick, 1000);
        }

        function autoSubmit() {
            const btn = form.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'pointer-events-none');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Time up, submitting...';
            }
            HTMLFormElement.prototype.submit.call(form);
        }

        // Count tab switches
        document.addEventListener('visibilitychange', () => {
            if (document.hidden && locked) {
                switches++;
                tabInput.value = switches;
                sessionStorage.setItem(storageKey, switches);
                syncSwitches();
            }
        });
        
        // Block copy/paste/cut/right-click on the form
        ['copy', 'cut', 'paste', 'contextmenu'].forEach(evt => {
            form.addEventListener(evt, e => {
                e.preventDefault();
                return false;
            });
        });

        // Submit handler
        window.confirmQuizSubmit = function(f) {
            if (switches >= 3) {
                if (!confirm(`You switched tabs ${switches} times during this quiz. This has been logged. Submit anyway?`)) {
                    return false;
                }
            }
            sessionStorage.removeItem(storageKey);
            const btn = f.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-70', 'pointer-events-none');
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Submitting...</span>';
            }
            return true;
        };
    })();
    </script>

        @else
            {{-- Instructor preview --}}
            <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
                <div class="mb-6">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Preview
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Instructor view · correct answers shown
                    </h2>
                </div>

                <div class="space-y-4">
                    @foreach($quiz->questions as $index => $question)
                        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                            <div class="flex items-start gap-3 mb-4">
                                <span class="text-xs font-semibold text-gray-400 dark:text-gray-600 tabular-nums flex-shrink-0 mt-0.5">
                                    Q{{ $index + 1 }}
                                </span>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $question->question }}
                                </h3>
                            </div>

                            <div class="space-y-2 ml-7">
                                @foreach($question->options as $option)
                                    <div class="flex items-center gap-3 p-3 border rounded-lg
                                        {{ $option->is_correct ? 'border-emerald-500 dark:border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20' : 'border-gray-200 dark:border-gray-800' }}">
                                        @if($option->is_correct)
                                            <i class="fas fa-check-circle text-emerald-500 text-sm flex-shrink-0"></i>
                                        @else
                                            <i class="fas fa-circle text-gray-300 dark:text-gray-700 text-[8px] flex-shrink-0"></i>
                                        @endif
                                        <span class="text-sm {{ $option->is_correct ? 'text-gray-900 dark:text-white font-medium' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $option->option_text }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    {{-- Past Due --}}
    @else
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
            <div class="flex items-start gap-4">
                <i class="fas fa-exclamation-circle text-amber-500 text-lg flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                        Past due date
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        This quiz is no longer accepting submissions.
                    </p>
                </div>
            </div>
        </section>
    @endif
</div>
@endsection