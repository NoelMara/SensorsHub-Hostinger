@extends('layouts.app')

@section('title', $class->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('dashboard.classes.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Classroom
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Class
        </p>
        <div class="flex flex-wrap items-baseline gap-3 mb-4">
            <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $class->name }}
            </h1>
            @if($class->section)
                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    · Block {{ $class->section }}
                </span>
            @endif
        </div>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            Taught by <span class="text-blue-600 dark:text-blue-400 font-medium">{{ $class->instructor->name }}</span>
        </p>
    </div>

    {{-- Progress summary — compact bar + stats inline --}}
    @if($totalPossible > 0)
        @php
            $overallPct = $totalPossible > 0 ? round(($totalPoints / $totalPossible) * 100) : 0;
        @endphp
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 mb-12">
            <div class="flex items-end justify-between gap-4 mb-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Progress
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        My progress
                    </h2>
                </div>
                <span class="text-3xl font-semibold tabular-nums
                    @if($overallPct >= 80) text-emerald-600 dark:text-emerald-400
                    @elseif($overallPct >= 50) text-amber-600 dark:text-amber-400
                    @else text-red-600 dark:text-red-400
                    @endif">{{ $overallPct }}%</span>
            </div>

            <div class="bg-gray-100 dark:bg-gray-800 rounded-full h-1.5 overflow-hidden mb-6">
                <div class="bg-gray-900 dark:bg-white h-1.5 rounded-full transition-all duration-500" style="width: {{ $overallPct }}%"></div>
            </div>

            <div class="grid grid-cols-3 gap-4 text-center sm:text-left">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Assessments</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $assessmentSubmissions->count() }}/{{ $assessments->count() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Quizzes</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $quizSubmissions->count() }}/{{ $quizzes->count() }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Points</p>
                    <p class="text-sm font-medium text-emerald-600 dark:text-emerald-400">{{ $totalPoints }}/{{ $totalPossible }}</p>
                </div>
            </div>
        </section>
    @endif

    {{-- Class code — subtle info card --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 mb-12 bg-gray-50 dark:bg-gray-900/50">
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Class code</p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white tracking-[0.25em] font-mono">{{ $class->code }}</p>
            </div>
            <button type="button"
                onclick="navigator.clipboard.writeText('{{ $class->code }}'); this.querySelector('span').textContent='Copied'; setTimeout(() => this.querySelector('span').textContent='Copy', 2000);"
                class="inline-flex items-center gap-1.5 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-copy text-[11px]"></i>
                <span>Copy</span>
            </button>
        </div>
        @if($class->description)
            <div class="mt-5 pt-5 border-t border-gray-100 dark:border-gray-800">
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Description</p>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $class->description }}</p>
            </div>
        @endif
    </section>

    {{-- Quick access — assessment / quiz / module / announcement cards --}}
    <section class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-12">
        <a href="{{ route('dashboard.classes.assessments.index', $class) }}"
           class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
            <i class="fas fa-tasks text-emerald-500 dark:text-emerald-400 text-sm mb-3 block"></i>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Assessments</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $assessments->count() }} available</p>
        </a>
        <a href="{{ route('dashboard.classes.quizzes.index', $class) }}"
           class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
            <i class="fas fa-question-circle text-blue-500 dark:text-blue-400 text-sm mb-3 block"></i>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Quizzes</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $quizzes->count() }} available</p>
        </a>
        <a href="{{ route('dashboard.classes.modules.index', $class) }}"
           class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
            <i class="fas fa-book-open text-amber-500 dark:text-amber-400 text-sm mb-3 block"></i>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Modules</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $class->modules()->where('is_published', true)->count() }} available</p>
        </a>
        <a href="{{ route('dashboard.classes.announcements.index', $class) }}"
           class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
            <i class="fas fa-bullhorn text-purple-500 dark:text-purple-400 text-sm mb-3 block"></i>
            <p class="text-sm font-medium text-gray-900 dark:text-white">Announcements</p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $class->announcements()->where('is_published', true)->count() }} posted</p>
        </a>
    </section>

    {{-- Announcements --}}
    @php $announcements = $class->announcements()->where('is_published', true)->latest()->take(3)->get(); @endphp
    @if($announcements->count() > 0)
        <section class="mb-12">
            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Announcements
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Latest from instructor
                    </h2>
                </div>
                <a href="{{ route('dashboard.classes.announcements.index', $class) }}"
                   class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition flex-shrink-0">
                    View all
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="space-y-3">
                @foreach($announcements as $announcement)
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 border-l-2 border-l-amber-500 dark:border-l-amber-400">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-1">{{ $announcement->title }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ Str::limit($announcement->content, 120) }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-600 mt-2">{{ $announcement->created_at->diffForHumans() }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Modules --}}
    @php $modules = $class->modules()->where('is_published', true)->orderBy('order')->take(3)->get(); @endphp
    @if($modules->count() > 0)
        <section class="mb-12">
            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Modules
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Learning materials
                    </h2>
                </div>
                <a href="{{ route('dashboard.classes.modules.index', $class) }}"
                   class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition flex-shrink-0">
                    View all
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="space-y-2">
                @foreach($modules as $module)
                    <a href="{{ route('dashboard.classes.modules.show', [$class, $module]) }}"
                       class="flex items-center justify-between gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-xs font-semibold text-gray-600 dark:text-gray-400">
                                {{ $module->order }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $module->title }}</h3>
                                @if($module->content)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ Str::limit($module->content, 80) }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-shrink-0">
                            @if($module->file_path)
                                <i class="fas fa-paperclip text-gray-400 text-xs"></i>
                            @endif
                            <i class="fas fa-arrow-right text-gray-300 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-0.5 transition-all text-xs"></i>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Assessments --}}
    @if($assessments->count() > 0)
        <section class="mb-12">
            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Assessments
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $assessmentPoints }}/{{ $totalAssessmentPoints }} pts earned
                    </h2>
                </div>
                <a href="{{ route('dashboard.classes.assessments.index', $class) }}"
                   class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition flex-shrink-0">
                    View all
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="space-y-2">
                @foreach($assessments as $assessment)
                    @php $sub = $assessmentSubmissions->get($assessment->id); @endphp
                    <a href="{{ route('dashboard.classes.assessments.show', [$class, $assessment]) }}"
                       class="flex items-center justify-between gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($sub && $sub->score !== null)
                                <i class="fas fa-check-circle text-emerald-500 text-sm flex-shrink-0"></i>
                            @elseif($sub)
                                <i class="fas fa-clock text-blue-500 text-sm flex-shrink-0"></i>
                            @else
                                <i class="fas fa-circle text-gray-300 dark:text-gray-700 text-xs flex-shrink-0"></i>
                            @endif
                            <span class="text-sm text-gray-900 dark:text-white truncate">{{ $assessment->title }}</span>
                        </div>
                        <span class="text-xs font-medium flex-shrink-0 tabular-nums
                            {{ $sub && $sub->score !== null ? 'text-emerald-600 dark:text-emerald-400' : ($sub ? 'text-blue-600 dark:text-blue-400' : 'text-gray-400 dark:text-gray-600') }}">
                            {{ $sub && $sub->score !== null ? $sub->score : '--' }}/{{ $assessment->points }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Quizzes --}}
    @if($quizzes->count() > 0)
        <section class="mb-12">
            <div class="flex items-end justify-between gap-4 mb-6">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Quizzes
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $quizPoints }}/{{ $totalQuizPoints }} pts earned
                    </h2>
                </div>
                <a href="{{ route('dashboard.classes.quizzes.index', $class) }}"
                   class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition flex-shrink-0">
                    View all
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            <div class="space-y-2">
                @foreach($quizzes as $quiz)
                    @php $sub = $quizSubmissions->get($quiz->id); @endphp
                    <a href="{{ route('dashboard.classes.quizzes.show', [$class, $quiz]) }}"
                       class="flex items-center justify-between gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($sub)
                                @php $pct = ($sub->correct_answers / max($sub->total_questions, 1)) * 100; @endphp
                                @if($pct >= $quiz->passing_score)
                                    <i class="fas fa-check-circle text-emerald-500 text-sm flex-shrink-0"></i>
                                @else
                                    <i class="fas fa-times-circle text-red-500 text-sm flex-shrink-0"></i>
                                @endif
                            @else
                                <i class="fas fa-circle text-gray-300 dark:text-gray-700 text-xs flex-shrink-0"></i>
                            @endif
                            <span class="text-sm text-gray-900 dark:text-white truncate">{{ $quiz->title }}</span>
                        </div>
                        <span class="text-xs font-medium flex-shrink-0 tabular-nums
                            {{ $sub ? ($sub->correct_answers / max($sub->total_questions, 1) * 100 >= $quiz->passing_score ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400') : 'text-gray-400 dark:text-gray-600' }}">
                            {{ $sub ? $sub->score : '--' }}/{{ $quiz->points }}
                        </span>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Resources --}}
    @php $resources = $class->resources()->get(); @endphp
    @if($resources->count() > 0)
        <section class="mb-12">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Resources
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Instructor recommended
                </h2>
            </div>

            <div class="space-y-2">
                @foreach($resources as $resource)
                    @php
                        $item = null;
                        $link = '#';
                        if ($resource->resource_type === 'sensor') {
                            $item = \App\Models\Sensor::find($resource->resource_id);
                            $link = $item ? route('sensors.show', $item->slug) : '#';
                        } elseif ($resource->resource_type === 'project') {
                            $item = \App\Models\Project::find($resource->resource_id);
                            $link = $item ? route('projects.show', $item->slug) : '#';
                        } elseif ($resource->resource_type === 'video') {
                            $item = \App\Models\Video::find($resource->resource_id);
                            $link = $item && $item->youtube_link ? $item->youtube_link : '#';
                        }
                    @endphp
                    @if($item)
                        <a href="{{ $link }}" target="_blank"
                           class="flex items-center gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">
                            <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0 w-14
                                {{ $resource->resource_type === 'sensor' ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                                {{ $resource->resource_type === 'project' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                {{ $resource->resource_type === 'video' ? 'text-red-600 dark:text-red-400' : '' }}">
                                {{ $resource->resource_type }}
                            </span>
                            <span class="text-sm text-gray-900 dark:text-white truncate flex-1">{{ $item->title ?? $item->name }}</span>
                            <i class="fas fa-arrow-up-right-from-square text-gray-400 text-xs flex-shrink-0 group-hover:text-gray-900 dark:group-hover:text-white transition"></i>
                        </a>
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    {{-- Back link bottom --}}
    <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800">
        <a href="{{ route('dashboard.classes.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to Classroom
        </a>
    </div>
</div>
@endsection