@extends('layouts.app')

@section('title', 'Edit Module')

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.modules.index', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Modules
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor · Edit
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Edit module
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Update this module for {{ $class->name }}.
        </p>
    </div>

    {{-- Current file info (only if a file exists) --}}
    @if($module->file_name)
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 mb-8 bg-gray-50 dark:bg-gray-900/50">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                Current attachment
            </p>
            <div class="flex items-center gap-3">
                <i class="fas fa-file-alt text-emerald-500 text-lg flex-shrink-0"></i>
                <div class="min-w-0 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $module->file_name }}</p>
                    @if($module->file_size)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $module->file_size > 1048576 ? number_format($module->file_size / 1048576, 1) . ' MB' : number_format($module->file_size / 1024, 1) . ' KB' }}
                        </p>
                    @endif
                </div>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-3">Upload a new file below to replace it.</p>
        </div>
    @endif

    {{-- Form --}}
    <form method="POST" action="{{ route('instructor.classes.modules.update', [$class, $module]) }}"
        enctype="multipart/form-data"
        onsubmit="const f=document.getElementById('file').files[0];if(f&&f.size>52428800){document.getElementById('fileSizeError').classList.remove('hidden');return false;}const b=this.querySelector('button[type=submit]');b.disabled=true;b.innerHTML='<i class=&quot;fas fa-spinner fa-spin&quot;></i> Saving...';">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- Title --}}
            <div>
                <label for="title" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Module title <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title" required
                    value="{{ old('title', $module->title) }}"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm @error('title') !border-red-500 @enderror"
                    placeholder="e.g., Introduction to Sensors">
                @error('title') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Content --}}
            <div>
                <label for="content" class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Additional information <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional)</span>
                </label>
                <textarea name="content" id="content" rows="6"
                    class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm resize-none leading-relaxed @error('content') !border-red-500 @enderror"
                    placeholder="Any extra instructions or notes for students...">{{ old('content', $module->content) }}</textarea>
                @error('content') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- File upload --}}
            <div>
                <label class="block text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Attachment <span class="text-gray-400 dark:text-gray-600 normal-case tracking-normal">(optional, PDF/Word, max 50MB)</span>
                </label>

                <div x-data="{ fileName: null, fileSize: null, dragging: false }"
                    @dragover.prevent="dragging = true"
                    @dragleave.prevent="dragging = false"
                    @drop.prevent="dragging = false; const file = $event.dataTransfer.files[0]; if(file) { fileName = file.name; fileSize = Math.round(file.size / 1024); document.getElementById('file').files = $event.dataTransfer.files; }"
                    :class="{ 'border-emerald-500 bg-emerald-50 dark:bg-emerald-950/20': dragging }"
                    class="border border-dashed border-gray-300 dark:border-gray-700 rounded-lg hover:border-gray-400 dark:hover:border-gray-600 transition cursor-pointer"
                    onclick="document.getElementById('file').click()">

                    <div class="p-6 text-center" x-show="!fileName">
                        <i class="fas fa-cloud-upload-alt text-2xl text-gray-400 dark:text-gray-600 mb-2 block"></i>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium text-gray-900 dark:text-white">Click to upload</span> or drag and drop
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-600 mt-1">
                            @if($module->file_name)
                                Leave empty to keep the current file
                            @else
                                PDF, DOC, DOCX up to 50MB
                            @endif
                        </p>
                    </div>

                    <div class="p-6 text-center" x-show="fileName" x-cloak>
                        <i class="fas fa-file-alt text-2xl text-emerald-500 mb-2 block"></i>
                        <p class="text-sm font-medium text-gray-900 dark:text-white break-all" x-text="fileName"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" x-text="fileSize + ' KB'"></p>
                        <button type="button"
                            @click.stop="fileName = null; fileSize = null; document.getElementById('file').value = ''"
                            class="mt-3 inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                            <i class="fas fa-times text-[10px]"></i>
                            Remove
                        </button>
                    </div>

                    <input type="file" name="file" id="file"
                        accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document"
                        class="hidden"
                        @change="fileName = $event.target.files[0]?.name; fileSize = Math.round($event.target.files[0]?.size / 1024)">
                </div>

                @error('file') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                <p id="fileSizeError" class="text-red-500 text-xs mt-1.5 hidden">File too large! Maximum is 50MB.</p>
            </div>

            {{-- Publish toggle --}}
            <label for="is_published" class="flex items-start gap-3 border border-gray-200 dark:border-gray-800 rounded-lg p-4 cursor-pointer hover:border-gray-400 dark:hover:border-gray-600 transition">
                <input type="checkbox" name="is_published" id="is_published" value="1"
                    {{ old('is_published', $module->is_published) ? 'checked' : '' }}
                    class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">Publish this module</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">When checked, students will see it. Uncheck to save as draft.</p>
                </div>
            </label>
        </div>

        {{-- Actions --}}
        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-gray-800 flex flex-wrap items-center justify-end gap-3">
            <a href="{{ route('instructor.classes.modules.index', $class) }}"
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