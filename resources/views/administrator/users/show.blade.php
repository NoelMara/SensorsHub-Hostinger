@extends('layouts.app')

@section('title', 'View User')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('administrator.users.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Users
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · User
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            User details
        </h1>
    </div>

    {{-- Profile card --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-8">
        <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
            <div class="w-20 h-20 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center overflow-hidden flex-shrink-0">
                @if($user->profile_image)
                    <img src="{{ Str::startsWith($user->profile_image, ['http://', 'https://']) ? $user->profile_image : asset($user->profile_image) }}"
                         alt="{{ $user->name }}" class="w-full h-full object-cover">
                @else
                    <span class="text-2xl font-semibold text-white dark:text-gray-900">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 justify-center sm:justify-start flex-wrap mb-1">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white break-words">{{ $user->name }}</h2>
                    <span class="text-[10px] font-medium uppercase tracking-wider
                        {{ $user->role === 'administrator' ? 'text-purple-600 dark:text-purple-400' : '' }}
                        {{ $user->role === 'instructor' ? 'text-blue-600 dark:text-blue-400' : '' }}
                        {{ $user->role === 'student' ? 'text-emerald-600 dark:text-emerald-400' : '' }}
                        {{ $user->role === 'user' ? 'text-gray-500 dark:text-gray-400' : '' }}">
                        {{ $user->role === 'administrator' ? 'Administrator' : ($user->role === 'instructor' ? 'Instructor' : ($user->role === 'student' ? 'Student' : 'User')) }}
                    </span>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 break-words">{{ $user->email }}</p>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2 justify-center sm:justify-start mt-4">
                    @if(!$user->isAdministrator() || $user->is(auth()->user()))
                        <a href="{{ route('administrator.users.edit', $user) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                            <i class="fas fa-edit text-[11px]"></i>
                            Edit
                        </a>
                    @endif
                    @if(!$user->is(auth()->user()) && !$user->isAdministrator())
                        <form method="POST" action="{{ route('administrator.users.destroy', $user) }}"
                            onsubmit="return confirm('Remove this account?');" class="inline">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                                <i class="fas fa-trash text-[11px]"></i>
                                Remove
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Info tiles --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-8">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-calendar-check text-xs {{ $user->email_verified_at ? 'text-emerald-500' : 'text-amber-500' }}"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Email status</p>
            </div>
            <p class="text-sm font-semibold {{ $user->email_verified_at ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400' }}">
                {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
            </p>
            @if($user->email_verified_at)
                <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">{{ $user->email_verified_at->format('M d, Y') }}</p>
            @endif
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-user-plus text-xs text-blue-500"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Account created</p>
            </div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">{{ $user->created_at->diffForHumans() }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-clock text-xs text-gray-400 dark:text-gray-600"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Last updated</p>
            </div>
            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $user->updated_at->format('M d, Y') }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">{{ $user->updated_at->diffForHumans() }}</p>
        </div>
    </div>

    {{-- Moderation --}}
    @if(!$user->isAdministrator())
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 mb-8">
            <div class="mb-6">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Moderation
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Account status
                </h2>
            </div>

            @if($user->isBanned())
                {{-- Banned state --}}
                <div class="border-l-2 border-red-500 dark:border-red-400 pl-4 py-1 mb-6">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-red-600 dark:text-red-400 mb-2">
                        ● Banned
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                        {{ $user->ban_reason ?: 'No reason provided.' }}
                    </p>
                </div>

                <form method="POST" action="{{ route('administrator.users.unban', $user) }}">
                    @csrf
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-emerald-500 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-950/20 transition text-sm font-medium">
                        <i class="fas fa-unlock text-xs"></i>
                        Unban user
                    </button>
                </form>
            @else
                {{-- Warning count --}}
                <div class="flex items-center justify-between gap-3 mb-6 pb-6 border-b border-gray-100 dark:border-gray-800">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">Warnings</p>
                        <p class="text-lg font-semibold {{ $user->warning_count > 0 ? 'text-amber-600 dark:text-amber-400' : 'text-gray-900 dark:text-white' }}">
                            {{ $user->warning_count }} / 3
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap items-center gap-2">
                    <form method="POST" action="{{ route('administrator.users.warn', $user) }}" class="inline">
                        @csrf
                        <input type="hidden" name="reason" value="">
                        <button type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-amber-500 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-950/20 transition text-sm font-medium"
                            onclick="let reason = prompt('Warning reason:'); if(reason) { this.form.querySelector('[name=reason]').value = reason; this.form.submit(); }">
                            <i class="fas fa-exclamation-triangle text-xs"></i>
                            Warn
                        </button>
                    </form>
                    <form method="POST" action="{{ route('administrator.users.ban', $user) }}" class="inline">
                        @csrf
                        <input type="hidden" name="reason" value="">
                        <button type="button"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-red-500 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition text-sm font-medium"
                            onclick="let reason = prompt('Ban reason:'); if(reason) { this.form.querySelector('[name=reason]').value = reason; this.form.submit(); }">
                            <i class="fas fa-ban text-xs"></i>
                            Ban
                        </button>
                    </form>
                </div>
            @endif
        </section>
    @endif

    {{-- Bottom back link --}}
    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-800">
        <a href="{{ route('administrator.users.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to Users
        </a>
    </div>
</div>
@endsection