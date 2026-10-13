@extends('layouts.app')

@section('title', 'Video Tutorials')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Tutorials
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Watch & build
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Learn from real sensor tutorials — follow along, then make it your own.
        </p>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('videos.index') }}" class="mb-12">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-7 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search videos..." 
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition">
            </div>
            <div class="sm:col-span-3">
                <select name="category" class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition">
                    <option value="">All Categories</option>
                    <option value="Tutorial" {{ request('category') == 'Tutorial' ? 'selected' : '' }}>Tutorial</option>
                    <option value="Project" {{ request('category') == 'Project' ? 'selected' : '' }}>Project</option>
                    <option value="Review" {{ request('category') == 'Review' ? 'selected' : '' }}>Review</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    Search
                </button>
            </div>
        </div>
        @if(request('search') || request('category'))
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-3">
                <span>
                    @if(request('search'))
                        Results for "<span class="font-medium text-gray-900 dark:text-white">{{ request('search') }}</span>"
                    @endif
                    @if(request('search') && request('category')) ·
                    @endif
                    @if(request('category'))
                        Category: <span class="font-medium text-gray-900 dark:text-white">{{ request('category') }}</span>
                    @endif
                </span>
                <a href="{{ route('videos.index') }}" class="text-gray-900 dark:text-white hover:underline">
                    Clear
                </a>
            </p>
        @endif
    </form>

    {{-- Videos Grid --}}
    @if($videos->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($videos as $video)
                <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:border-gray-400 dark:hover:border-gray-600 transition flex flex-col group">

                    {{-- Thumbnail → opens video modal --}}
                    <button type="button" 
                        onclick="openVideoModal('{{ $video->youtube_id }}')" 
                        class="text-left block w-full">
                        <div class="relative aspect-video bg-gray-100 dark:bg-gray-900 overflow-hidden">
                            @if($video->youtube_id)
                                <img 
                                    src="https://img.youtube.com/vi/{{ $video->youtube_id }}/mqdefault.jpg" 
                                    alt="{{ $video->title }}" 
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" 
                                    loading="lazy">
                                <div class="absolute inset-0 flex items-center justify-center bg-black/0 group-hover:bg-black/20 transition">
                                    <div class="w-12 h-12 rounded-full bg-black/70 dark:bg-white/90 flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform">
                                        <i class="fas fa-play text-white dark:text-gray-900 text-sm ml-0.5"></i>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <i class="fab fa-youtube text-4xl text-gray-400 dark:text-gray-600"></i>
                                </div>
                            @endif
                        </div>
                    </button>

                    {{-- Content --}}
                    <div class="p-5 flex-1 flex flex-col">

                        {{-- Category --}}
                        @if($video->category)
                            <p class="text-[10px] font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                {{ $video->category }}
                            </p>
                        @endif

                        {{-- Title --}}
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2">
                            {{ $video->title }}
                        </h3>

                        {{-- Sensor --}}
                        @if($video->sensor)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 inline-flex items-center gap-1.5">
                                <i class="fas fa-microchip"></i>
                                {{ $video->sensor->name }}
                            </p>
                        @endif

                        {{-- Description --}}
                        @if($video->description)
                            <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 flex-1 mb-4">
                                {{ Str::limit($video->description, 100) }}
                            </p>
                        @endif

                        {{-- Watch on YouTube (external) --}}
                        <a href="https://www.youtube.com/watch?v={{ $video->youtube_id }}" 
                           target="_blank" 
                           class="text-sm font-medium text-gray-900 dark:text-white inline-flex items-center gap-1 hover:gap-2 transition-all">
                            Watch on YouTube
                            <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($videos->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $videos->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fab fa-youtube text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No videos found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                @if(request('search') || request('category'))
                    Try a different search or category.
                @else
                    Check back later for new tutorials.
                @endif
            </p>
            @if(request('search') || request('category'))
                <a href="{{ route('videos.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    Clear filters
                </a>
            @endif
        </div>
    @endif

    {{-- CTA --}}
    <div class="mt-20 bg-gray-900 dark:bg-black rounded-2xl p-8 sm:p-12 text-center">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Subscribe
        </p>
        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white mb-4">
            Get the latest tutorials
        </h2>
        <p class="text-gray-400 mb-8 max-w-xl mx-auto">
            Subscribe to our YouTube channel for new sensor tutorials and project guides.
        </p>
        <a href="https://youtube.com" target="_blank" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 font-medium rounded-lg hover:bg-gray-100 transition">
            Visit YouTube Channel
            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
        </a>
    </div>

    {{-- Video Modal --}}
    <div id="videoModal" class="hidden fixed inset-0 bg-black/90 z-50 flex items-center justify-center p-4" onclick="if(event.target === this) closeVideoModal()">
        <button type="button" onclick="closeVideoModal()" class="absolute top-4 right-4 text-white/70 hover:text-white transition" aria-label="Close video">
            <i class="fas fa-times text-2xl"></i>
        </button>
        <div class="w-full max-w-4xl aspect-video">
            <iframe id="videoFrame" src="" class="w-full h-full rounded-lg" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
</div>
@endsection

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
</script>
@endpush