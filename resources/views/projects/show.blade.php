@extends('layouts.app')

@section('title', $project->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Breadcrumb --}}
    <nav class="mb-8">
        <ol class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('home') }}" class="hover:text-gray-900 dark:hover:text-white transition">Home</a></li>
            <li class="text-gray-300 dark:text-gray-700">/</li>
            <li><a href="{{ route('projects.index') }}" class="hover:text-gray-900 dark:hover:text-white transition">Projects</a></li>
            <li class="text-gray-300 dark:text-gray-700">/</li>
            <li class="text-gray-900 dark:text-white">{{ Str::limit($project->title, 40) }}</li>
        </ol>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">

        {{-- Main content --}}
        <div class="lg:col-span-2 space-y-12">

            {{-- Header --}}
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                    Project
                </p>

                {{-- Meta row --}}
                <div class="flex flex-wrap items-center gap-3 mb-4 text-xs uppercase tracking-wider">
                    <span class="
                        @if($project->difficulty === 'Beginner') text-emerald-600 dark:text-emerald-400
                        @elseif($project->difficulty === 'Intermediate') text-amber-600 dark:text-amber-400
                        @else text-red-600 dark:text-red-400
                        @endif">
                        ● {{ $project->difficulty }}
                    </span>
                    @if($project->is_featured)
                        <span class="text-gray-500 dark:text-gray-400">· Featured</span>
                    @endif
                    @if($project->sensor)
                        <span class="text-gray-400 dark:text-gray-600">·</span>
                        <a href="{{ route('sensors.show', $project->sensor->slug) }}" class="text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition inline-flex items-center gap-1.5">
                            <i class="fas fa-microchip"></i>
                            {{ $project->sensor->name }}
                        </a>
                    @endif
                </div>

                {{-- Title --}}
                <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-gray-900 dark:text-white break-words">
                    {{ $project->title }}
                </h1>
            </div>

            {{-- Overview --}}
            <section>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                    Overview
                </p>
                <div class="text-base text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                    {{ $project->description }}
                </div>
            </section>

            {{-- Instructions --}}
            @if($project->instructions)
                <section>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Instructions
                    </p>
                    <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white mb-5">
                        Step by step
                    </h2>
                    <div class="text-base text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                        {{ $project->instructions }}
                    </div>
                </section>
            @endif

            {{-- Code --}}
            @if($project->code)
                <section>
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                Code
                            </p>
                            <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                                Source code
                            </h2>
                        </div>
                        <button onclick="let btn=this; navigator.clipboard.writeText(document.getElementById('projectCode').textContent); btn.innerHTML='<i class=\'fas fa-check\'></i> Copied'; setTimeout(()=>{btn.innerHTML='<i class=\'fas fa-copy\'></i> Copy';},2000)" 
                                class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition rounded-lg text-xs font-medium">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>

                    <div class="bg-gray-950 border border-gray-800 rounded-lg overflow-hidden">
                        <div class="flex items-center justify-between px-4 py-2.5 border-b border-gray-800">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-gray-700"></span>
                                <span class="w-2 h-2 rounded-full bg-gray-700"></span>
                                <span class="w-2 h-2 rounded-full bg-gray-700"></span>
                            </div>
                            <span class="font-mono text-[10px] uppercase tracking-widest text-gray-600">
                                code
                            </span>
                        </div>
                        <pre id="projectCode" class="p-5 font-mono text-[13px] leading-relaxed text-gray-300 overflow-x-auto"><code>{{ $project->code }}</code></pre>
                    </div>
                </section>
            @endif
        </div>

        {{-- Sidebar --}}
        <aside class="lg:col-span-1 space-y-6">

            {{-- Actions --}}
            <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-4">
                    Actions
                </p>

                @auth
                    <form action="{{ route('dashboard.projects.save', $project) }}" method="POST" class="mb-3">
                        @csrf
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 {{ $isSaved ? 'border border-emerald-500 text-emerald-600 dark:text-emerald-400' : 'bg-gray-900 dark:bg-white text-white dark:text-gray-900' }} font-medium rounded-lg hover:{{ $isSaved ? 'bg-emerald-50 dark:bg-emerald-950/20' : 'bg-gray-800 dark:hover:bg-gray-100' }} transition">
                            <i class="fas {{ $isSaved ? 'fa-check' : 'fa-bookmark' }} text-sm"></i>
                            {{ $isSaved ? 'Saved' : 'Save project' }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition mb-3">
                        <i class="fas fa-bookmark text-sm"></i>
                        Save project
                    </a>
                    <p class="text-xs text-gray-500 dark:text-gray-400 text-center">
                        <a href="{{ route('login') }}" class="hover:underline">Sign in</a> to save projects
                    </p>
                @endauth

                <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800">
                    <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                        <i class="fas fa-arrow-left text-xs"></i>
                        Back to projects
                    </a>
                </div>
            </div>

            {{-- Components --}}
            @if($project->components_needed)
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Components
                    </p>
                    <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                        {{ $project->components_needed }}
                    </div>
                </div>
            @endif

            {{-- Related Sensor --}}
            @if($project->sensor)
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Related sensor
                    </p>
                    <a href="{{ route('sensors.show', $project->sensor->slug) }}" class="flex items-start gap-3 group">
                        <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-microchip text-gray-400 dark:text-gray-500 text-sm"></i>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 line-clamp-1">
                                {{ $project->sensor->name }}
                            </h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2">
                                {{ Str::limit($project->sensor->description, 60) }}
                            </p>
                        </div>
                    </a>
                    <div class="mt-4">
                        <a href="{{ route('sensors.show', $project->sensor->slug) }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:gap-3 transition-all">
                            Learn more
                            <i class="fas fa-arrow-right text-xs"></i>
                        </a>
                    </div>
                </div>
            @endif
        </aside>
    </div>

    {{-- Related Projects --}}
    @if($relatedProjects->count() > 0)
            <section class="mt-20 pt-16 border-t border-gray-200 dark:border-gray-800">
                <div class="mb-8">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        Related
                    </p>
                    <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                        More projects with this sensor
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach($relatedProjects as $relatedProject)
                        <a href="{{ route('projects.show', $relatedProject->slug) }}" 
                           class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 hover:border-gray-400 dark:hover:border-gray-600 transition group flex flex-col">
                            <span class="text-xs font-medium uppercase tracking-wide mb-3
                                @if($relatedProject->difficulty === 'Beginner') text-emerald-600 dark:text-emerald-400
                                @elseif($relatedProject->difficulty === 'Intermediate') text-amber-600 dark:text-amber-400
                                @else text-red-600 dark:text-red-400
                                @endif">
                                ● {{ $relatedProject->difficulty }}
                            </span>
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                                {{ $relatedProject->title }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 flex-1">
                                {{ Str::limit($relatedProject->description, 100) }}
                            </p>
                            <span class="text-sm font-medium text-gray-900 dark:text-white inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                                View project
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif
</div>
@endsection