@extends('layouts.app')

@section('title', 'Resources - ' . $class->name)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Class
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Resources · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Manage resources
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Curate sensors, projects, and videos for your students.
        </p>
    </div>

    {{-- Current Resources --}}
    <section class="mb-12">
        <div class="mb-6">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Recommended
            </p>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Current resources ({{ $resources->count() }})
            </h2>
        </div>

        @if($resources->count() > 0)
            <div class="space-y-2">
                @foreach($resources as $resource)
                    @php
                        $item = null;
                        if ($resource->resource_type === 'sensor') $item = $sensors->find($resource->resource_id);
                        elseif ($resource->resource_type === 'project') $item = $projects->find($resource->resource_id);
                        elseif ($resource->resource_type === 'video') $item = $videos->find($resource->resource_id);
                    @endphp
                    @if($item)
                        <div class="flex items-center justify-between gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0 w-16
                                    {{ $resource->resource_type === 'sensor' ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                                    {{ $resource->resource_type === 'project' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                    {{ $resource->resource_type === 'video' ? 'text-red-600 dark:text-red-400' : '' }}">
                                    {{ $resource->resource_type }}
                                </span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->title ?? $item->name }}</span>
                            </div>
                            <form method="POST" action="{{ route('instructor.classes.resources.destroy', [$class, $resource]) }}" class="flex-shrink-0">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                                    <i class="fas fa-times text-[10px]"></i>
                                    Remove
                                </button>
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-book text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400">No resources added yet. Add from the sections below.</p>
            </div>
        @endif
    </section>

    {{-- Add Resources --}}
    <div>
        <div class="mb-6">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Add resources
            </p>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Pick from the library
            </h2>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Sensors --}}
            <section class="border border-gray-200 dark:border-gray-800 rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-microchip text-emerald-500 text-xs"></i>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Sensors</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto tabular-nums">{{ $sensors->count() }}</span>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-96 overflow-y-auto">
                    @forelse($sensors as $sensor)
                        <div class="flex items-start gap-3 p-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                @if($sensor->image)
                                    <img src="{{ Str::startsWith($sensor->image, ['http://', 'https://']) ? $sensor->image : asset($sensor->image) }}"
                                         alt="{{ $sensor->name }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <i class="fas fa-microchip text-gray-400 dark:text-gray-600 text-sm"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $sensor->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">{{ Str::limit($sensor->description, 50) }}</p>
                                <a href="{{ route('sensors.show', $sensor->slug) }}" target="_blank"
                                   class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 mt-1 transition">
                                    View
                                    <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                            <form method="POST" action="{{ route('instructor.classes.resources.store', $class) }}" class="flex-shrink-0">
                                @csrf
                                <input type="hidden" name="resource_type" value="sensor">
                                <input type="hidden" name="resource_id" value="{{ $sensor->id }}">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-emerald-500 hover:text-emerald-500 dark:hover:border-emerald-500 dark:hover:text-emerald-500 transition whitespace-nowrap"
                                    title="Add to class">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    Add
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-gray-500 dark:text-gray-400">
                            No sensors available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Projects --}}
            <section class="border border-gray-200 dark:border-gray-800 rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-project-diagram text-blue-500 text-xs"></i>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Projects</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto tabular-nums">{{ $projects->count() }}</span>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-96 overflow-y-auto">
                    @forelse($projects as $project)
                        <div class="flex items-start gap-3 p-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-project-diagram text-blue-500 text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $project->title }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $project->difficulty }}</span>
                                    @if($project->sensor)
                                        <span class="text-gray-300 dark:text-gray-700">·</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $project->sensor->name }}</span>
                                    @endif
                                </div>
                                <a href="{{ route('projects.show', $project->slug) }}" target="_blank"
                                   class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 mt-1 transition">
                                    View
                                    <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                </a>
                            </div>
                            <form method="POST" action="{{ route('instructor.classes.resources.store', $class) }}" class="flex-shrink-0">
                                @csrf
                                <input type="hidden" name="resource_type" value="project">
                                <input type="hidden" name="resource_id" value="{{ $project->id }}">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-blue-500 hover:text-blue-500 dark:hover:border-blue-500 dark:hover:text-blue-500 transition whitespace-nowrap"
                                    title="Add to class">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    Add
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-gray-500 dark:text-gray-400">
                            No projects available.
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- Videos --}}
            <section class="border border-gray-200 dark:border-gray-800 rounded-lg">
                <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-video text-red-500 text-xs"></i>
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white">Videos</h3>
                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-auto tabular-nums">{{ $videos->count() }}</span>
                    </div>
                </div>

                <div class="divide-y divide-gray-100 dark:divide-gray-800 max-h-96 overflow-y-auto">
                    @forelse($videos as $video)
                        <div class="flex items-start gap-3 p-4">
                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                @if($video->youtube_id)
                                    <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/mqdefault.jpg"
                                         alt="{{ $video->title }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    <i class="fas fa-video text-red-500 text-sm"></i>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $video->title }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $video->category ?? 'Uncategorized' }}</p>
                                @if($video->youtube_link)
                                    <a href="{{ $video->youtube_link }}" target="_blank"
                                       class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 mt-1 transition">
                                        Watch
                                        <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                                    </a>
                                @endif
                            </div>
                            <form method="POST" action="{{ route('instructor.classes.resources.store', $class) }}" class="flex-shrink-0">
                                @csrf
                                <input type="hidden" name="resource_type" value="video">
                                <input type="hidden" name="resource_id" value="{{ $video->id }}">
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition whitespace-nowrap"
                                    title="Add to class">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    Add
                                </button>
                            </form>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-gray-500 dark:text-gray-400">
                            No videos available.
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection