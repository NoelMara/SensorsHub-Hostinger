@extends('layouts.app')

@section('title', 'Leaderboard - ' . $class->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Class
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Leaderboard · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Leaderboard
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            {{ $leaderboard->count() }} {{ Str::plural('student', $leaderboard->count()) }} ranked by total points.
        </p>
    </div>

    @if($leaderboard->count() > 0)
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="pl-5 pr-3 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 w-16">Rank</th>
                            <th class="px-3 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Student</th>
                            <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Points</th>
                            <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Assessments</th>
                            <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Quizzes</th>
                            <th class="px-3 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Pending</th>
                            <th class="pl-3 pr-5 py-3 text-center text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($leaderboard as $index => $row)
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                                {{-- Rank --}}
                                <td class="pl-5 pr-3 py-4">
                                    @if($index == 0)
                                        <i class="fas fa-trophy text-amber-500 text-base"></i>
                                    @elseif($index == 1)
                                        <i class="fas fa-trophy text-gray-400 text-base"></i>
                                    @elseif($index == 2)
                                        <i class="fas fa-trophy text-orange-600 dark:text-orange-500 text-base"></i>
                                    @else
                                        <span class="text-sm font-semibold text-gray-400 dark:text-gray-600 tabular-nums">{{ $index + 1 }}</span>
                                    @endif
                                </td>

                                {{-- Student --}}
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-xs font-semibold text-white dark:text-gray-900">
                                            @if($row['student']->profile_image)
                                                <img src="{{ Str::startsWith($row['student']->profile_image, ['http://', 'https://']) ? $row['student']->profile_image : asset($row['student']->profile_image) }}"
                                                     alt="{{ $row['student']->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($row['student']->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $row['student']->name }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Points --}}
                                <td class="px-3 py-4 text-center">
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">
                                        {{ $row['total_points'] }}
                                    </span>
                                </td>

                                {{-- Assessments --}}
                                <td class="px-3 py-4 text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                                        {{ $row['graded_assessments'] }}/{{ $row['total_assessments'] }}
                                    </span>
                                </td>

                                {{-- Quizzes --}}
                                <td class="px-3 py-4 text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                                        {{ $row['graded_quizzes'] }}/{{ $row['total_quizzes'] }}
                                    </span>
                                </td>

                                {{-- Pending --}}
                                <td class="px-3 py-4 text-center">
                                    @if($row['pending'] > 0)
                                        <span class="text-xs font-medium tabular-nums text-amber-600 dark:text-amber-400">{{ $row['pending'] }}</span>
                                    @else
                                        <span class="text-xs text-gray-300 dark:text-gray-700 tabular-nums">0</span>
                                    @endif
                                </td>

                                {{-- Overdue --}}
                                <td class="pl-3 pr-5 py-4 text-center">
                                    @if($row['overdue'] > 0)
                                        <span class="text-xs font-medium tabular-nums text-red-600 dark:text-red-400">{{ $row['overdue'] }}</span>
                                    @else
                                        <span class="text-xs text-gray-300 dark:text-gray-700 tabular-nums">0</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-trophy text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No students yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Students will appear here once approved.</p>
        </div>
    @endif
</div>
@endsection