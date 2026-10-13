@extends('layouts.app')

@section('title', 'Assessments - ' . $class->name)

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
            Assessments
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            {{ $class->name }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            {{ $assessments->total() }} {{ Str::plural('assessment', $assessments->total()) }} available
        </p>
    </div>

    @if($assessments->count() > 0)
        <div class="space-y-3">
            @foreach($assessments as $assessment)
                @php $sub = $assessment->submissions()->where('user_id', auth()->id())->first(); @endphp
                @php $isOverdue = $assessment->due_date && now()->isAfter($assessment->due_date); @endphp
                <a href="{{ route('dashboard.classes.assessments.show', [$class, $assessment]) }}"
                   class="flex items-center gap-4 border border-gray-200 dark:border-gray-800 rounded-lg p-4 sm:p-5 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">

                    {{-- Status icon --}}
                    <div class="flex-shrink-0">
                        @if($sub && $sub->score !== null)
                            <i class="fas fa-check-circle text-emerald-500 text-lg"></i>
                        @elseif($sub)
                            <i class="fas fa-clock text-blue-500 text-lg"></i>
                        @elseif($isOverdue)
                            <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                        @else
                            <i class="fas fa-circle text-gray-300 dark:text-gray-700 text-xs"></i>
                        @endif
                    </div>

                    {{-- Title + meta --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $assessment->title }}
                        </h3>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1">
                                <i class="fas fa-star text-[10px]"></i>
                                {{ $assessment->points }} pts
                            </span>
                            @if($assessment->due_date)
                                <span>Due {{ $assessment->due_date->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>

                    {{-- Score / status text --}}
                    @if($sub && $sub->score !== null)
                        <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 flex-shrink-0 tabular-nums">
                            {{ $sub->score }}/{{ $assessment->points }}
                        </span>
                    @elseif($sub)
                        <span class="text-xs font-medium uppercase tracking-wider text-blue-600 dark:text-blue-400 flex-shrink-0">
                            Submitted
                        </span>
                    @elseif($isOverdue)
                        <span class="text-xs font-medium uppercase tracking-wider text-red-600 dark:text-red-400 flex-shrink-0">
                            Overdue
                        </span>
                    @else
                        <span class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 flex-shrink-0">
                            Pending
                        </span>
                    @endif

                    {{-- Arrow --}}
                    <i class="fas fa-arrow-right text-gray-300 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-0.5 transition-all flex-shrink-0 text-xs"></i>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-tasks text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No assessments yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Check back later.</p>
        </div>
    @endif

    @if($assessments->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $assessments->links() }}
        </div>
    @endif
</div>
@endsection