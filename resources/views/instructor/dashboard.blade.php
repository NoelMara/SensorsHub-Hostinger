@extends('layouts.app')

@section('title', 'Instructor Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Dashboard
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Here's what's happening with your classes.
        </p>
    </div>

    {{-- Administrator section --}}
    @if(auth()->user()->isAdministrator())
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 mb-12">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Administrator
            </p>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                        Manage accounts
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Instructor and student user management.
                    </p>
                </div>
                <a href="{{ route('administrator.users.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm flex-shrink-0">
                    <i class="fas fa-users-cog text-xs"></i>
                    Manage accounts
                </a>
            </div>
        </section>
    @endif

    {{-- Profile card --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 sm:p-6 mb-12">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                @if(auth()->user()->profile_image)
                    <img src="{{ Str::startsWith(auth()->user()->profile_image, ['http://', 'https://']) ? auth()->user()->profile_image : asset(auth()->user()->profile_image) }}"
                         alt="{{ auth()->user()->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-xl font-semibold text-white dark:text-gray-900">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap mb-1">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</h2>
                    <span class="text-[10px] font-medium uppercase tracking-wider
                        {{ auth()->user()->isAdministrator() ? 'text-purple-600 dark:text-purple-400' : 'text-blue-600 dark:text-blue-400' }}">
                        {{ auth()->user()->isAdministrator() ? 'Administrator' : 'Instructor' }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ auth()->user()->isAdministrator() ? route('administrator.profile') : route('dashboard.profile') }}"
               class="inline-flex items-center gap-2 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition flex-shrink-0">
                <i class="fas fa-user-edit text-[11px]"></i>
                Edit profile
            </a>
        </div>
    </section>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                Total users
            </p>
            <p class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['users'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">
                Suggestions
            </p>
            <div class="flex items-baseline gap-3">
                <p class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['suggestions'] }}</p>
                @if($stats['pending_suggestions'] > 0)
                    <span class="text-xs font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        ● {{ $stats['pending_suggestions'] }} pending
                    </span>
                @endif
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent users --}}
        <section>
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Recent
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    New users
                </h2>
            </div>

            @if($recentUsers->count() > 0)
                <div class="space-y-2">
                    @foreach($recentUsers as $user)
                        <div class="flex items-center justify-between gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-xs font-semibold text-white dark:text-gray-900">
                                    @if($user->profile_image)
                                        <img src="{{ Str::startsWith($user->profile_image, ['http://', 'https://']) ? $user->profile_image : asset($user->profile_image) }}"
                                             alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                                        <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0
                                            {{ $user->role === 'user' ? 'text-gray-500 dark:text-gray-400' : 'text-emerald-600 dark:text-emerald-400' }}">
                                            {{ $user->role === 'user' ? 'User' : 'Student' }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400 dark:text-gray-600 flex-shrink-0">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                    <i class="fas fa-users text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No users yet.</p>
                </div>
            @endif
        </section>

        {{-- Recent suggestions --}}
        <section>
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Recent
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Suggestions
                    </h2>
                </div>
                <a href="{{ route('instructor.suggestions.index') }}"
                   class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition flex-shrink-0">
                    View all
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            @if($recentSuggestions->count() > 0)
                <div class="space-y-2">
                    @foreach($recentSuggestions as $suggestion)
                        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate mb-0.5">
                                        {{ Str::limit($suggestion->title, 50) }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        by {{ $suggestion->user?->name ?? 'Deleted user' }}
                                    </p>
                                </div>
                                <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0
                                    @if($suggestion->status === 'pending') text-amber-600 dark:text-amber-400
                                    @elseif($suggestion->status === 'reviewed') text-blue-600 dark:text-blue-400
                                    @elseif($suggestion->status === 'implemented') text-emerald-600 dark:text-emerald-400
                                    @else text-red-600 dark:text-red-400
                                    @endif">
                                    ● {{ $suggestion->status }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                    <i class="fas fa-lightbulb text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No suggestions yet.</p>
                </div>
            @endif
        </section>
    </div>
</div>
@endsection