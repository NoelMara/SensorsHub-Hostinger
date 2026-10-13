@extends('layouts.app')

@section('title', 'Projects')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Projects
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Build these next
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Step-by-step walkthroughs using real components and real code.
        </p>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('projects.index') }}" class="mb-12">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-4 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search projects..." 
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition">
            </div>
            <div class="sm:col-span-3">
                <select name="difficulty" class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition">
                    <option value="">All Difficulties</option>
                    <option value="Beginner" {{ request('difficulty') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                    <option value="Intermediate" {{ request('difficulty') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Advanced" {{ request('difficulty') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                </select>
            </div>
            <div class="sm:col-span-3">
                <select name="sensor_id" class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition">
                    <option value="">All Sensors</option>
                    @foreach($sensors as $sensor)
                        <option value="{{ $sensor->id }}" {{ request('sensor_id') == $sensor->id ? 'selected' : '' }}>{{ $sensor->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    Search
                </button>
            </div>
        </div>
        @if(request('search') || request('difficulty') || request('sensor_id'))
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-3 flex-wrap">
                <span>
                    @if(request('search'))
                        Results for "<span class="font-medium text-gray-900 dark:text-white">{{ request('search') }}</span>"
                    @endif
                    @if(request('search') && (request('difficulty') || request('sensor_id'))) · @endif
                    @if(request('difficulty'))
                        Difficulty: <span class="font-medium text-gray-900 dark:text-white">{{ request('difficulty') }}</span>
                    @endif
                    @if(request('difficulty') && request('sensor_id')) · @endif
                    @if(request('sensor_id'))
                        Sensor: <span class="font-medium text-gray-900 dark:text-white">{{ $sensors->firstWhere('id', request('sensor_id'))?->name ?? 'Unknown' }}</span>
                    @endif
                </span>
                <a href="{{ route('projects.index') }}" class="text-gray-900 dark:text-white hover:underline">
                    Clear
                </a>
            </p>
        @endif
    </form>

    {{-- Projects Grid --}}
    @if($projects->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($projects as $project)
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

                        @auth
                            @php $isSaved = in_array($project->id, $savedProjectIds ?? []); @endphp
                            <form action="{{ route('dashboard.projects.save', $project) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="inline-flex items-center gap-1.5 px-3 h-9 rounded-lg border text-xs font-medium transition
                                        {{ $isSaved 
                                            ? 'border-gray-900 dark:border-white bg-gray-900 dark:bg-white text-white dark:text-gray-900' 
                                            : 'border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white' }}"
                                    title="{{ $isSaved ? 'Remove from saved' : 'Save project' }}">
                                    <i class="{{ $isSaved ? 'fas' : 'far' }} fa-bookmark text-[11px]"></i>
                                    {{ $isSaved ? 'Saved' : 'Save' }}
                                </button>
                            </form>
                        @endauth
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($projects->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $projects->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-project-diagram text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No projects found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                @if(request('search') || request('difficulty') || request('sensor_id'))
                    Try different filters.
                @else
                    Check back later for new projects.
                @endif
            </p>
            @if(request('search') || request('difficulty') || request('sensor_id'))
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    Clear filters
                </a>
            @endif
        </div>
    @endif

    {{-- CTA --}}
    <div class="mt-20 bg-gray-900 dark:bg-black rounded-2xl p-8 sm:p-12 text-center">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Contribute
        </p>
        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white mb-4">
            Have a project idea?
        </h2>
        <p class="text-gray-400 mb-8 max-w-xl mx-auto">
            Share your project suggestion with the SensorsHub community.
        </p>
        @auth
            <a href="{{ route('dashboard.suggestions') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 font-medium rounded-lg hover:bg-gray-100 transition">
                Submit Your Idea
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        @else
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 font-medium rounded-lg hover:bg-gray-100 transition">
                Join to Share Ideas
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        @endauth
    </div>
</div>
@endsection