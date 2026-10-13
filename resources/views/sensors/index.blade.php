@extends('layouts.app')

@section('title', 'Sensors')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            All Sensors
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Explore the components
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Discover different types of sensors and learn how they work — with real projects, tutorials, and code.
        </p>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('sensors.index') }}" class="mb-12">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search sensors..." 
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition">
            </div>
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('sensors.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 border border-gray-200 dark:border-gray-800 text-gray-700 dark:text-gray-300 font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    Clear
                </a>
            @endif
        </div>
        @if(request('search'))
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Results for "<span class="font-medium text-gray-900 dark:text-white">{{ request('search') }}</span>"
            </p>
        @endif
    </form>

    {{-- Sensors Grid --}}
    @if($sensors->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($sensors as $sensor)
                <a href="{{ route('sensors.show', $sensor->slug) }}" 
                   class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:border-gray-400 dark:hover:border-gray-600 transition flex flex-col group">
                    
                    {{-- Image --}}
                    <div class="aspect-[16/10] bg-gray-100 dark:bg-gray-900 overflow-hidden">
                        @if($sensor->image)
                            <img src="{{ Str::startsWith($sensor->image, ['http://', 'https://']) ? $sensor->image : (Str::startsWith($sensor->image, ['images/', '/images/']) ? asset($sensor->image) : asset('storage/' . $sensor->image)) }}" 
                                alt="{{ $sensor->name }}" 
                                class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-microchip text-4xl text-gray-400 dark:text-gray-600"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-primary transition">
                            {{ $sensor->name }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 flex-1 line-clamp-2 mb-4">
                            {{ Str::limit($sensor->description, 90) }}
                        </p>

                        {{-- Meta --}}
                        <div class="flex items-center gap-4 pt-3 border-t border-gray-100 dark:border-gray-800 mb-3 text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-project-diagram"></i>
                                {{ $sensor->projects_count }} {{ Str::plural('Project', $sensor->projects_count) }}
                            </span>
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-video"></i>
                                {{ $sensor->videos_count }} {{ Str::plural('Video', $sensor->videos_count) }}
                            </span>
                        </div>

                        {{-- Learn more --}}
                        <span class="text-sm font-medium text-gray-900 dark:text-white inline-flex items-center gap-1 group-hover:gap-2 transition-all">
                            Learn more
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($sensors->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $sensors->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-microchip text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No sensors found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                @if(request('search'))
                    Try a different search term.
                @else
                    Check back later for new sensors.
                @endif
            </p>
            @if(request('search'))
                <a href="{{ route('sensors.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    Clear search
                </a>
            @endif
        </div>
    @endif
</div>
@endsection