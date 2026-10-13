@extends('layouts.app')

@section('title', 'Submissions - ' . $assessment->title)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.assessments.index', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Assessments
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Submissions · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4 break-words">
            {{ $assessment->title }}
        </h1>

        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-users text-xs"></i>
                {{ $submissions->count() }} {{ Str::plural('submission', $submissions->count()) }}
            </span>
            <span class="text-gray-300 dark:text-gray-700">·</span>
            <span class="inline-flex items-center gap-1.5">
                <i class="fas fa-star text-xs"></i>
                {{ $assessment->points }} points
            </span>
        </div>
    </div>

    @if($submissions->count() > 0)
        <div class="space-y-4">
            @foreach($submissions as $submission)
                <article class="border border-gray-200 dark:border-gray-800 rounded-lg">

                    {{-- Header: student + status --}}
                    <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0 flex-1">
                            <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 text-sm font-semibold text-white dark:text-gray-900">
                                {{ strtoupper(substr($submission->user->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $submission->user->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ $submission->submitted_at->format('M d, Y · h:i A') }}</p>
                            </div>
                        </div>

                        @if($submission->score !== null)
                            <span class="text-sm font-semibold tabular-nums text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                                {{ $submission->score }}/{{ $assessment->points }}
                            </span>
                        @else
                            <span class="text-[10px] font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400 flex-shrink-0">
                                ● Pending
                            </span>
                        @endif
                    </div>

                    {{-- Content + grading --}}
                    <div class="p-5">

                        {{-- Submitted content --}}
                        <div class="relative mb-4">
                            <button type="button" onclick="copySubmission('{{ $submission->id }}')"
                                class="absolute top-3 right-3 inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition z-10">
                                <i class="fas fa-copy text-[10px]"></i>
                                Copy
                            </button>
                            <pre id="submission-{{ $submission->id }}"
                                 class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap font-mono bg-gray-50 dark:bg-gray-900/50 border border-gray-100 dark:border-gray-800 rounded-lg p-4 pr-24 leading-relaxed overflow-x-auto">{{ $submission->content }}</pre>
                        </div>

                        {{-- Grading form --}}
                        <form method="POST" action="{{ route('instructor.classes.assessments.grade', [$class, $assessment, $submission]) }}"
                            class="pt-4 border-t border-gray-100 dark:border-gray-800">
                            @csrf

                            {{-- Mobile: stacked; Desktop: single row --}}
                            <div class="flex flex-col sm:flex-row sm:items-end gap-4">

                                <div class="flex-1">
                                    <label for="score-{{ $submission->id }}" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                        Score <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(max {{ $assessment->points }})</span>
                                    </label>
                                    <input type="number" name="score" id="score-{{ $submission->id }}"
                                        value="{{ $submission->score }}" required min="0" max="{{ $assessment->points }}"
                                        class="w-full sm:w-24 px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm tabular-nums">
                                </div>

                                <div class="flex-[2]">
                                    <label for="feedback-{{ $submission->id }}" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                                        Feedback <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                                    </label>
                                    <input type="text" name="feedback" id="feedback-{{ $submission->id }}"
                                        value="{{ $submission->feedback }}" placeholder="Optional feedback..."
                                        class="w-full px-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
                                </div>

                                <button type="submit"
                                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                                    <i class="fas fa-check text-xs"></i>
                                    Save grade
                                </button>
                            </div>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-users text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No submissions yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Wait for students to submit their work.</p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function copySubmission(id) {
        const el = document.getElementById('submission-' + id);
        if (!el) return;
        const content = el.innerText;
        const btn = el.parentElement.querySelector('button');

        const textarea = document.createElement('textarea');
        textarea.value = content;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        textarea.setSelectionRange(0, 99999);
        document.execCommand('copy');
        document.body.removeChild(textarea);

        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check text-[10px]"></i> Copied';
        setTimeout(() => { btn.innerHTML = originalHTML; }, 2000);
    }
</script>
@endpush