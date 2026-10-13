@extends('layouts.app')

@section('title', 'Saved Projects')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Dashboard
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Saved projects
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Projects you've bookmarked for later. Remove any that no longer interest you.
        </p>
    </div>

    @if($savedProjects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($savedProjects as $saved)
                @php $project = $saved->project; @endphp
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 hover:border-gray-400 dark:hover:border-gray-600 transition flex flex-col group">

                    {{-- Top: badges --}}
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="text-xs font-medium uppercase tracking-wide
                            @if($project->difficulty === 'Beginner') text-emerald-600 dark:text-emerald-400
                            @elseif($project->difficulty === 'Intermediate') text-amber-600 dark:text-amber-400
                            @else text-red-600 dark:text-red-400
                            @endif">
                            ● {{ $project->difficulty }}
                        </span>
                        @if($project->is_featured)
                            <span class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                · Featured
                            </span>
                        @endif
                        <span class="text-xs text-gray-400 dark:text-gray-600 ml-auto">
                            {{ $project->sensor?->name ?? 'General' }}
                        </span>
                    </div>

                    {{-- Title --}}
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2">
                        {{ $project->title }}
                    </h3>

                    {{-- Description --}}
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 flex-1 mb-4">
                        {{ Str::limit($project->description, 150) }}
                    </p>

                    {{-- Components --}}
                    @if($project->components_needed)
                        <div class="border-t border-gray-100 dark:border-gray-800 pt-4 mb-4">
                            <p class="text-[10px] font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-1.5">
                                Components needed
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                {{ Str::limit($project->components_needed, 100) }}
                            </p>
                        </div>
                    @endif

                    {{-- Actions --}}
                    <div class="flex items-center justify-between gap-4 mt-auto pt-2">
                        <a href="{{ route('projects.show', $project->slug) }}"
                           class="text-sm font-medium text-gray-900 dark:text-white inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                            View project
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>

                        <form action="{{ route('dashboard.projects.save', $project) }}" method="POST">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition"
                                title="Remove from saved">
                                <i class="fas fa-bookmark-slash text-[11px]"></i>
                                Remove
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-bookmark text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No saved projects yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Start exploring and save projects you're interested in.
            </p>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                Browse projects
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    @endif

    {{-- Back to Dashboard --}}
    <div class="mt-12">
        <a href="{{ route('dashboard.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to Dashboard
        </a>
    </div>
</div>
@endsection