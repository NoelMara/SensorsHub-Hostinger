@extends('layouts.app')

@section('title', 'Feedback')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Feedback
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            User feedback
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Bugs, feature requests, and other messages from students and instructors.
        </p>
    </div>

    {{-- Search & filter --}}
    <form method="GET" action="{{ route('administrator.feedback.index') }}" class="mb-8">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by message or user..."
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            </div>
            <select name="type"
                class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                <option value="">All types</option>
                <option value="bug" {{ request('type') === 'bug' ? 'selected' : '' }}>Bug</option>
                <option value="feature" {{ request('type') === 'feature' ? 'selected' : '' }}>Feature</option>
                <option value="other" {{ request('type') === 'other' ? 'selected' : '' }}>Other</option>
            </select>
            <select name="status"
                class="px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                <option value="">All status</option>
                <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
                <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>Read</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="wont_fix" {{ request('status') === 'wont_fix' ? 'selected' : '' }}>Won't fix</option>
            </select>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                <i class="fas fa-search text-xs"></i>
                Search
            </button>
            @if(request('search') || request('type') || request('status'))
                <a href="{{ route('administrator.feedback.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium whitespace-nowrap">
                    <i class="fas fa-times text-xs"></i>
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Total</p>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $counts['all'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">New</p>
            <p class="text-2xl font-semibold text-blue-600 dark:text-blue-400 tabular-nums">{{ $counts['new'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Read</p>
            <p class="text-2xl font-semibold text-gray-500 dark:text-gray-400 tabular-nums">{{ $counts['read'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Resolved</p>
            <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $counts['resolved'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Won't fix</p>
            <p class="text-2xl font-semibold text-red-600 dark:text-red-400 tabular-nums">{{ $counts['wont_fix'] }}</p>
        </div>
    </div>

    @if($feedback->count() > 0)
        {{-- Feedback table --}}
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Submitted by</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Feedback</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Type</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Date</th>
                            <th class="px-5 py-3 text-right text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($feedback as $item)
                            @php
                                $typeLabels = ['bug' => 'Bug', 'feature' => 'Feature', 'other' => 'Other'];
                                $typeColors = [
                                    'bug' => 'text-red-600 dark:text-red-400',
                                    'feature' => 'text-blue-600 dark:text-blue-400',
                                    'other' => 'text-gray-500 dark:text-gray-400',
                                ];
                                $statusColors = [
                                    'new' => 'text-blue-600 dark:text-blue-400',
                                    'read' => 'text-gray-500 dark:text-gray-400',
                                    'resolved' => 'text-emerald-600 dark:text-emerald-400',
                                    'wont_fix' => 'text-red-600 dark:text-red-400',
                                ];
                            @endphp
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                                {{-- Submitter --}}
                                <td class="px-5 py-4 align-top">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 text-xs font-semibold text-white dark:text-gray-900">
                                            {{ strtoupper(substr($item->user?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->user?->name ?? 'Deleted user' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                {{ $item->user_role ?? '—' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Feedback --}}
                                <td class="px-5 py-4 align-top max-w-xs">
                                    <p class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ Str::limit($item->message, 60) }}</p>
                                    @if($item->page_url)
                                        <p class="text-xs text-gray-400 dark:text-gray-600 mt-1 truncate">
                                            <i class="fas fa-link text-[10px] mr-1"></i>
                                            {{ $item->page_url }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Type --}}
                                <td class="px-5 py-4 align-top">
                                    <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider {{ $typeColors[$item->type] ?? '' }}">
                                        ● {{ $typeLabels[$item->type] ?? $item->type }}
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 align-top">
                                    <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider {{ $statusColors[$item->status] ?? '' }}">
                                        ● {{ str_replace('_', ' ', $item->status) }}
                                    </span>
                                </td>

                                {{-- Date --}}
                                <td class="px-5 py-4 align-top">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item->created_at->format('M d, Y') }}</span>
                                </td>

                                {{-- Action --}}
                                <td class="px-5 py-4 align-top text-right">
                                    <a href="{{ route('administrator.feedback.show', $item) }}"
                                       class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition whitespace-nowrap">
                                        View
                                        <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($feedback->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $feedback->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            @if(request('search') || request('type') || request('status'))
                <i class="fas fa-search text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No results found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Try adjusting your search or filters.</p>
                <a href="{{ route('administrator.feedback.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                    <i class="fas fa-times text-xs"></i>
                    Clear filters
                </a>
            @else
                <i class="fas fa-comment-dots text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No feedback yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">When users send feedback, it'll appear here.</p>
            @endif
        </div>
    @endif
</div>
@endsection