@extends('layouts.app')

@section('title', 'Suggestion Details')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('administrator.suggestions.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Suggestions
    </a>

    {{-- Suggestion card --}}
    <article class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-8">

        {{-- Status + date + delete --}}
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

            <form method="POST" action="{{ route('administrator.suggestions.destroy', $suggestion) }}"
                onsubmit="return confirm('Delete this suggestion permanently?');" class="inline">
                @csrf @method('DELETE')
                <button type="submit"
                    class="inline-flex items-center gap-1.5 px-3 h-9 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                    <i class="fas fa-trash text-[11px]"></i>
                    Delete
                </button>
            </form>
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
            <p class="text-sm text-gray-500 dark:text-gray-400">by
                @if($suggestion->user)
                    <a href="{{ route('administrator.users.show', $suggestion->user) }}"
                    class="text-gray-900 dark:text-white font-medium hover:underline">
                        {{ $suggestion->user->name }}
                    </a>
                @else
                    Deleted user
                @endif
            </p>
        </div>

        {{-- Description --}}
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Description
        </p>
        <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line leading-relaxed break-words border border-gray-100 dark:border-gray-800 rounded-lg p-5 bg-gray-50 dark:bg-gray-800/30">
            {{ $suggestion->description }}
        </div>

        {{-- Status + notes update --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800">
            <form method="POST" action="{{ route('administrator.suggestions.status', $suggestion) }}">
                @csrf @method('PUT')

                <div class="space-y-5">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Update status
                        </p>
                        <select name="status"
                            class="w-full sm:w-48 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                            <option value="pending" {{ $suggestion->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="reviewed" {{ $suggestion->status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                            <option value="implemented" {{ $suggestion->status === 'implemented' ? 'selected' : '' }}>Implemented</option>
                            <option value="rejected" {{ $suggestion->status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <div>
                        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                            Administrator notes
                        </p>
                        <textarea name="admin_notes" rows="4"
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition resize-none leading-relaxed"
                            placeholder="Add notes for this suggestion...">{{ $suggestion->admin_notes }}</textarea>
                    </div>

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                            <i class="fas fa-check text-xs"></i>
                            Save changes
                        </button>
                    </div>
                </div>
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
                                    @if($comment->user)
                                        <a href="{{ route('administrator.users.show', $comment->user) }}"
                                        class="text-sm font-medium text-gray-900 dark:text-white hover:underline">
                                            {{ $comment->user->name }}
                                        </a>
                                    @else
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">
                                            Deleted user
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                    @if($comment->flagged)
                                        <span class="text-[10px] font-medium uppercase tracking-wider text-red-600 dark:text-red-400">
                                            ● Flagged
                                        </span>
                                    @endif
                                    @if($comment->created_at != $comment->updated_at)
                                        <span class="text-xs text-gray-400 dark:text-gray-600 italic">edited</span>
                                    @endif

                                    <form method="POST" action="{{ route('administrator.suggestions.comment.destroy', [$suggestion, $comment]) }}"
                                        onsubmit="return confirm('Delete this comment?');" class="inline ml-auto">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-gray-400 hover:text-red-500 transition"
                                            title="Delete comment">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
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
                                    action="{{ route('administrator.suggestions.comment.update', [$suggestion, $comment]) }}"
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
        @else
            <div class="text-center py-12 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg mb-8">
                <i class="fas fa-comments text-3xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet.</p>
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
                    action="{{ route('administrator.suggestions.comment.update', [$suggestion, $userComment]) }}"
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
            <form method="POST" action="{{ route('administrator.suggestions.comment.store', $suggestion) }}">
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