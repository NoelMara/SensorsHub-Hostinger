@extends('layouts.app')

@section('title', 'Edit Sensor')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route(($prefix ?? 'administrator') . '.sensors.index') }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Sensors
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Edit sensor
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Edit sensor
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Update the sensor details below.
        </p>
    </div>

    {{-- Current image --}}
    @if($sensor->image)
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 mb-8 bg-gray-50 dark:bg-gray-900/50">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Current image
            </p>
            <div class="flex items-center gap-4">
                <img src="{{ Str::startsWith($sensor->image, ['http://', 'https://']) ? $sensor->image : (Str::startsWith($sensor->image, ['images/', '/images/']) ? asset($sensor->image) : asset('storage/' . $sensor->image)) }}"
                     alt="{{ $sensor->name }}"
                     class="w-16 h-16 rounded-lg object-cover flex-shrink-0">
                <p class="text-sm text-gray-600 dark:text-gray-400">Upload a new image below to replace it.</p>
            </div>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('administrator.content.update', ['sensors', $sensor->id]) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Name --}}
            <div>
                <label for="name" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Sensor name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name', $sensor->name) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('name') !border-red-500 @enderror">
                @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label for="description" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <textarea name="description" id="description" rows="3" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('description') !border-red-500 @enderror">{{ old('description', $sensor->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- How it works --}}
            <div>
                <label for="how_it_works" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    How it works <span class="text-red-500">*</span>
                </label>
                <textarea name="how_it_works" id="how_it_works" rows="4" required
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('how_it_works') !border-red-500 @enderror">{{ old('how_it_works', $sensor->how_it_works) }}</textarea>
                @error('how_it_works') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Use cases + Components --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="use_cases" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Use cases <span class="text-red-500">*</span>
                    </label>
                    <textarea name="use_cases" id="use_cases" rows="4" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('use_cases') !border-red-500 @enderror">{{ old('use_cases', $sensor->use_cases) }}</textarea>
                    @error('use_cases') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="components_needed" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                        Components needed <span class="text-red-500">*</span>
                    </label>
                    <textarea name="components_needed" id="components_needed" rows="4" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('components_needed') !border-red-500 @enderror">{{ old('components_needed', $sensor->components_needed) }}</textarea>
                    @error('components_needed') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Image --}}
            <div>
                <label class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Sensor image <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(leave empty to keep current)</span>
                </label>

                <div x-data="{ preview: null }"
                    class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg hover:border-gray-400 dark:hover:border-gray-600 transition cursor-pointer"
                    onclick="document.getElementById('image').click()">

                    <div class="p-6 text-center" x-show="!preview">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-600 mb-2 block"></i>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium text-gray-900 dark:text-white">Click to upload</span> a new image
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">PNG, JPG, GIF up to 2MB</p>
                    </div>

                    <div x-show="preview" x-cloak class="p-4 relative">
                        <img :src="preview" class="max-h-40 w-full object-contain rounded-lg mx-auto">
                        <button type="button"
                            @click.stop="preview = null; document.getElementById('image').value = ''"
                            class="absolute top-2 right-2 inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                            <i class="fas fa-times text-[10px]"></i>
                            Remove
                        </button>
                    </div>

                    <input type="file" name="image" id="image" accept="image/*" class="hidden"
                        @change="preview = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                </div>
                @error('image') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Active toggle --}}
            <label for="is_active" class="flex items-start gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-gray-400 dark:hover:border-gray-600 transition">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                    {{ old('is_active', $sensor->is_active) ? 'checked' : '' }}
                    class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Active</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">When checked, this sensor is visible to users on the public site.</p>
                </div>
            </label>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route(($prefix ?? 'administrator') . '.sensors.index') }}"
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