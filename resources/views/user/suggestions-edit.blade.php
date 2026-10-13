@extends('layouts.app')

@section('title', 'Edit Suggestion')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('dashboard.suggestions') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to My Suggestions
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Edit
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Edit suggestion
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            You can only edit suggestions that are still <span class="text-amber-600 dark:text-amber-400 font-medium">pending</span> review.
        </p>
    </div>

    {{-- Validation errors --}}
    @if($errors->any())
        <div class="border border-red-200 dark:border-red-900/40 bg-red-50 dark:bg-red-950/20 rounded-lg p-5 mb-8">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-red-600 dark:text-red-400 mb-2">
                Please fix the following
            </p>
            <ul class="list-disc list-inside text-sm text-red-700 dark:text-red-300 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('dashboard.suggestions.update', $suggestion) }}">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Project title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    value="{{ old('title', $suggestion->title) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="6" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none"
                    placeholder="Describe your project idea in detail...">{{ old('description', $suggestion->description) }}</textarea>
            </div>

            {{-- Difficulty + Sensor Type --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="difficulty" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Difficulty
                    </label>
                    <select name="difficulty" id="difficulty"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm">
                        <option value="">Select difficulty</option>
                        <option value="Beginner"     {{ old('difficulty', $suggestion->difficulty) == 'Beginner'     ? 'selected' : '' }}>Beginner</option>
                        <option value="Intermediate" {{ old('difficulty', $suggestion->difficulty) == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Advanced"     {{ old('difficulty', $suggestion->difficulty) == 'Advanced'     ? 'selected' : '' }}>Advanced</option>
                    </select>
                </div>
                <div>
                    <label for="sensor_type" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Sensor type
                    </label>
                    <input type="text" name="sensor_type" id="sensor_type"
                        value="{{ old('sensor_type', $suggestion->sensor_type) }}"
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm"
                        placeholder="e.g., DHT11, HC-SR04">
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800 flex items-center justify-end gap-3">
            <a href="{{ route('dashboard.suggestions') }}"
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