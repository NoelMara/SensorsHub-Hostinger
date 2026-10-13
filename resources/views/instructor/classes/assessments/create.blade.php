@extends('layouts.app')

@section('title', 'Add Assessment')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.assessments.index', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Assessments
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor · New
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Add assessment
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Create a new assessment for {{ $class->name }}.
        </p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('instructor.classes.assessments.store', $class) }}">
        @csrf

        <div class="space-y-6">

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Assessment title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    value="{{ old('title') }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('title') !border-red-500 @enderror"
                    placeholder="e.g., Sensor Wiring Practice">
                @error('title') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Description <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                </label>
                <textarea name="description" id="description" rows="3"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('description') !border-red-500 @enderror"
                    placeholder="Brief description of the assessment...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Instructions --}}
            <div>
                <label for="instructions" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Instructions <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                </label>
                <textarea name="instructions" id="instructions" rows="5"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('instructions') !border-red-500 @enderror"
                    placeholder="Step-by-step instructions for students...">{{ old('instructions') }}</textarea>
                @error('instructions') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Points + Due date --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="points" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Points <span class="text-red-500">*</span>
                    </label>
                    <input type="number" name="points" id="points" required min="1"
                        value="{{ old('points', 10) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm @error('points') !border-red-500 @enderror">
                    @error('points') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="due_date" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Due date <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                    </label>
                    <input type="datetime-local" name="due_date" id="due_date" value="{{ old('due_date') }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm @error('due_date') !border-red-500 @enderror">
                    @error('due_date') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Publish toggle --}}
            <label for="is_published" class="flex items-start gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-gray-400 dark:hover:border-gray-600 transition">
                <input type="checkbox" name="is_published" id="is_published" value="1"
                    {{ old('is_published', true) ? 'checked' : '' }}
                    class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Publish immediately</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">When checked, students will see this assessment right away. Uncheck to save as draft.</p>
                </div>
            </label>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('instructor.classes.assessments.index', $class) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-save text-xs"></i>
                Submit assessment
            </button>
        </div>
    </form>
</div>
@endsection