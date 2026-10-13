@extends('layouts.app')

@section('title', 'Suggestions')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Suggestions
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Review user feedback and track status.
        </p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Total</p>
            <p class="text-3xl font-semibold text-gray-900 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Pending</p>
            <p class="text-3xl font-semibold text-amber-600 dark:text-amber-400">{{ $stats['pending'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Reviewed</p>
            <p class="text-3xl font-semibold text-blue-600 dark:text-blue-400">{{ $stats['reviewed'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
            <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-2">Implemented</p>
            <p class="text-3xl font-semibold text-emerald-600 dark:text-emerald-400">{{ $stats['implemented'] }}</p>
        </div>
    </div>

    @if($suggestions->count() > 0)
        {{-- Suggestions list --}}
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
                                <td class="px-5 py-4 align-top">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ Str::limit($suggestion->title, 50) }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ Str::limit($suggestion->description, 70) }}</p>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <span class="text-[10px] font-medium uppercase tracking-wider
                                        @if($suggestion->status === 'pending') text-amber-600 dark:text-amber-400
                                        @elseif($suggestion->status === 'reviewed') text-blue-600 dark:text-blue-400
                                        @elseif($suggestion->status === 'implemented') text-emerald-600 dark:text-emerald-400
                                        @else text-red-600 dark:text-red-400
                                        @endif">
                                        ● {{ $suggestion->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $suggestion->created_at->format('M d, Y') }}</span>
                                </td>
                                <td class="px-5 py-4 align-top text-right">
                                    <a href="{{ route('instructor.suggestions.show', $suggestion) }}"
                                       class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
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

        @if($suggestions->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $suggestions->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-lightbulb text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No suggestions yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">User feedback will appear here once submitted.</p>
        </div>
    @endif
</div>
@endsection