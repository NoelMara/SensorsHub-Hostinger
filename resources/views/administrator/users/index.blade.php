@extends('layouts.app')

@section('title', 'Manage Users')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Users
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Manage users
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-8">
            Manage student and instructor accounts.
        </p>
        <a href="{{ route('administrator.users.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
            <i class="fas fa-plus text-xs"></i>
            Add user
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('administrator.users.index') }}" class="mb-6">
        @if($role)
            <input type="hidden" name="role" value="{{ $role }}">
        @endif
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name or email..."
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            </div>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                <i class="fas fa-search text-xs"></i>
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('administrator.users.index', $role ? ['role' => $role] : []) }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium whitespace-nowrap">
                    <i class="fas fa-times text-xs"></i>
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Filter tabs --}}
    <div class="flex flex-wrap items-center gap-2 mb-8">
        @php
            $tabs = [
                ['label' => 'All',         'role' => null,             'count' => $roleCounts['all']],
                ['label' => 'Users',       'role' => 'user',           'count' => $roleCounts['user']],
                ['label' => 'Students',    'role' => 'student',        'count' => $roleCounts['student']],
                ['label' => 'Instructors', 'role' => 'instructor',     'count' => $roleCounts['instructor']],
                ['label' => 'Admins',      'role' => 'administrator',  'count' => $roleCounts['administrator']],
            ];
        @endphp

        @foreach($tabs as $tab)
            @php $isActive = $role === $tab['role']; @endphp
            <a href="{{ route('administrator.users.index', $tab['role'] ? ['role' => $tab['role']] : []) }}"
               class="inline-flex items-center gap-2.5 px-4 py-2 rounded-lg text-xs font-medium transition whitespace-nowrap
                   {{ $isActive
                       ? 'bg-gray-900 dark:bg-white text-white dark:text-gray-900'
                       : 'border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 hover:border-gray-400 dark:hover:border-gray-600' }}">
                <span>{{ $tab['label'] }}</span>
                <span class="tabular-nums px-1.5 py-0.5 rounded text-[10px] font-semibold
                    {{ $isActive
                        ? 'bg-white/20 dark:bg-black/15 text-white dark:text-gray-900'
                        : 'bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400' }}">
                    {{ $tab['count'] }}
                </span>
            </a>
        @endforeach
    </div>

    {{-- Users table --}}
    <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-gray-800">
                        <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Role</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Verified</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Joined</th>
                        <th class="px-5 py-3 text-right text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="border-b border-gray-100 dark:border-gray-800/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                            {{-- User --}}
                            <td class="px-5 py-4 align-top">
                                <div class="flex items-center gap-3">
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
                            </td>

                            {{-- Role selector --}}
                            <td class="px-5 py-4 align-top">
                                <form method="POST" action="{{ route('administrator.users.role', $user) }}" class="flex items-center gap-2">
                                    @csrf
                                    @method('PUT')
                                    <select name="role"
                                        class="text-xs px-3 py-2 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition">
                                        <option value="student" @selected($user->role === 'student')>Student</option>
                                        <option value="instructor" @selected($user->role === 'instructor')>Instructor</option>
                                        <option value="administrator" @selected($user->role === 'administrator')>Administrator</option>
                                    </select>
                                    <button type="submit"
                                        class="inline-flex items-center px-2.5 h-7 rounded-lg border border-gray-200 dark:border-gray-800 text-[11px] font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                                        Save
                                    </button>
                                </form>
                            </td>

                            {{-- Verified --}}
                            <td class="px-5 py-4 align-top">
                                @if($user->email_verified_at)
                                    <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-400">● Verified</span>
                                @else
                                    <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400">● Pending</span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-5 py-4 align-top">
                                @if($user->isBanned())
                                    <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-red-600 dark:text-red-400">● Banned</span>
                                @elseif($user->warning_count > 0)
                                    <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400">● {{ $user->warning_count }} {{ Str::plural('warning', $user->warning_count) }}</span>
                                @else
                                    <span class="text-xs text-gray-300 dark:text-gray-700">—</span>
                                @endif
                            </td>

                            {{-- Joined --}}
                            <td class="px-5 py-4 align-top">
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $user->created_at->format('M d, Y') }}</span>
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-4 align-top text-right">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('administrator.users.show', $user) }}"
                                       class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition"
                                       title="View">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if(!$user->isAdministrator() || $user->is(auth()->user()))
                                        <a href="{{ route('administrator.users.edit', $user) }}"
                                           class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition"
                                           title="Edit">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    @endif
                                    @if(!$user->is(auth()->user()) && !$user->isAdministrator())
                                        <form method="POST" action="{{ route('administrator.users.destroy', $user) }}"
                                            onsubmit="return confirm('Remove this user?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center justify-center w-8 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-gray-500 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition"
                                                title="Remove">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-20 text-center">
                                <i class="fas fa-users text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No users found</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filters.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection