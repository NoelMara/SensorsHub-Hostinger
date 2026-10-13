@extends('layouts.app')

@section('title', $sensor->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Breadcrumb --}}
    <nav class="mb-8">
        <ol class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('home') }}" class="hover:text-gray-900 dark:hover:text-white transition">Home</a></li>
            <li class="text-gray-300 dark:text-gray-700">/</li>
            <li><a href="{{ route('sensors.index') }}" class="hover:text-gray-900 dark:hover:text-white transition">Sensors</a></li>
            <li class="text-gray-300 dark:text-gray-700">/</li>
            <li class="text-gray-900 dark:text-white">{{ $sensor->name }}</li>
        </ol>
    </nav>

    {{-- Hero: image + info --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 lg:gap-16 mb-16">

        {{-- Image --}}
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden aspect-square bg-gray-100 dark:bg-gray-900">
            @if($sensor->image)
                <img src="{{ Str::startsWith($sensor->image, ['http://', 'https://']) ? $sensor->image : (Str::startsWith($sensor->image, ['images/', '/images/']) ? asset($sensor->image) : asset('storage/' . $sensor->image)) }}" 
                    alt="{{ $sensor->name }}" 
                    class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <i class="fas fa-microchip text-6xl text-gray-400 dark:text-gray-600"></i>
                </div>
            @endif
        </div>

        {{-- Info --}}
        <div class="flex flex-col">

            {{-- Eyebrow --}}
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Sensor
            </p>

            {{-- Title --}}
            <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4 break-words">
                {{ $sensor->name }}
            </h1>

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-4 mb-6 text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                <span class="inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    Active
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-project-diagram"></i>
                    {{ $sensor->projects_count }} {{ Str::plural('Project', $sensor->projects_count) }}
                </span>
                <span class="inline-flex items-center gap-1.5">
                    <i class="fas fa-video"></i>
                    {{ $sensor->videos_count }} {{ Str::plural('Video', $sensor->videos_count) }}
                </span>
            </div>

            {{-- Description --}}
            <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed mb-8">
                {{ $sensor->description }}
            </p>

            {{-- Quick stats --}}
            <div class="grid grid-cols-2 gap-3 mb-8">
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                    <p class="text-[10px] font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-1.5">
                        Difficulty
                    </p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        Beginner → Advanced
                    </p>
                </div>
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                    <p class="text-[10px] font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-1.5">
                        Category
                    </p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                        Electronics
                    </p>
                </div>
            </div>

            {{-- CTA --}}
            <div class="flex flex-col sm:flex-row gap-3 mt-auto">
                <a href="#projects" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    View projects
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
                <a href="#tutorials" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-800 text-gray-900 dark:text-white font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Watch tutorials
                </a>
            </div>
        </div>
    </div>

    {{-- How It Works --}}
    @if($sensor->how_it_works)
        <section class="mb-16">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                How it works
            </p>
            <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-5">
                How this sensor works
            </h2>
            <p class="text-base text-gray-600 dark:text-gray-400 leading-relaxed max-w-3xl">
                {{ $sensor->how_it_works }}
            </p>
        </section>
    @endif

    {{-- Use Cases --}}
    @if($sensor->use_cases)
        <section class="mb-16">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Applications
            </p>
            <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-5">
                Common use cases
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                @foreach(explode(',', $sensor->use_cases) as $useCase)
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 flex items-center gap-3">
                        <i class="fas fa-check text-emerald-500 text-sm"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ trim($useCase) }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Components Needed --}}
    @if($sensor->components_needed)
        <section class="mb-16">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Required
            </p>
            <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-5">
                Components needed
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                @foreach(explode(',', $sensor->components_needed) as $component)
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 flex items-center gap-3">
                        <i class="fas fa-microchip text-gray-400 dark:text-gray-500 text-sm"></i>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ trim($component) }}</span>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Related Projects --}}
    @if($relatedProjects->count() > 0)
        <section id="projects" class="mb-16">
            <div class="mb-8">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                    Projects
                </p>
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">
                    Related projects
                </h2>
                <p class="text-base text-gray-600 dark:text-gray-400">
                    Build something with this sensor.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($relatedProjects as $project)
                    <a href="{{ route('projects.show', $project->slug) }}" 
                       class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 hover:border-gray-400 dark:hover:border-gray-600 transition group flex flex-col">
                        <div class="flex items-center justify-between gap-3 mb-3">
                            <span class="text-xs font-medium uppercase tracking-wide
                                @if($project->difficulty === 'Beginner') text-emerald-600 dark:text-emerald-400
                                @elseif($project->difficulty === 'Intermediate') text-amber-600 dark:text-amber-400
                                @else text-red-600 dark:text-red-400
                                @endif">
                                ● {{ $project->difficulty }}
                            </span>
                            @if($project->is_featured)
                                <span class="text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    Featured
                                </span>
                            @endif
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                            {{ $project->title }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 flex-1">
                            {{ Str::limit($project->description, 100) }}
                        </p>
                        <span class="text-sm font-medium text-gray-900 dark:text-white inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                            View project
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </a>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:gap-3 transition-all">
                    View all projects
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </section>
    @endif

    {{-- Related Videos --}}
    @if($relatedVideos->count() > 0)
        <section id="tutorials" class="mb-16">
            <div class="mb-8">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                    Tutorials
                </p>
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2">
                    Video tutorials
                </h2>
                <p class="text-base text-gray-600 dark:text-gray-400">
                    Watch, follow along, then build your own version.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($relatedVideos as $video)
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
                        <div class="relative aspect-video bg-gray-100 dark:bg-gray-900">
                            <iframe 
                                class="absolute inset-0 w-full h-full" 
                                src="https://www.youtube.com/embed/{{ $video->youtube_id }}" 
                                frameborder="0" 
                                loading="lazy"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                allowfullscreen>
                            </iframe>
                        </div>
                        <div class="p-4">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 mb-1.5">
                                {{ $video->title }}
                            </h3>
                            @if($video->description)
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit($video->description, 80) }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                <a href="{{ route('videos.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:gap-3 transition-all">
                    View all tutorials
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </section>
    @endif

    {{-- Back --}}
    <div class="pt-8 border-t border-gray-200 dark:border-gray-800">
        <a href="{{ route('sensors.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to sensors
        </a>
    </div>
</div>
@endsection