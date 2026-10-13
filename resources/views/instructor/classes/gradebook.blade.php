@extends('layouts.app')

@section('title', 'Gradebook - ' . $class->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Class
    </a>

    {{-- Header --}}
    <div class="mb-12 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-6">
        <div>
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Gradebook · {{ $class->name }}
            </p>
            <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
                Gradebook
            </h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
                All scores for {{ $students->count() }} {{ Str::plural('student', $students->count()) }}.
            </p>
        </div>
        <a href="{{ route('instructor.classes.gradebook.export', $class) }}"
          class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm flex-shrink-0">
            <i class="fas fa-download text-xs"></i>
            Download CSV
        </a>
    </div>

    @if($students->count() > 0 && ($assessments->count() > 0 || $quizzes->count() > 0))

        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                            <th class="pl-5 pr-3 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 sticky left-0 bg-gray-50 dark:bg-gray-900/50 z-10 min-w-[180px]">
                                Student
                            </th>

                            {{-- Assessment columns --}}
                            @foreach($assessments as $assessment)
                                <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 min-w-[90px]"
                                    title="{{ $assessment->title }} ({{ $assessment->points }} pts)">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="text-blue-600 dark:text-blue-400 text-[9px]">Assessment</span>
                                        <span class="truncate max-w-[90px]">{{ Str::limit($assessment->title, 14) }}</span>
                                    </div>
                                </th>
                            @endforeach

                            {{-- Assessment Total column --}}
                            <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-blue-600 dark:text-blue-400 min-w-[100px] border-l-2 border-gray-200 dark:border-gray-800 bg-blue-50/50 dark:bg-blue-950/10">
                                Assess Total
                            </th>

                            {{-- Quiz columns --}}
                            @foreach($quizzes as $quiz)
                                <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 min-w-[90px] border-l-2 border-gray-200 dark:border-gray-800"
                                    title="{{ $quiz->title }} ({{ $quiz->points }} pts)">
                                    <div class="flex flex-col items-center gap-0.5">
                                        <span class="text-purple-600 dark:text-purple-400 text-[9px]">Quiz</span>
                                        <span class="truncate max-w-[90px]">{{ Str::limit($quiz->title, 14) }}</span>
                                    </div>
                                </th>
                            @endforeach

                            {{-- Quiz Total column --}}
                            <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-purple-600 dark:text-purple-400 min-w-[100px] border-l-2 border-gray-200 dark:border-gray-800 bg-purple-50/50 dark:bg-purple-950/10">
                                Quiz Total
                            </th>

                            {{-- Grand Total column --}}
                            <th class="pl-3 pr-5 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-900 dark:text-white min-w-[100px] border-l-2 border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-800">
                                Grand Total
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @php
                                $studentAssessSubs = $assessmentSubs->get($student->id, collect())->keyBy('assessment_id');
                                $studentQuizSubs = $quizSubs->get($student->id, collect())->keyBy('quiz_id');

                                $assessTotal = $studentAssessSubs->sum('score');
                                $quizTotal = $studentQuizSubs->sum('score');
                                $grandTotal = $assessTotal + $quizTotal;
                            @endphp

                            <tr class="border-b border-gray-100 dark:border-gray-800/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                <td class="pl-5 pr-3 py-3 sticky left-0 bg-white dark:bg-gray-900 z-10">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-xs font-semibold text-white dark:text-gray-900">
                                            @if($student->profile_image)
                                                <img src="{{ Str::startsWith($student->profile_image, ['http://', 'https://']) ? $student->profile_image : asset($student->profile_image) }}"
                                                     alt="{{ $student->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($student->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white truncate max-w-[120px]">{{ $student->name }}</span>
                                    </div>
                                </td>

                                {{-- Assessment cells --}}
                                @foreach($assessments as $assessment)
                                    @php
                                        $sub = $studentAssessSubs->get($assessment->id);
                                        $score = $sub?->score;
                                        $percent = ($score !== null && $assessment->points > 0) ? round(($score / $assessment->points) * 100) : null;
                                    @endphp
                                    <td class="px-3 py-3 text-center">
                                        @if($score !== null)
                                            <span class="text-sm font-semibold tabular-nums {{ $percent >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($percent >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                                {{ $score }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300 dark:text-gray-700">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Assessment Total cell --}}
                                <td class="px-3 py-3 text-center border-l-2 border-gray-200 dark:border-gray-800 bg-blue-50/30 dark:bg-blue-950/5">
                                    <span class="text-sm font-semibold text-blue-700 dark:text-blue-400 tabular-nums">
                                        {{ $assessTotal }}
                                    </span>
                                </td>

                                {{-- Quiz cells --}}
                                @foreach($quizzes as $quiz)
                                    @php
                                        $sub = $studentQuizSubs->get($quiz->id);
                                        $score = $sub?->score;
                                        $percent = ($score !== null && $quiz->points > 0) ? round(($score / $quiz->points) * 100) : null;
                                    @endphp
                                    <td class="px-3 py-3 text-center border-l-2 border-gray-100 dark:border-gray-800/50 first:border-l-2 first:border-gray-200 dark:first:border-gray-800">
                                        @if($score !== null)
                                            <span class="text-sm font-semibold tabular-nums {{ $percent >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($percent >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                                {{ $score }}
                                            </span>
                                        @else
                                            <span class="text-xs text-gray-300 dark:text-gray-700">—</span>
                                        @endif
                                    </td>
                                @endforeach

                                {{-- Quiz Total cell --}}
                                <td class="px-3 py-3 text-center border-l-2 border-gray-200 dark:border-gray-800 bg-purple-50/30 dark:bg-purple-950/5">
                                    <span class="text-sm font-semibold text-purple-700 dark:text-purple-400 tabular-nums">
                                        {{ $quizTotal }}
                                    </span>
                                </td>

                                {{-- Grand Total cell --}}
                                <td class="pl-3 pr-5 py-3 text-center border-l-2 border-gray-200 dark:border-gray-800 bg-gray-100 dark:bg-gray-800">
                                    <span class="text-sm font-bold text-gray-900 dark:text-white tabular-nums">
                                        {{ $grandTotal }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    @elseif($students->count() === 0)
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-users text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No students yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Approved students will appear here.</p>
        </div>
    @else
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-table text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No assessments or quizzes yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Publish an assessment or quiz to start tracking scores.</p>
        </div>
    @endif
</div>
@endsection