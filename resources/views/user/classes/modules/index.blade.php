@extends('layouts.app')

@section('title', 'Modules - ' . $class->name)

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
            Modules
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            {{ $class->name }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            {{ $modules->total() }} {{ Str::plural('module', $modules->total()) }} available
        </p>
    </div>

    @if($modules->count() > 0)
        <div class="space-y-3">
            @foreach($modules as $module)
                <a href="{{ route('dashboard.classes.modules.show', [$class, $module]) }}"
                   class="flex items-center gap-4 border border-gray-200 dark:border-gray-800 rounded-lg p-4 sm:p-5 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group">

                    {{-- Order number --}}
                    <span class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 text-sm font-semibold text-gray-600 dark:text-gray-400">
                        {{ $module->order }}
                    </span>

                    {{-- Title + meta --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $module->title }}
                        </h3>
                        @if($module->content)
                            <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">{{ Str::limit($module->content, 100) }}</p>
                        @endif
                        @if($module->file_name)
                            <p class="text-xs text-gray-400 dark:text-gray-600 mt-1 inline-flex items-center gap-1">
                                <i class="fas fa-paperclip text-[10px]"></i>
                                {{ $module->file_name }}
                            </p>
                        @endif
                    </div>

                    {{-- Arrow --}}
                    <i class="fas fa-arrow-right text-gray-300 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-0.5 transition-all flex-shrink-0 text-xs"></i>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-book-open text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No modules yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Check back later.</p>
        </div>
    @endif

    @if($modules->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $modules->links() }}
        </div>
    @endif
</div>
@endsection