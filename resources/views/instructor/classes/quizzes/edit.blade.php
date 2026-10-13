@extends('layouts.app')

@section('title', 'Edit Quiz')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.quizzes.index', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Quizzes
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor · Edit
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Edit quiz
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Update this quiz for {{ $class->name }}.
        </p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('instructor.classes.quizzes.update', [$class, $quiz]) }}"
        x-data="quizEditor({{ json_encode($quiz->questions->map(function($q) { return ['id' => $q->id, 'text' => $q->question, 'options' => $q->options->map(function($o) { return ['id' => $o->id, 'text' => $o->option_text, 'isCorrect' => $o->is_correct]; })->values()]; })->values()) }})"
        @submit="checkAnswers($event)">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Quiz title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    value="{{ old('title', $quiz->title) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Description <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                </label>
                <textarea name="description" id="description" rows="2"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none">{{ old('description', $quiz->description) }}</textarea>
            </div>

            {{-- Instructions --}}
            <div>
                <label for="instructions" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Instructions <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                </label>
                <textarea name="instructions" id="instructions" rows="2"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none">{{ old('instructions', $quiz->instructions) }}</textarea>
            </div>

            {{-- Settings grid --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div>
                    <label for="points" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Points <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="points" id="points" required min="1"
                        value="{{ old('points', $quiz->points) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                </div>
                <div>
                    <label for="passing_score" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Passing score (%) <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="passing_score" id="passing_score" required min="0" max="100"
                        value="{{ old('passing_score', $quiz->passing_score) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                </div>
                <div>
                    <label for="due_date" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Due date <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                    </label>
                    <input type="datetime-local" name="due_date" id="due_date"
                        value="{{ old('due_date', $quiz->due_date ? $quiz->due_date->format('Y-m-d\TH:i') : '') }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                </div>
                <div>
                    <label for="time_limit" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Time limit <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(min)</span>
                    </label>
                    <input type="number" name="time_limit" id="time_limit" min="1" max="600"
                        value="{{ old('time_limit', $quiz->time_limit) }}"
                        placeholder="Blank = untimed"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
                </div>
            </div>

            {{-- Publish toggle --}}
            <label for="is_published" class="flex items-start gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-gray-400 dark:hover:border-gray-600 transition">
                <input type="checkbox" name="is_published" id="is_published" value="1"
                    {{ $quiz->is_published ? 'checked' : '' }}
                    class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Publish this quiz</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">When checked, students will see it. Uncheck to save as draft.</p>
                </div>
            </label>

            {{-- Questions section --}}
            <div class="pt-6 border-t border-gray-100 dark:border-gray-800">
                <div class="mb-6">
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Questions
                    </p>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Edit questions
                    </h2>
                </div>

                {{-- Hint --}}
                <div class="border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-950/20 rounded-lg p-4 mb-6 flex items-start gap-3">
                    <i class="fas fa-info-circle text-amber-600 dark:text-amber-400 text-sm mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-amber-800 dark:text-amber-300 leading-relaxed">
                        Select the radio button next to the <strong>correct answer</strong> for each question. You can add up to 6 options per question.
                    </p>
                </div>

                {{-- Questions list --}}
                <template x-for="(question, qIndex) in questions" :key="qIndex">
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 mb-4">

                        {{-- Question header --}}
                        <div class="flex items-center justify-between gap-3 mb-4">
                            <span class="text-xs font-semibold text-gray-400 dark:text-gray-600 tabular-nums">
                                Q<span x-text="qIndex + 1"></span>
                            </span>
                            <button type="button" @click="removeQuestion(qIndex)"
                                class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition"
                                x-show="questions.length > 1">
                                <i class="fas fa-trash text-[10px]"></i>
                                Remove
                            </button>
                        </div>

                        {{-- Hidden IDs --}}
                        <input type="hidden" :name="'questions[' + qIndex + '][id]'" x-model="question.id">

                        {{-- Question text --}}
                        <input type="text" :name="'questions[' + qIndex + '][question]'" required
                            class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm mb-4"
                            placeholder="Enter your question..." x-model="question.text">

                        {{-- Options --}}
                        <div class="space-y-2 mb-4">
                            <template x-for="(option, oIndex) in question.options" :key="oIndex">
                                <div class="flex items-center gap-3">
                                    <input type="radio" :name="'questions[' + qIndex + '][correct_option]'"
                                        :value="oIndex" :checked="option.isCorrect"
                                        @change="setCorrect(qIndex, oIndex)"
                                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 flex-shrink-0 cursor-pointer">
                                    <input type="hidden" :name="'questions[' + qIndex + '][options][' + oIndex + '][id]'" x-model="option.id">
                                    <input type="text" :name="'questions[' + qIndex + '][options][' + oIndex + '][text]'" required
                                        class="flex-1 px-3 py-2.5 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm"
                                        :placeholder="'Option ' + (oIndex + 1)" x-model="option.text">
                                    <input type="hidden" :name="'questions[' + qIndex + '][options][' + oIndex + '][is_correct]'"
                                        :value="option.isCorrect ? '1' : '0'">
                                    <button type="button" @click="removeOption(qIndex, oIndex)"
                                        class="p-1.5 text-gray-400 hover:text-red-500 transition flex-shrink-0"
                                        x-show="question.options.length > 2"
                                        title="Remove option">
                                        <i class="fas fa-times text-xs"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        {{-- Add option --}}
                        <button type="button" @click="addOption(qIndex)"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition"
                            x-show="question.options.length < 6">
                            <i class="fas fa-plus text-[10px]"></i>
                            Add option
                        </button>
                    </div>
                </template>

                {{-- Add question --}}
                <button type="button" @click="addQuestion()"
                    class="w-full py-4 border border-dashed border-gray-300 dark:border-gray-700 rounded-lg text-sm font-medium text-gray-500 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                    <i class="fas fa-plus mr-1.5 text-xs"></i>
                    Add question
                </button>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('instructor.classes.quizzes.index', $class) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-save text-xs"></i>
                Save changes
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function quizEditor(existingQuestions) {
        return {
            questions: existingQuestions && existingQuestions.length > 0 ? existingQuestions : [{
                id: null,
                text: '',
                options: [
                    { id: null, text: '', isCorrect: false },
                    { id: null, text: '', isCorrect: false },
                    { id: null, text: '', isCorrect: false },
                    { id: null, text: '', isCorrect: false },
                ]
            }],

            addQuestion() {
                this.questions.push({
                    id: null,
                    text: '',
                    options: [
                        { id: null, text: '', isCorrect: false },
                        { id: null, text: '', isCorrect: false },
                        { id: null, text: '', isCorrect: false },
                        { id: null, text: '', isCorrect: false },
                    ]
                });
            },

            removeQuestion(index) {
                this.questions.splice(index, 1);
            },

            addOption(qIndex) {
                this.questions[qIndex].options.push({ id: null, text: '', isCorrect: false });
            },

            removeOption(qIndex, oIndex) {
                this.questions[qIndex].options.splice(oIndex, 1);
            },

            setCorrect(qIndex, oIndex) {
                this.questions[qIndex].options.forEach((opt, i) => {
                    opt.isCorrect = (i === oIndex);
                });
            },

            checkAnswers(e) {
                // 1. Validate every question has a correct answer marked
                for (let i = 0; i < this.questions.length; i++) {
                    if (!this.questions[i].options.some(o => o.isCorrect)) {
                        e.preventDefault();
                        alert('Please mark a correct answer for Question ' + (i + 1) + '.');
                        return;
                    }
                }

                // 2. All good — disable button + show "Saving..."
                const btn = e.target.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.classList.add('opacity-70', 'pointer-events-none');
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Saving...';
                }
            },
        }
    }
</script>
@endpush