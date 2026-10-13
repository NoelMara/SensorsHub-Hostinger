@extends('layouts.app')

@section('title', 'Import Modules - ' . $class->name)

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
            Instructor · Import
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Import modules
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Copy modules from your other classes into {{ $class->name }}.
        </p>
    </div>

    @if($otherClasses->count() > 0)
        @php $hasAnyModules = $otherClasses->contains(fn($c) => $c->modules()->count() > 0); @endphp

        @if($hasAnyModules)
            <div class="space-y-6">
                @foreach($otherClasses as $otherClass)
                    @if($otherClass->modules()->count() > 0)
                        <section class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">

                            {{-- Section header --}}
                            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                                <h2 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $otherClass->name }}
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $otherClass->modules()->count() }} {{ Str::plural('module', $otherClass->modules()->count()) }}
                                </p>
                            </div>

                            {{-- Form --}}
                            <form method="POST" action="{{ route('instructor.classes.modules.copy', $class) }}">
                                @csrf
                                <input type="hidden" name="from_class" value="{{ $otherClass->id }}">

                                {{-- Module list --}}
                                <div>
                                    @foreach($otherClass->modules as $module)
                                        <label class="flex items-start gap-3 px-5 py-4 cursor-pointer border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                            <input type="checkbox" name="modules[]" value="{{ $module->id }}" checked
                                                class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer flex-shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $module->title }}</p>
                                                @if($module->file_name)
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 inline-flex items-center gap-1.5 mt-1">
                                                        <i class="fas fa-paperclip text-[10px]"></i>
                                                        {{ $module->file_name }}
                                                        @if($module->file_size)
                                                            <span class="text-gray-300 dark:text-gray-700">·</span>
                                                            {{ $module->file_size > 1048576 ? number_format($module->file_size / 1048576, 1) . ' MB' : number_format($module->file_size / 1024, 1) . ' KB' }}
                                                        @endif
                                                    </p>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                {{-- Submit --}}
                                <div class="px-5 py-4 border-t border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/30 flex justify-end">
                                    <button type="submit"
                                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                                        <i class="fas fa-download text-xs"></i>
                                        Import selected
                                    </button>
                                </div>
                            </form>
                        </section>
                    @endif
                @endforeach
            </div>
        @else
            {{-- No modules in any other class --}}
            <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-copy text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nothing to import</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">None of your other classes have modules yet.</p>
            </div>
        @endif
    @else
        {{-- No other classes at all --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-copy text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No other classes</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">You don't have any other classes with modules to import from.</p>
        </div>
    @endif
</div>
@endsection