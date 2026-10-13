@extends('layouts.app')

@section('title', $assessment->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? route('instructor.classes.assessments.index', $class) : route('dashboard.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        {{ (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? 'Back to Assessments' : 'Back to Classroom' }}
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Assessment
        </p>
        <div class="flex flex-wrap items-center gap-3 mb-4">
            <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white break-words">
                {{ $assessment->title }}
            </h1>

            {{-- Status badge --}}
            @if($submission && $submission->score !== null)
                <span class="text-[10px] font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                    ● Graded
                </span>
            @elseif($submission)
                <span class="text-[10px] font-medium uppercase tracking-wider text-blue-600 dark:text-blue-400">
                    ● Submitted
                </span>
            @elseif($assessment->due_date && now()->isAfter($assessment->due_date))
                <span class="text-[10px] font-medium uppercase tracking-wider text-red-600 dark:text-red-400">
                    ● Overdue
                </span>
            @else
                <span class="text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                    ● Pending
                </span>
            @endif
        </div>

        {{-- Meta line --}}
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-star text-xs"></i>
                {{ $assessment->points }} points
            </span>
            @if($assessment->due_date)
                <span class="text-gray-300 dark:text-gray-700">·</span>
                <span>Due {{ $assessment->due_date->format('M d, Y · h:i A') }}</span>
            @else
                <span class="text-gray-300 dark:text-gray-700">·</span>
                <span>No deadline</span>
            @endif
        </div>
    </div>

    {{-- Description + Instructions --}}
    @if($assessment->description || $assessment->instructions)
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-8">

            @if($assessment->description)
                <div @if($assessment->instructions) class="mb-8" @endif>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Description
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $assessment->description }}
                    </p>
                </div>
            @endif

            @if($assessment->instructions)
                <div @if($assessment->description) class="pt-8 border-t border-gray-100 dark:border-gray-800" @endif>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Instructions
                    </p>
                    <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                        {{ $assessment->instructions }}
                    </div>
                </div>
            @endif
        </section>
    @endif

    {{-- Submission area (students only) --}}
    @if(!auth()->user()->isInstructor() && !auth()->user()->isAdministrator())

        @if($submission)
            {{-- Already submitted --}}
            <section class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-1">
                            Your submission
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Submitted {{ $submission->submitted_at->diffForHumans() }}
                        </p>
                    </div>
                    @if($submission->score !== null)
                        <span class="text-xl font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums flex-shrink-0">
                            {{ $submission->score }}/{{ $assessment->points }}
                        </span>
                    @endif
                </div>

                <div class="p-6">
                    {{-- Submitted content --}}
                    <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono bg-gray-50 dark:bg-gray-800/50 border border-gray-100 dark:border-gray-800 rounded-lg p-4 leading-relaxed overflow-x-auto">{{ $submission->content }}</pre>

                    {{-- Feedback --}}
                    @if($submission->score !== null && $submission->feedback)
                        <div class="mt-6 border-l-2 border-emerald-500 dark:border-emerald-400 pl-4 py-1">
                            <p class="text-xs font-medium uppercase tracking-[0.15em] text-emerald-600 dark:text-emerald-400 mb-2">
                                Instructor feedback
                            </p>
                            <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                                {{ $submission->feedback }}
                            </p>
                        </div>
                    @endif

                    @if($submission->score === null)
                        <p class="mt-6 text-xs text-gray-500 dark:text-gray-400">
                            Awaiting grading by your instructor.
                        </p>
                    @endif
                </div>
            </section>

        @elseif(!$assessment->due_date || now()->lessThanOrEqualTo($assessment->due_date))
            {{-- Submit form --}}
            <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8">
                <div class="mb-6">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Submission
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Submit your work
                    </h2>
                </div>

                <form method="POST" action="{{ route('dashboard.classes.assessments.submit', [$class, $assessment]) }}">
                    @csrf
                    <textarea name="content" rows="8" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none font-mono"
                        placeholder="Write your answer or paste your code here...">{{ old('content') }}</textarea>

                    <div class="mt-6 flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                            <i class="fas fa-paper-plane text-xs"></i>
                            Submit
                        </button>
                    </div>
                </form>
            </section>

        @else
            {{-- Past due --}}
            <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
                <div class="flex items-start gap-4">
                    <i class="fas fa-exclamation-circle text-amber-500 text-lg flex-shrink-0 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white mb-1">
                            Past due date
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            This assessment is no longer accepting submissions.
                        </p>
                    </div>
                </div>
            </section>
        @endif

    @endif
</div>
@endsection