@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Dashboard
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Welcome back, {{ $user->name }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Manage your projects, suggestions, and explore new sensors.
        </p>
    </div>

    {{-- Profile Card --}}
    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 sm:p-6 mb-12">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($user->profile_image)
                    <img src="{{ Str::startsWith($user->profile_image, ['http://', 'https://']) ? $user->profile_image : asset($user->profile_image) }}"
                         alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-xl font-semibold text-white dark:text-gray-900">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ $user->name }}</h2>
                    <span class="text-[10px] font-medium uppercase tracking-wider
                        {{ auth()->user()->isUser() ? 'text-gray-500 dark:text-gray-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                        {{ auth()->user()->isUser() ? 'User' : 'Student' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
            </div>
            <a href="{{ route('dashboard.profile') }}"
               class="inline-flex items-center gap-2 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition flex-shrink-0">
                <i class="fas fa-user-edit text-[11px]"></i>
                Edit profile
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-12">
        {{-- My Suggestions --}}
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">My Suggestions</p>
            <p class="text-3xl font-semibold text-gray-900 dark:text-white mb-3">{{ $suggestionsCount }}</p>
            <a href="{{ route('dashboard.suggestions') }}" class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition">
                View all
                <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        {{-- Saved Projects --}}
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Saved Projects</p>
            <p class="text-3xl font-semibold text-gray-900 dark:text-white mb-3">{{ $savedProjectsCount }}</p>
            <a href="{{ route('dashboard.saved') }}" class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition">
                View all
                <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
        {{-- My Class --}}
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">My Class</p>
            <p class="text-lg font-semibold text-gray-900 dark:text-white mb-3 truncate">{{ $myClass?->name ?? 'No class yet' }}</p>
            <a href="{{ $myClass ? route('dashboard.classes.show', $myClass) : route('dashboard.classes.index') }}" class="text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition">
                {{ $myClass ? 'View class' : 'Join a class' }}
                <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="mb-12">
        <div class="mb-4">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Shortcuts
            </p>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Quick actions
            </h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <button type="button" onclick="document.getElementById('suggestionModal').classList.remove('hidden')"
                class="w-full flex items-center gap-3 p-4 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-white hover:bg-gray-50 dark:hover:bg-gray-800/40 transition text-left cursor-pointer appearance-none bg-transparent">
                <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-plus text-sm text-white dark:text-gray-900"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Submit suggestion</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Share your project idea</p>
                </div>
            </button>
            <a href="{{ route('projects.index') }}"
                class="flex items-center gap-3 p-4 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-white hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-search text-sm text-white dark:text-gray-900"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Browse projects</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Explore sensor projects</p>
                </div>
            </a>
            <a href="https://sensors-hub-simulator.vercel.app/" target="_blank"
                class="flex items-center gap-3 p-4 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg hover:border-gray-900 dark:hover:border-white hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-flask text-sm text-white dark:text-gray-900"></i>
                </div>
                <div class="min-w-0">
                    <p class="font-medium text-gray-900 dark:text-white text-sm">Simulation</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">Test sensor circuits</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Suggestions --}}
    <div>
        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Activity
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Recent suggestions
                </h2>
            </div>
            @if($recentSuggestions->count() > 0)
                <a href="{{ route('dashboard.suggestions') }}" class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition">
                    View all
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            @endif
        </div>

        @if($recentSuggestions->count() > 0)
            <div class="space-y-3">
                @foreach($recentSuggestions as $suggestion)
                    <a href="{{ route('dashboard.suggestions.show', $suggestion) }}"
                       class="block border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $suggestion->title }}</h3>
                                    <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0
                                        @if($suggestion->status === 'pending') text-amber-600 dark:text-amber-400
                                        @elseif($suggestion->status === 'reviewed') text-blue-600 dark:text-blue-400
                                        @elseif($suggestion->status === 'implemented') text-emerald-600 dark:text-emerald-400
                                        @else text-red-600 dark:text-red-400
                                        @endif">
                                        ● {{ $suggestion->status }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1">{{ Str::limit($suggestion->description, 100) }}</p>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-600 flex-shrink-0 mt-0.5">{{ $suggestion->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-lightbulb text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">No suggestions yet. Submit your first idea.</p>
                <button onclick="document.getElementById('suggestionModal').classList.remove('hidden')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                    <i class="fas fa-plus text-xs"></i>
                    Submit suggestion
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Suggestion Modal --}}
<div id="suggestionModal" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-5 border-b border-gray-200 dark:border-gray-800 flex items-center justify-between">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-1">
                    New suggestion
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Submit project suggestion</h2>
            </div>
            <button onclick="document.getElementById('suggestionModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('dashboard.suggestions.store') }}">
                @csrf
                <div class="space-y-5">
                    <div>
                        <label for="title" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Project title
                        </label>
                        <input type="text" name="title" id="title" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('title') !border-red-500 @enderror"
                            placeholder="e.g., Automatic plant watering system">
                        @error('title') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="description" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Description
                        </label>
                        <textarea name="description" id="description" rows="5" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none @error('description') !border-red-500 @enderror"
                            placeholder="Describe your project idea in detail..."></textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="difficulty" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                Difficulty
                            </label>
                            <select name="difficulty" id="difficulty"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                                <option value="">Select difficulty</option>
                                <option value="Beginner">Beginner</option>
                                <option value="Intermediate">Intermediate</option>
                                <option value="Advanced">Advanced</option>
                            </select>
                        </div>
                        <div>
                            <label for="sensor_type" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                Sensor type
                            </label>
                            <input type="text" name="sensor_type" id="sensor_type"
                                class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm"
                                placeholder="e.g., DHT11, HC-SR04">
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-end gap-3">
                    <button type="button" onclick="document.getElementById('suggestionModal').classList.add('hidden')"
                        class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                        Cancel
                    </button>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                        <i class="fas fa-paper-plane text-xs"></i>
                        Submit suggestion
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection