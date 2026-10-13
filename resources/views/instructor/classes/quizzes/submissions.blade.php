@extends('layouts.app')

@section('title', 'Submissions - ' . $quiz->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.quizzes.index', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Quizzes
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Submissions · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4 break-words">
            {{ $quiz->title }}
        </h1>

        {{-- Meta line --}}
        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-users text-xs"></i>
                {{ $submissions->count() }} {{ Str::plural('submission', $submissions->count()) }}
            </span>
            <span class="text-gray-300 dark:text-gray-700">·</span>
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-star text-xs"></i>
                {{ $quiz->points }} points
            </span>
            <span class="text-gray-300 dark:text-gray-700">·</span>
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-check text-xs"></i>
                Pass: {{ $quiz->passing_score }}%
            </span>
        </div>
    </div>

    @if($submissions->count() > 0)
        <div class="space-y-3">
            @foreach($submissions as $submission)
                @php
                    $percent = ($submission->correct_answers / max($submission->total_questions, 1)) * 100;
                    $passed = $percent >= $quiz->passing_score;
                @endphp

                <article class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                    <div class="flex flex-wrap items-center justify-between gap-4">

        {{-- Student info --}}
        <div class="flex items-center gap-3 min-w-0 flex-1">
            <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 text-sm font-semibold text-white dark:text-gray-900">
                {{ strtoupper(substr($submission->user->name, 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $submission->user->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $submission->submitted_at->format('M d, Y · h:i A') }}</p>
                @if(($submission->tab_switches ?? 0) > 0)
                    <p class="text-[10px] font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400 mt-1 inline-flex items-center gap-1">
                        <i class="fas fa-triangle-exclamation text-[9px]"></i>
                        {{ $submission->tab_switches }} tab {{ Str::plural('switch', $submission->tab_switches) }}
                    </p>
                @endif
            </div>
        </div>

            {{-- Score + result --}}
                        <div class="flex items-center gap-4 flex-shrink-0">
                            <div class="text-right">
                                <p class="text-sm font-semibold tabular-nums {{ $passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $submission->score }}/{{ $quiz->points }}
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                                    {{ $submission->correct_answers }}/{{ $submission->total_questions }} correct
                                </p>
                            </div>
                            <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0
                                {{ $passed ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                ● {{ $passed ? 'Passed' : 'Failed' }}
                            </span>
                        </div>
                    </div>

                    {{-- Progress bar --}}
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-center justify-between gap-3 mb-2">
                            <span class="text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                Score
                            </span>
                            <span class="text-xs tabular-nums text-gray-500 dark:text-gray-400">
                                {{ round($percent) }}% (pass {{ $quiz->passing_score }}%)
                            </span>
                        </div>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden relative">
                            <div class="{{ $passed ? 'bg-emerald-500' : 'bg-red-500' }} h-1.5 rounded-full transition-all"
                                 style="width: {{ min(100, round($percent)) }}%"></div>
                            <div class="absolute top-0 bottom-0 w-px bg-gray-400 dark:bg-gray-600"
                                 style="left: {{ min(100, $quiz->passing_score) }}%"></div>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-users text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No submissions yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Wait for students to take this quiz.</p>
        </div>
    @endif
</div>
@endsection