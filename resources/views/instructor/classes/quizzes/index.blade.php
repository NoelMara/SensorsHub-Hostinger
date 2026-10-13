@extends('layouts.app')

@section('title', 'Quizzes - ' . $class->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Class
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Quizzes · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Quizzes
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-8">
            {{ $quizzes->total() }} {{ Str::plural('quiz', $quizzes->total()) }} in this class.
        </p>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('instructor.classes.quizzes.create', $class) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-plus text-xs"></i>
                Add quiz
            </a>
            <a href="{{ route('instructor.classes.quizzes.import', $class) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                <i class="fas fa-download text-xs"></i>
                Import
            </a>
        </div>
    </div>

    @if($quizzes->count() > 0)
        <div class="space-y-3">
            @foreach($quizzes as $index => $quiz)
                @php $isOverdue = $quiz->due_date && $quiz->due_date->isPast(); @endphp
                <article class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">

                    {{-- Top: number + title + status --}}
                    <div class="flex items-start gap-3 mb-3">
                        <span class="w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-xs font-semibold text-gray-600 dark:text-gray-400">
                            {{ $index + 1 }}
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                                <h3 class="text-base font-medium text-gray-900 dark:text-white break-words min-w-0">
                                    {{ Str::limit($quiz->title, 60) }}
                                </h3>
                                @if($quiz->is_published)
                                    <span class="text-[10px] font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-400 flex-shrink-0">
                                        ● Published
                                    </span>
                                @else
                                    <span class="text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 flex-shrink-0">
                                        ● Draft
                                    </span>
                                @endif
                                @if($isOverdue)
                                    <span class="text-[10px] font-medium uppercase tracking-wider text-red-600 dark:text-red-400 flex-shrink-0">
                                        ● Overdue
                                    </span>
                                @endif
                            </div>

                            {{-- Meta line --}}
                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400 mt-2">
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-star text-[10px]"></i>
                                    {{ $quiz->points }} pts
                                </span>
                                <span class="text-gray-300 dark:text-gray-700">·</span>
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-check text-[10px]"></i>
                                    Pass: {{ $quiz->passing_score }}%
                                </span>
                                <span class="text-gray-300 dark:text-gray-700">·</span>
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-question-circle text-[10px]"></i>
                                    {{ $quiz->questions->count() }} {{ Str::plural('question', $quiz->questions->count()) }}
                                </span>
                                <span class="text-gray-300 dark:text-gray-700">·</span>
                                <span class="inline-flex items-center gap-1">
                                    <i class="fas fa-users text-[10px]"></i>
                                    {{ $quiz->submissions->count() }} {{ Str::plural('submission', $quiz->submissions->count()) }}
                                </span>
                                @if($quiz->due_date)
                                    <span class="text-gray-300 dark:text-gray-700">·</span>
                                    <span class="inline-flex items-center gap-1">
                                        <i class="fas fa-clock text-[10px]"></i>
                                        Due {{ $quiz->due_date->format('M d') }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Actions footer --}}
                    <div class="grid grid-cols-2 sm:flex sm:items-center sm:justify-end gap-2 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <a href="{{ route('instructor.classes.quizzes.show', [$class, $quiz]) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3 h-9 sm:h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                            <i class="fas fa-eye text-[10px]"></i>
                            Preview
                        </a>
                        <a href="{{ route('instructor.classes.quizzes.edit', [$class, $quiz]) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3 h-9 sm:h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                            <i class="fas fa-edit text-[10px]"></i>
                            Edit
                        </a>
                        <a href="{{ route('instructor.classes.quizzes.submissions', [$class, $quiz]) }}"
                           class="inline-flex items-center justify-center gap-1.5 px-3 h-9 sm:h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                            <i class="fas fa-users text-[10px]"></i>
                            Submissions
                        </a>
                        <form action="{{ route('instructor.classes.quizzes.destroy', [$class, $quiz]) }}"
                            method="POST" onsubmit="return confirm('Delete this quiz?');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-1.5 px-3 h-9 sm:h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                                <i class="fas fa-trash text-[10px]"></i>
                                Delete
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        @if($quizzes->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $quizzes->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-question-circle text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No quizzes yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Use the buttons above to add your first quiz or import one.</p>
        </div>
    @endif
</div>
@endsection