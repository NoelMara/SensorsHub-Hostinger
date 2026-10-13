@extends('layouts.app')

@section('title', 'Add Project')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route(($prefix ?? 'administrator') . '.projects.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Projects
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · New project
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Add new project
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Create a new guided project tutorial.
        </p>
    </div>

    {{-- Form --}}
    <form method="POST" action="{{ route('administrator.content.store', 'projects') }}">
        @csrf

        <div class="space-y-6">

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    value="{{ old('title') }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('title') !border-red-500 @enderror"
                    placeholder="e.g., Temperature Sensor Display">
                @error('title') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="3" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('description') !border-red-500 @enderror"
                    placeholder="Brief overview of what the project does...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Sensor + Difficulty --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="sensor_id" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Sensor <span class="text-red-500">*</span>
                    </label>
                    <select name="sensor_id" id="sensor_id" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm @error('sensor_id') !border-red-500 @enderror">
                        @foreach($sensors as $sensor)
                            <option value="{{ $sensor->id }}" {{ old('sensor_id') == $sensor->id ? 'selected' : '' }}>{{ $sensor->name }}</option>
                        @endforeach
                    </select>
                    @error('sensor_id') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="difficulty" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Difficulty <span class="text-red-500">*</span>
                    </label>
                    <select name="difficulty" id="difficulty" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition text-sm @error('difficulty') !border-red-500 @enderror">
                        <option value="Beginner" {{ old('difficulty') == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                        <option value="Intermediate" {{ old('difficulty') == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                        <option value="Advanced" {{ old('difficulty') == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                    </select>
                    @error('difficulty') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Components --}}
            <div>
                <label for="components_needed" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Components needed <span class="text-red-500">*</span>
                </label>
                <textarea name="components_needed" id="components_needed" rows="3" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('components_needed') !border-red-500 @enderror"
                    placeholder="Arduino Uno, LED, Jumper Wires...">{{ old('components_needed') }}</textarea>
                @error('components_needed') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Instructions --}}
            <div>
                <label for="instructions" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Instructions <span class="text-red-500">*</span>
                </label>
                <textarea name="instructions" id="instructions" rows="6" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('instructions') !border-red-500 @enderror"
                    placeholder="Step-by-step instructions...">{{ old('instructions') }}</textarea>
                @error('instructions') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Code --}}
            <div>
                <label for="code" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Code <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                </label>
                <textarea name="code" id="code" rows="8"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm font-mono resize-none leading-relaxed @error('code') !border-red-500 @enderror"
                    placeholder="// Paste your code here...">{{ old('code') }}</textarea>
                @error('code') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Toggles --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <label for="is_active" class="flex items-start gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-gray-400 dark:hover:border-gray-600 transition">
                    <input type="checkbox" name="is_active" id="is_active" value="1" checked
                        class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Active</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Visible to users</p>
                    </div>
                </label>
                <label for="is_featured" class="flex items-start gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-gray-400 dark:hover:border-gray-600 transition">
                    <input type="checkbox" name="is_featured" id="is_featured" value="1"
                        {{ old('is_featured') ? 'checked' : '' }}
                        class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-amber-500 focus:ring-amber-500 cursor-pointer">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Featured</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Highlight on the homepage</p>
                    </div>
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route(($prefix ?? 'administrator') . '.projects.index') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                Cancel
            </a>
            <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-save text-xs"></i>
                Create project
            </button>
        </div>
    </form>
</div>
@endsection