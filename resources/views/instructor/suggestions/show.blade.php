@extends('layouts.app')

@section('title', 'Suggestion Details')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('suggestions.community') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Community
    </a>

    {{-- Suggestion card --}}
    <article class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-8">

        {{-- Status + date + report --}}
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <div class="flex flex-wrap items-center gap-3 min-w-0">
                <span class="text-[10px] font-medium uppercase tracking-wider
                    @if($suggestion->status === 'pending') text-amber-600 dark:text-amber-400
                    @elseif($suggestion->status === 'reviewed') text-blue-600 dark:text-blue-400
                    @elseif($suggestion->status === 'implemented') text-emerald-600 dark:text-emerald-400
                    @else text-red-600 dark:text-red-400
                    @endif">
                    ● {{ $suggestion->status }}
                </span>
                <span class="text-xs text-gray-400 dark:text-gray-600">·</span>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $suggestion->created_at->format('M d, Y') }}</span>
            </div>

            @if(!auth()->user()->isAdministrator() && auth()->id() !== $suggestion->user_id)
                <form method="POST" action="{{ route('report.store') }}" class="inline">
                    @csrf
                    <input type="hidden" name="reportable_type" value="suggestion">
                    <input type="hidden" name="reportable_id" value="{{ $suggestion->id }}">
                    <input type="hidden" name="reason" value="inappropriate">
                    <button type="button"
                        class="inline-flex items-center gap-1.5 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition"
                        title="Report Suggestion"
                        onclick="let reason = prompt('Reason: spam, inappropriate, harassment, other'); if(reason) { this.parentElement.querySelector('[name=reason]').value = reason; this.parentElement.submit(); }">
                        <i class="fas fa-flag text-[11px]"></i>
                        Report
                    </button>
                </form>
            @endif
        </div>

        {{-- Title --}}
        <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-2 break-words">
            {{ $suggestion->title }}
        </h1>

        {{-- Author --}}
        <div class="flex items-center gap-2 mb-6">
            <div class="w-6 h-6 rounded-md bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-[10px] font-semibold text-white dark:text-gray-900">
                @if($suggestion->user?->profile_image)
                    <img src="{{ Str::startsWith($suggestion->user->profile_image, ['http://', 'https://']) ? $suggestion->user->profile_image : asset($suggestion->user->profile_image) }}"
                         alt="{{ $suggestion->user->name }}" class="w-full h-full object-cover">
                @else
                    {{ strtoupper(substr($suggestion->user?->name ?? '?', 0, 1)) }}
                @endif
            </div>
            <p class="text-sm text-gray-500 dark:text-gray-400">by {{ $suggestion->user?->name ?? 'Deleted user' }}</p>
        </div>

        {{-- Description --}}
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Description
        </p>
        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed break-words border border-gray-100 dark:border-gray-800 rounded-lg p-5 bg-gray-50 dark:bg-gray-800/30">
            {{ $suggestion->description }}
        </div>

        {{-- Status change --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Update status
            </p>
            <form method="POST" action="{{ route('instructor.suggestions.status', $suggestion) }}" class="flex flex-wrap items-center gap-3">
                @csrf @method('PUT')
                <select name="status"
                    class="px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                    <option value="pending" {{ $suggestion->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ $suggestion->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="implemented" {{ $suggestion->status === 'implemented' ? 'selected' : '' }}>Implemented</option>
                    <option value="rejected" {{ $suggestion->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                    <i class="fas fa-check text-xs"></i>
                    Update status
                </button>
            </form>
        </div>
    </article>

    {{-- Discussion --}}
    <section>
        <div class="mb-6">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Discussion
            </p>
            <h2 class="text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                {{ $suggestion->comments->count() }} {{ Str::plural('comment', $suggestion->comments->count()) }}
            </h2>
        </div>

        @if($suggestion->comments->count() > 0)
            <div class="space-y-3 mb-8">
                @foreach($suggestion->comments as $comment)
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-8 h-8 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 overflow-hidden text-xs font-semibold text-white dark:text-gray-900">
                                @if($comment->user?->profile_image)
                                    <img src="{{ Str::startsWith($comment->user->profile_image, ['http://', 'https://']) ? $comment->user->profile_image : asset($comment->user->profile_image) }}"
                                         alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr($comment->user?->name ?? '?', 0, 1)) }}
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $comment->user?->name ?? 'Deleted user' }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    @if($comment->created_at != $comment->updated_at)
                                        <span class="text-xs text-gray-400 dark:text-gray-600 italic">edited</span>
                                    @endif
                                    @if($comment->flagged && $comment->user_id === auth()->id())
                                        <span class="text-[10px] font-medium uppercase tracking-wider text-red-600 dark:text-red-400">
                                            ● Hidden — pending review
                                        </span>
                                    @endif

                                    @if(!auth()->user()->isAdministrator() && auth()->id() !== $comment->user_id && !$comment->user?->isAdministrator())
                                        <form method="POST" action="{{ route('report.store') }}" class="inline ml-auto">
                                            @csrf
                                            <input type="hidden" name="reportable_type" value="comment">
                                            <input type="hidden" name="reportable_id" value="{{ $comment->id }}">
                                            <input type="hidden" name="reason" value="inappropriate">
                                            <button type="button"
                                                class="text-gray-400 hover:text-red-500 transition"
                                                title="Report"
                                                onclick="let reason = prompt('Reason: spam, inappropriate, harassment, other'); if(reason) { this.parentElement.querySelector('[name=reason]').value = reason; this.parentElement.submit(); }">
                                                <i class="fas fa-flag text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line break-words">{{ $comment->body }}</p>

                        @if(auth()->id() === $comment->user_id)
                            <div class="mt-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                                <button type="button"
                                    onclick="document.getElementById('edit-{{ $comment->id }}').classList.toggle('hidden')"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
                                    <i class="fas fa-edit text-[10px]"></i>
                                    Edit
                                </button>

                                <form id="edit-{{ $comment->id }}" method="POST"
                                    action="{{ route('instructor.suggestions.comment.update', [$suggestion, $comment]) }}"
                                    class="mt-3 hidden">
                                    @csrf @method('PUT')
                                    <textarea name="body" rows="3" required
                                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition">{{ $comment->body }}</textarea>
                                    <div class="flex gap-2 mt-3">
                                        <button type="submit"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                                            Update
                                        </button>
                                        <button type="button"
                                            onclick="document.getElementById('edit-{{ $comment->id }}').classList.add('hidden')"
                                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

        @php $userComment = $suggestion->comments->where('user_id', auth()->id())->first(); @endphp

        @if($userComment)
            <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    You've already commented.
                    <button type="button"
                        onclick="document.getElementById('edit-my-comment').classList.toggle('hidden')"
                        class="text-gray-900 dark:text-white font-medium hover:underline">
                        Edit your comment
                    </button>
                </p>
                <form id="edit-my-comment" method="POST"
                    action="{{ route('instructor.suggestions.comment.update', [$suggestion, $userComment]) }}"
                    class="hidden">
                    @csrf @method('PUT')
                    <textarea name="body" rows="3" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition">{{ $userComment->body }}</textarea>
                    <div class="flex gap-2 mt-3">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-xs font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                            Update comment
                        </button>
                        <button type="button"
                            onclick="document.getElementById('edit-my-comment').classList.add('hidden')"
                            class="inline-flex items-center gap-2 px-4 py-2 border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @else
            <form method="POST" action="{{ route('instructor.suggestions.comment.store', $suggestion) }}">
                @csrf
                <textarea name="body" rows="3" required placeholder="Write a comment..."
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition"></textarea>
                <button type="submit"
                    class="mt-3 inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    <i class="fas fa-paper-plane text-xs"></i>
                    Post comment
                </button>
            </form>
        @endif
    </section>
</div>
@endsection