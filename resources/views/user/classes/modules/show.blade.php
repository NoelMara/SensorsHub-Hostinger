@extends('layouts.app')

@section('title', $module->title)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? route('instructor.classes.modules.index', $class) : route('dashboard.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        {{ (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? 'Back to Modules' : 'Back to Classroom' }}
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Module {{ $module->order }} · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4 break-words">
            {{ $module->title }}
        </h1>
    </div>

    {{-- Content --}}
    <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-8">
        @if($module->content)
            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">
                {{ $module->content }}
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-file-alt text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    No additional content. Check the attachment below.
                </p>
            </div>
        @endif
    </section>

    {{-- Attachment --}}
    @if($module->file_path)
        @php
            $extension = strtolower(pathinfo($module->file_name, PATHINFO_EXTENSION));
            $icon = match($extension) {
                'pdf' => 'fa-file-pdf',
                'doc', 'docx' => 'fa-file-word',
                default => 'fa-file-alt'
            };
        @endphp

        <section class="border border-gray-200 dark:border-gray-800 rounded-lg p-6"
                 x-data="{ downloading: false, cooldown: 5, timer: null }">
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Attachment
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Download file
                </h2>
            </div>

            <a href="{{ $module->file_path }}" download
               @click="if(!downloading){ downloading = true; cooldown = 5; timer = setInterval(() => { cooldown--; if(cooldown <= 0){ clearInterval(timer); downloading = false } }, 1000) }"
               :class="downloading ? 'pointer-events-none opacity-60' : ''"
               class="flex items-center gap-4 border border-gray-200 dark:border-gray-800 rounded-lg p-4 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">

                <i class="fas {{ $icon }} text-gray-400 dark:text-gray-600 text-lg flex-shrink-0"></i>

                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                        {{ $module->file_name ?? 'Download File' }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                        @if($module->file_size)
                            {{ $module->file_size > 1048576 ? number_format($module->file_size / 1048576, 1) . ' MB' : number_format($module->file_size / 1024, 1) . ' KB' }}
                            <span class="text-gray-300 dark:text-gray-700 mx-1">·</span>
                        @endif
                        <span x-show="!downloading">Click to download</span>
                        <span x-show="downloading" class="text-blue-600 dark:text-blue-400 font-medium">
                            Please wait <span x-text="cooldown + 's'"></span>
                        </span>
                    </p>
                </div>

                <i class="fas fa-arrow-down text-gray-300 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-y-0.5 transition-all text-xs flex-shrink-0"></i>
            </a>
        </section>
    @endif

    {{-- Back link bottom --}}
    <div class="mt-16 pt-8 border-t border-gray-100 dark:border-gray-800">
        <a href="{{ (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? route('instructor.classes.modules.index', $class) : route('dashboard.classes.show', $class) }}"
           class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xs"></i>
            {{ (auth()->user()->isInstructor() || auth()->user()->isAdministrator()) ? 'Back to Modules' : 'Back to Classroom' }}
        </a>
    </div>
</div>
@endsection