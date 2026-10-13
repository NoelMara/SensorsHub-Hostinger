@extends('layouts.app')

@section('title', 'Feedback · ' . ($feedback->user->name ?? 'Deleted'))

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('administrator.feedback.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Feedback
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Feedback
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4 break-words">
            Feedback detail
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400">
            Submitted {{ $feedback->created_at->format('M d, Y · h:i A') }}
        </p>
    </div>

    @php
        $typeLabels = ['bug' => 'Bug', 'feature' => 'Feature', 'other' => 'Other'];
        $typeColors = [
            'bug' => 'text-red-600 dark:text-red-400 border-red-200 dark:border-red-900 bg-red-50 dark:bg-red-950/20',
            'feature' => 'text-blue-600 dark:text-blue-400 border-blue-200 dark:border-blue-900 bg-blue-50 dark:bg-blue-950/20',
            'other' => 'text-gray-600 dark:text-gray-400 border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50',
        ];
        $statusColors = [
            'new' => 'text-blue-600 dark:text-blue-400',
            'read' => 'text-gray-500 dark:text-gray-400',
            'resolved' => 'text-emerald-600 dark:text-emerald-400',
            'wont_fix' => 'text-red-600 dark:text-red-400',
        ];
    @endphp

    {{-- Meta row --}}
    <div class="flex flex-wrap items-center gap-3 mb-8">
        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg border text-xs font-medium uppercase tracking-wider {{ $typeColors[$feedback->type] ?? '' }}">
            <i class="fas {{ $feedback->type === 'bug' ? 'fa-bug' : ($feedback->type === 'feature' ? 'fa-lightbulb' : 'fa-comment') }} text-[10px]"></i>
            {{ $typeLabels[$feedback->type] ?? $feedback->type }}
        </span>
        <span class="text-[10px] font-medium uppercase tracking-wider {{ $statusColors[$feedback->status] ?? '' }}">
            ● {{ str_replace('_', ' ', $feedback->status) }}
        </span>
    </div>

    {{-- Submitter card --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 mb-8">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-4">
            Submitted by
        </p>
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 text-base font-semibold text-white dark:text-gray-900">
                {{ strtoupper(substr($feedback->user->name ?? '?', 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-base font-medium text-gray-900 dark:text-white truncate">{{ $feedback->user->name ?? 'Deleted user' }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ $feedback->user->email ?? 'No email' }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-600 mt-0.5 uppercase tracking-wider">Role: {{ $feedback->user_role ?? '—' }}</p>
            </div>
        </div>
    </section>

    {{-- Message --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-8">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-4">
            Message
        </p>
        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line break-words">
            {{ $feedback->message }}
        </p>
    </section>

    {{-- Context --}}
    @if($feedback->page_url)
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 mb-8 bg-gray-50 dark:bg-gray-900/50">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Reported from page
            </p>
            <a href="{{ $feedback->page_url }}" target="_blank"
               class="inline-flex items-center gap-2 text-sm text-blue-600 dark:text-blue-400 hover:underline break-all">
                <i class="fas fa-link text-xs"></i>
                {{ $feedback->page_url }}
            </a>
        </section>
    @endif

    {{-- Admin note --}}
    @if($feedback->admin_note)
        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 mb-8">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Admin note
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                {{ $feedback->admin_note }}
            </p>
        </section>
    @endif

    {{-- Status update --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-4">
            Update status
        </p>
        <form method="POST" action="{{ route('administrator.feedback.status', $feedback) }}">
            @csrf @method('PUT')
            <div class="flex flex-col sm:flex-row gap-3">
                <select name="status"
                    class="flex-1 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                    <option value="new" {{ $feedback->status === 'new' ? 'selected' : '' }}>New</option>
                    <option value="read" {{ $feedback->status === 'read' ? 'selected' : '' }}>Read</option>
                    <option value="resolved" {{ $feedback->status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                    <option value="wont_fix" {{ $feedback->status === 'wont_fix' ? 'selected' : '' }}>Won't fix</option>
                </select>
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                    <i class="fas fa-save text-xs"></i>
                    Update
                </button>
            </div>
        </form>
    </section>

        {{-- Delete --}}
    <section class="border border-red-200 dark:border-red-900 rounded-lg p-6 mt-8">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-red-600 dark:text-red-400 mb-4">
            Danger zone
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Permanently delete this feedback. This cannot be undone.
        </p>
        <form method="POST" action="{{ route('administrator.feedback.destroy', $feedback) }}"
              onsubmit="return confirm('Delete this feedback permanently?');">
            @csrf @method('DELETE')
            <button type="submit"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-lg border border-red-500 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/20 transition text-sm font-medium">
                <i class="fas fa-trash text-xs"></i>
                Delete feedback
            </button>
        </form>
    </section>

    {{-- Back --}}
    <div class="mt-12 pt-8 border-t border-gray-100 dark:border-gray-800">
        <a href="{{ route('administrator.feedback.index') }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to Feedback
        </a>
    </div>
</div>
@endsection