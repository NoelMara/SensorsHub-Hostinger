@extends('layouts.app')

@section('title', 'Home')

@section('content')
{{-- ==================== GUEST HOME ==================== --}}

<style>
    @keyframes cursor-blink {
        0%, 49% { opacity: 1; }
        50%, 100% { opacity: 0; }
    }
    .cursor-blink {
        display: inline-block;
        width: 0.55ch;
        animation: cursor-blink 1.1s step-end infinite;
    }
    .terminal-line {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
    }

    /* Reveal on scroll */
    .reveal {
        opacity: 0;
        transform: translateY(20px);
        transition: opacity 0.7s cubic-bezier(0.22, 1, 0.36, 1),
                    transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
    }

    .reveal.is-visible {
        opacity: 1;
        transform: translateY(0);
    }

    @media (prefers-reduced-motion: reduce) {
        .reveal {
            opacity: 1;
            transform: none;
            transition: none;
        }
    }
</style>

{{-- Hero --}}
<section class="bg-white dark:bg-gray-900">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 sm:pt-28 pb-16 sm:pb-20">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-6">
            SensorsHub
        </p>

        <h1 class="text-5xl sm:text-6xl lg:text-7xl font-semibold text-gray-900 dark:text-white leading-[1.05] tracking-tight mb-8 max-w-3xl">
            Learn sensors.<br>
            Build real circuits.
        </h1>

        <p class="text-lg sm:text-xl text-gray-600 dark:text-gray-400 mb-12 max-w-2xl leading-relaxed">
            A hands-on workspace for students and makers. Study components, follow real projects, and test circuits in a live simulator.
        </p>

        <div class="flex flex-col sm:flex-row sm:items-center gap-6 mb-16">
            <a href="{{ route('sensors.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                Browse Sensors
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-gray-900 dark:text-white font-medium hover:text-primary transition group">
                View Projects
                <i class="fas fa-arrow-right text-xs group-hover:translate-x-0.5 transition-transform"></i>
            </a>
        </div>

        <p class="terminal-line text-sm text-gray-500 dark:text-gray-500 mb-20">
            <span class="text-secondary">$</span> start with a sensor, build something real<span class="cursor-blink">▌</span>
        </p>

        {{-- Code card --}}
        <div class="max-w-2xl">
            <div class="bg-gray-950 border border-gray-800 rounded-lg overflow-hidden shadow-2xl">
                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-800">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-700"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-700"></span>
                        <span class="w-2.5 h-2.5 rounded-full bg-gray-700"></span>
                    </div>
                    <span class="terminal-line text-[10px] uppercase tracking-widest text-gray-600">main.py — pico</span>
                </div>

                <div class="p-6 terminal-line text-[13px] leading-[1.7] space-y-0.5">
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">1</span>
                        <span class="whitespace-pre"><span class="text-pink-400">from</span> <span class="text-gray-300">machine</span> <span class="text-pink-400">import</span> <span class="text-gray-300">ADC, Pin</span></span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">2</span>
                        <span class="whitespace-pre"><span class="text-pink-400">import</span> <span class="text-gray-300">time</span></span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">3</span>
                        <span class="whitespace-pre"> </span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">4</span>
                        <span class="whitespace-pre"><span class="text-gray-300">sensor = ADC(Pin(</span><span class="text-amber-400">26</span><span class="text-gray-300">))</span></span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">5</span>
                        <span class="whitespace-pre"> </span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">6</span>
                        <span class="whitespace-pre"><span class="text-purple-400">while</span> <span class="text-amber-400">True</span><span class="text-gray-300">:</span></span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">7</span>
                        <span class="whitespace-pre"><span class="text-gray-300">    value = sensor.</span><span class="text-blue-400">read_u16</span><span class="text-gray-300">()</span></span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">8</span>
                        <span class="whitespace-pre"><span class="text-gray-300">    </span><span class="text-blue-400">print</span><span class="text-gray-300">(value)</span></span>
                    </div>
                    <div class="flex">
                        <span class="text-gray-700 select-none w-6 text-right mr-5 shrink-0">9</span>
                        <span class="whitespace-pre"><span class="text-gray-300">    time.</span><span class="text-blue-400">sleep</span><span class="text-gray-300">(</span><span class="text-amber-400">1</span><span class="text-gray-300">)</span></span>
                    </div>
                </div>

                <div class="flex items-center justify-between px-4 py-2.5 border-t border-gray-800 terminal-line text-[10px] uppercase tracking-widest">
                    <span class="text-gray-600">Raspberry Pi Pico</span>
                    <span class="text-gray-500 flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        Running
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Featured Sensors --}}
<section class="reveal bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="mb-12">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">01 — Sensors</p>
            <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 dark:text-white tracking-tight mb-3">Explore the components</h2>
            <p class="text-base text-gray-600 dark:text-gray-400 max-w-xl">Every project starts with a sensor. Learn what each one does and how to use it.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredSensors as $sensor)
            <a href="{{ route('sensors.show', $sensor->slug) }}" class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:border-gray-400 dark:hover:border-gray-600 transition flex flex-col group">
                <div class="aspect-[16/10] bg-gray-100 dark:bg-gray-900 overflow-hidden">
                    @if($sensor->image)
                        <img src="{{ Str::startsWith($sensor->image, ['http://', 'https://']) ? $sensor->image : (Str::startsWith($sensor->image, ['images/', '/images/']) ? asset($sensor->image) : asset('storage/' . $sensor->image)) }}" alt="{{ $sensor->name }}" class="w-full h-full object-cover" loading="lazy">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-microchip text-3xl text-gray-400 dark:text-gray-600"></i>
                        </div>
                    @endif
                </div>
                <div class="p-5 flex-1 flex flex-col">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">{{ $sensor->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex-1 line-clamp-2 mb-4">{{ Str::limit($sensor->description, 90) }}</p>
                    <span class="text-sm font-medium text-gray-900 dark:text-white inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                        Learn more <i class="fas fa-arrow-right text-[10px]"></i>
                    </span>
                </div>
            </a>
            @endforeach
        </div>

        <div class="mt-12">
            <a href="{{ route('sensors.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:gap-3 transition-all">
                View all sensors
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>

{{-- Featured Projects --}}
<section class="reveal bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="mb-12">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">02 — Projects</p>
            <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 dark:text-white tracking-tight mb-3">Build these next</h2>
            <p class="text-base text-gray-600 dark:text-gray-400 max-w-xl">Step-by-step walkthroughs using real components and real code.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($featuredProjects as $project)
            <a href="{{ route('projects.show', $project->slug) }}" class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg p-6 hover:border-gray-400 dark:hover:border-gray-600 transition flex flex-col group">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                        {{ $project->difficulty }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-600">·</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $project->sensor?->name ?? 'General' }}
                    </span>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 line-clamp-2">{{ $project->title }}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 flex-1 line-clamp-2 mb-4">{{ Str::limit($project->description, 120) }}</p>
                <span class="text-sm font-medium text-gray-900 dark:text-white inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                    View project <i class="fas fa-arrow-right text-[10px]"></i>
                </span>
            </a>
            @endforeach
        </div>

        <div class="mt-12">
            <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:gap-3 transition-all">
                View all projects
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>

{{-- Latest Tutorials --}}
@if($latestVideos->count() > 0)
<section class="reveal bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="mb-12">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">03 — Tutorials</p>
            <h2 class="text-3xl sm:text-4xl font-semibold text-gray-900 dark:text-white tracking-tight mb-3">Watch & build</h2>
            <p class="text-base text-gray-600 dark:text-gray-400 max-w-xl">Follow along, then make it your own.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($latestVideos as $video)
            <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:border-gray-400 dark:hover:border-gray-600 transition flex flex-col group">
                <button type="button" onclick="openVideoModal('{{ $video->youtube_id }}')" class="text-left block w-full">
                    <div class="relative aspect-video bg-gray-100 dark:bg-gray-900 overflow-hidden">
                        @if($video->youtube_id)
                            <img src="https://img.youtube.com/vi/{{ $video->youtube_id }}/mqdefault.jpg" alt="{{ $video->title }}" class="absolute inset-0 w-full h-full object-cover" loading="lazy">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <div class="w-10 h-10 rounded-full bg-black/70 dark:bg-white/90 flex items-center justify-center">
                                    <i class="fas fa-play text-white dark:text-gray-900 text-xs ml-0.5"></i>
                                </div>
                            </div>
                        @endif
                    </div>
                </button>

                <div class="p-4 flex-1 flex flex-col">
                    <p class="text-[10px] font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">{{ $video->category ?? 'Tutorial' }}</p>
                    <h3 class="font-semibold text-sm text-gray-900 dark:text-white line-clamp-2 mb-2">{{ $video->title }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-2 flex-1 mb-3">{{ Str::limit($video->description, 70) }}</p>

                    <a href="{{ $video->youtube_link ?? 'https://www.youtube.com/watch?v=' . $video->youtube_id }}" target="_blank" class="text-xs text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition">
                        Watch on YouTube
                        <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-12">
            <a href="{{ route('videos.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-900 dark:text-white border-b border-gray-900 dark:border-white pb-0.5 hover:gap-3 transition-all">
                View all tutorials
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</section>
@endif

{{-- CTA --}}
<section class="reveal bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-20 sm:py-28">
        <div class="max-w-2xl">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">Get started</p>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-semibold text-gray-900 dark:text-white leading-[1.1] tracking-tight mb-6">
                Ready to build something?
            </h2>
            <p class="text-lg text-gray-600 dark:text-gray-400 mb-10 max-w-xl">
                Create a free account to submit project ideas, join classes, and track everything you learn.
            </p>
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                Create Free Account
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
            <p class="terminal-line text-sm text-gray-500 dark:text-gray-500 mt-12">
                <span class="text-secondary">$</span> ready when you are<span class="cursor-blink">▌</span>
            </p>
        </div>
    </div>
</section>

{{-- Video Modal --}}
<div id="videoModal" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeVideoModal()">
    <button type="button" onclick="closeVideoModal()" class="absolute top-4 right-4 text-white/70 hover:text-white transition" aria-label="Close video">
        <i class="fas fa-times text-2xl"></i>
    </button>
    <div class="w-full max-w-4xl aspect-video">
        <iframe id="videoFrame" src="" class="w-full h-full rounded-lg" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
    </div>
</div>

@push('scripts')
<script>
    function openVideoModal(videoId) {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        if (modal && frame && videoId) {
            frame.src = 'https://www.youtube.com/embed/' + videoId + '?autoplay=1&rel=0';
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const frame = document.getElementById('videoFrame');
        if (modal && frame) {
            frame.src = '';
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeVideoModal();
    });

    // Reveal on scroll
    (function () {
        const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const revealables = document.querySelectorAll('.reveal');

        if (prefersReduced) {
            revealables.forEach(el => el.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.08,
            rootMargin: '0px 0px -40px 0px',
        });

        revealables.forEach((el) => observer.observe(el));
    })();
</script>
@endpush
@endsection