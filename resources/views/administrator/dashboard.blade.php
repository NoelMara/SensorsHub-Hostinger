@extends('layouts.app')

@section('title', 'Administrator Dashboard')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Welcome back, {{ auth()->user()->name }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Manage your entire platform from one place.
        </p>
    </div>

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
                    <span class="text-[10px] font-medium uppercase tracking-wider text-purple-600 dark:text-purple-400">
                        Administrator
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
            </div>
            <a href="{{ route('administrator.profile') }}"
               class="inline-flex items-center gap-2 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition flex-shrink-0">
                <i class="fas fa-user-edit text-[11px]"></i>
                Edit profile
            </a>
        </div>
    </section>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-users text-indigo-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total users</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['users'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-user-shield text-blue-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Instructors</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['instructors'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-layer-group text-emerald-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Content items</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white">{{ $stats['sensors'] + $stats['projects'] + $stats['products'] + $stats['videos'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-clock {{ $stats['pending_suggestions'] > 0 ? 'text-amber-500' : 'text-gray-400 dark:text-gray-600' }} text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Pending</p>
            </div>
            <p class="text-2xl font-semibold {{ $stats['pending_suggestions'] > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }}">{{ $stats['pending_suggestions'] }}</p>
        </div>
    </div>

    {{-- Quick Links --}}
    <section class="mb-12">
        <div class="mb-4">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Shortcuts
            </p>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                Quick links
            </h2>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <a href="{{ route('administrator.users.index') }}"
               class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <i class="fas fa-users text-indigo-500 text-sm mb-3 block"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Users</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['users'] }}</p>
            </a>
            <a href="{{ route('administrator.suggestions.index') }}"
               class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <i class="fas fa-lightbulb text-amber-500 text-sm mb-3 block"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Suggestions</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['suggestions'] }}</p>
            </a>
            <a href="{{ route('administrator.sensors.index') }}"
               class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <i class="fas fa-microchip text-emerald-500 text-sm mb-3 block"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Sensors</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['sensors'] }}</p>
            </a>
            <a href="{{ route('administrator.projects.index') }}"
               class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <i class="fas fa-project-diagram text-blue-500 text-sm mb-3 block"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Projects</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['projects'] }}</p>
            </a>
            <a href="{{ route('administrator.products.index') }}"
               class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <i class="fas fa-shopping-cart text-purple-500 text-sm mb-3 block"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Products</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['products'] }}</p>
            </a>
            <a href="{{ route('administrator.videos.index') }}"
               class="border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                <i class="fas fa-video text-red-500 text-sm mb-3 block"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Videos</p>
                <p class="text-lg font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['videos'] }}</p>
            </a>
        </div>
    </section>

    {{-- Recent activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">

        {{-- Recent users --}}
        <section class="lg:col-span-2">
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Recent
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        New users
                    </h2>
                </div>
                <a href="{{ route('administrator.users.index') }}"
                   class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition flex-shrink-0">
                    View all
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>

            @if($recentUsers->count() > 0)
                <div class="space-y-2">
                    @foreach($recentUsers as $user)
                        <a href="{{ route('administrator.users.show', $user) }}"
                           class="flex items-center justify-between gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <div class="flex items-center gap-3 min-w-0 flex-1">
                                <div class="w-9 h-9 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-xs font-semibold text-white dark:text-gray-900">
                                    @if($user->profile_image)
                                        <img src="{{ Str::startsWith($user->profile_image, ['http://', 'https://']) ? $user->profile_image : asset($user->profile_image) }}"
                                             alt="{{ $user->name }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                            <span class="text-[10px] font-medium uppercase tracking-wider flex-shrink-0
                                {{ $user->role === 'administrator' ? 'text-purple-600 dark:text-purple-400' : '' }}
                                {{ $user->role === 'instructor' ? 'text-blue-600 dark:text-blue-400' : '' }}
                                {{ $user->role === 'student' ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                                {{ $user->role === 'user' ? 'text-gray-500 dark:text-gray-400' : '' }}">
                                {{ $user->role === 'administrator' ? 'Admin' : ($user->role === 'instructor' ? 'Instructor' : ($user->role === 'student' ? 'Student' : 'User')) }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                    <i class="fas fa-users text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">No users yet.</p>
                </div>
            @endif
        </section>

        {{-- Pending suggestions --}}
        <section>
            <div class="mb-4 flex items-end justify-between gap-4">
                <div>
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Pending
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Suggestions
                    </h2>
                </div>
                @if($recentSuggestions->count() > 0)
                    <a href="{{ route('administrator.suggestions.index') }}"
                       class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white inline-flex items-center gap-1 transition flex-shrink-0">
                        Review
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                @endif
            </div>

            @if($recentSuggestions->count() > 0)
                <div class="space-y-2">
                    @foreach($recentSuggestions as $suggestion)
                        <a href="{{ route('administrator.suggestions.show', $suggestion) }}"
                           class="block border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate mb-1">{{ Str::limit($suggestion->title, 50) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                by {{ $suggestion->user?->name ?? 'Deleted user' }} · {{ $suggestion->created_at->diffForHumans() }}
                            </p>
                        </a>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                    <i class="fas fa-check-circle text-3xl text-emerald-500 mb-3"></i>
                    <p class="text-sm text-gray-500 dark:text-gray-400">All clear.</p>
                </div>
            @endif
        </section>
    </div>

    {{-- Latest comments --}}
    <section>
        <div class="mb-4 flex items-end justify-between gap-4">
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Latest
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Comments
                </h2>
            </div>
        </div>

        @if($recentComments->count() > 0)
            <div class="space-y-2">
                @foreach($recentComments as $comment)
                    <a href="{{ route('administrator.suggestions.show', $comment->suggestion) }}"
                       class="block border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-xs font-semibold text-white dark:text-gray-900">
                                @if($comment->user?->profile_image)
                                    <img src="{{ Str::startsWith($comment->user->profile_image, ['http://', 'https://']) ? $comment->user->profile_image : asset($comment->user->profile_image) }}"
                                         alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($comment->user?->name ?? '?', 0, 1)) }}
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->user?->name ?? 'Deleted user' }}</span>
                                    <span class="text-xs text-gray-400 dark:text-gray-600">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-1">{{ Str::limit($comment->body, 100) }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    on: <span class="text-gray-900 dark:text-white">{{ $comment->suggestion?->title ?? 'Deleted suggestion' }}</span>
                                </p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-comments text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet.</p>
            </div>
        @endif
    </section>
</div>
@endsection