@extends('layouts.app')

@section('title', 'Suggestions')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Suggestions
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Suggestions
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Review user feedback and track status.
        </p>
    </div>

    {{-- Search & filter --}}
    <form method="GET" action="{{ route('administrator.suggestions.index') }}" class="mb-8">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by title, description, or user..."
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            </div>
                <select name="status"
                class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                <option value="">All status</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="reviewed" {{ request('status') === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                <option value="implemented" {{ request('status') === 'implemented' ? 'selected' : '' }}>Implemented</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <select name="flagged"
                class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                <option value="">All items</option>
                <option value="1" {{ request('flagged') === '1' ? 'selected' : '' }}>Flagged only</option>
                <option value="0" {{ request('flagged') === '0' ? 'selected' : '' }}>Clean only</option>
            </select>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                <i class="fas fa-search text-xs"></i>
                Search
            </button>
            @if(request('search') || request('status') || request()->filled('flagged'))
                <a href="{{ route('administrator.suggestions.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium whitespace-nowrap">
                    <i class="fas fa-times text-xs"></i>
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Stats --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Total</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['total'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Pending</p>
            <p class="text-2xl font-semibold text-amber-600 dark:text-amber-400 tabular-nums">{{ $stats['pending'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Reviewed</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400 tabular-nums">{{ $stats['reviewed'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Flagged</p>
            <p class="text-2xl font-semibold text-red-600 dark:text-red-400 tabular-nums">{{ $stats['flagged'] }}</p>
        </div>
    </div>

    @if($suggestions->count() > 0)
        {{-- Suggestions table --}}
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted by</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Suggestion</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-5 py-3 text-right text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($suggestions as $suggestion)
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                                {{-- Submitter --}}
                                <td class="px-5 py-4 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-xs font-semibold text-white dark:text-gray-900">
                                            @if($suggestion->user?->profile_image)
                                                <img src="{{ Str::startsWith($suggestion->user->profile_image, ['http://', 'https://']) ? $suggestion->user->profile_image : asset($suggestion->user->profile_image) }}"
                                                     alt="{{ $suggestion->user->name }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr($suggestion->user?->name ?? '?', 0, 1)) }}
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $suggestion->user?->name ?? 'Deleted user' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $suggestion->user?->email ?? 'No email' }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Suggestion --}}
                                <td class="px-5 py-4 align-top">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($suggestion->title, 50) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($suggestion->description, 70) }}</p>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 align-top">
                                    <div class="flex flex-col gap-1">
                                        <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider
                                            @if($suggestion->status === 'pending') text-amber-600 dark:text-amber-400
                                            @elseif($suggestion->status === 'reviewed') text-blue-600 dark:text-blue-400
                                            @elseif($suggestion->status === 'implemented') text-emerald-600 dark:text-emerald-400
                                            @else text-red-600 dark:text-red-400
                                            @endif">
                                            ● {{ $suggestion->status }}
                                        </span>
                                        @if($suggestion->flagged)
                                            <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-red-600 dark:text-red-400">
                                                ● Flagged
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Date --}}
                                <td class="px-5 py-4 align-top">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $suggestion->created_at->format('M d, Y') }}</span>
                                </td>

                                                                {{-- Action --}}
                                <td class="px-5 py-4 align-top text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($suggestion->flagged)
                                            <form method="POST" action="{{ route('administrator.suggestions.approve', $suggestion) }}" class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-emerald-500 hover:text-emerald-600 dark:hover:border-emerald-500 dark:hover:text-emerald-400 transition">
                                                    <i class="fas fa-check text-[10px]"></i>
                                                    Approve
                                                </button>
                                            </form>
                                        @endif
                                        <a href="{{ route('administrator.suggestions.show', $suggestion) }}"
                                           class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                                            View
                                            <i class="fas fa-arrow-right text-[10px]"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($suggestions->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $suggestions->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            @if(request('search') || request('status') || request()->filled('flagged'))
                <i class="fas fa-search text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No results found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Try adjusting your search or filters.</p>
                <a href="{{ route('administrator.suggestions.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                    <i class="fas fa-times text-xs"></i>
                    Clear filters
                </a>
            @else
                <i class="fas fa-lightbulb text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No suggestions yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">User feedback will appear here once submitted.</p>
            @endif
        </div>
    @endif
</div>
@endsection