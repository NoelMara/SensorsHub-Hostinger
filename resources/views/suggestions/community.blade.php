@extends('layouts.app')

@section('title', 'Community Suggestions')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Community
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Suggestions feed
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Discover and discuss project ideas from the community.
        </p>
    </div>

    {{-- Action button --}}
    @auth
        <div class="mb-8">
            @if(auth()->user()->isInstructor() || auth()->user()->isAdministrator())
                <a href="{{ route('instructor.suggestions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                    <i class="fas fa-clipboard-list text-xs"></i>
                    Manage suggestions
                </a>
            @else
                <a href="{{ route('dashboard.suggestions') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                    <i class="fas fa-list text-xs"></i>
                    My suggestions
                </a>
            @endif
        </div>
    @endauth

    @if($suggestions->count() > 0)
        <div class="space-y-3">
            @foreach($suggestions as $suggestion)
                <a href="{{ auth()->user() && (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? route('instructor.suggestions.show', $suggestion) : route('dashboard.suggestions.show', $suggestion) }}" 
                   class="block border border-gray-200 dark:border-gray-800 rounded-lg p-5 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition group">

                    <div class="flex items-start gap-4">

                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-sm font-semibold text-white dark:text-gray-900">
                            @if($suggestion->user?->profile_image)
                                <img src="{{ Str::startsWith($suggestion->user->profile_image, ['http://', 'https://']) ? $suggestion->user->profile_image : asset($suggestion->user->profile_image) }}"
                                     alt="{{ $suggestion->user->name }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($suggestion->user?->name ?? '?', 0, 1)) }}
                            @endif
                        </div>

                        {{-- Content --}}
                        <div class="flex-1 min-w-0">

                            {{-- Title + status --}}
                            <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">
                                    {{ $suggestion->title }}
                                </h3>
                                <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0
                                    @if($suggestion->status === 'pending') text-amber-600 dark:text-amber-400
                                    @elseif($suggestion->status === 'reviewed') text-blue-600 dark:text-blue-400
                                    @elseif($suggestion->status === 'implemented') text-emerald-600 dark:text-emerald-400
                                    @else text-red-600 dark:text-red-400
                                    @endif">
                                    ● {{ $suggestion->status }}
                                </span>
                            </div>

                            {{-- Description --}}
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 mb-3">
                                {{ Str::limit($suggestion->description, 200) }}
                            </p>

                            {{-- Meta --}}
                            <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $suggestion->user?->name ?? 'Deleted user' }}
                                </span>
                                <span>{{ $suggestion->created_at->diffForHumans() }}</span>
                                <div class="flex items-center gap-3 ml-auto">
                                    @if($suggestion->difficulty)
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-signal"></i>
                                            {{ $suggestion->difficulty }}
                                        </span>
                                    @endif
                                    @if($suggestion->sensor_type)
                                        <span class="inline-flex items-center gap-1">
                                            <i class="fas fa-microchip"></i>
                                            {{ $suggestion->sensor_type }}
                                        </span>
                                    @endif
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-comments"></i>
                                        {{ $suggestion->comments->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Arrow --}}
                        <i class="fas fa-arrow-right text-gray-300 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-0.5 transition-all flex-shrink-0 mt-1"></i>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12 flex justify-center">
            {{ $suggestions->links() }}
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-lightbulb text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No suggestions yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                Be the first to share your project idea with the community.
            </p>
            @auth
                @if(auth()->user()->isInstructor() || auth()->user()->isAdministrator())
                    <a href="{{ route('instructor.suggestions.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                        <i class="fas fa-clipboard-list text-xs"></i>
                        Manage suggestions
                    </a>
                @else
                    <a href="{{ route('dashboard.suggestions') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                        <i class="fas fa-list text-xs"></i>
                        My suggestions
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                    <i class="fas fa-arrow-right text-xs"></i>
                    Login to submit
                </a>
            @endauth
        </div>
    @endif
</div>
@endsection