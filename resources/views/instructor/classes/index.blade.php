@extends('layouts.app')

@section('title', 'My Classes')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            My classes
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-8">
            {{ $classes->total() }} {{ Str::plural('class', $classes->total()) }} you teach.
        </p>
        <a href="{{ route('instructor.classes.create') }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
            <i class="fas fa-plus text-xs"></i>
            Create class
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('instructor.classes.index') }}" class="mb-12">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search by name, section, or code..."
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            </div>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                <i class="fas fa-search text-xs"></i>
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('instructor.classes.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium whitespace-nowrap">
                    <i class="fas fa-times text-xs"></i>
                    Clear
                </a>
            @endif
        </div>
        @if(request('search'))
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400">
                Results for "<span class="font-medium text-gray-900 dark:text-white">{{ request('search') }}</span>"
                <a href="{{ route('instructor.classes.index') }}" class="text-gray-900 dark:text-white hover:underline ml-2">Clear</a>
            </p>
        @endif
    </form>

    @if($classes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($classes as $class)
                <a href="{{ route('instructor.classes.show', $class) }}"
                   class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition group flex flex-col">

                    {{-- Name + section --}}
                    <div class="flex items-start gap-3 mb-4">
                        <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 text-xs font-semibold text-white dark:text-gray-900">
                            {{ strtoupper(substr($class->section ?? $class->name, -1)) }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base font-medium text-gray-900 dark:text-white truncate">
                                {{ $class->name }}
                            </h3>
                            @if($class->section)
                                <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                    Block {{ $class->section }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Stats --}}
                    <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400 mb-4 pb-4 border-b border-gray-100 dark:border-gray-800">
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-users text-[10px]"></i>
                            {{ $class->students->count() }} {{ Str::plural('student', $class->students->count()) }}
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <i class="fas fa-tasks text-[10px]"></i>
                            {{ $class->assessments()->count() }} {{ Str::plural('assessment', $class->assessments()->count()) }}
                        </span>
                    </div>

                    {{-- Code + arrow --}}
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <p class="text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 mb-1">
                                Class code
                            </p>
                            <p class="text-base font-semibold text-gray-900 dark:text-white tracking-[0.15em] font-mono">
                                {{ $class->code }}
                            </p>
                        </div>
                        <i class="fas fa-arrow-right text-gray-300 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-0.5 transition-all text-xs"></i>
                    </div>
                </a>
            @endforeach
        </div>

        @if($classes->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $classes->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        @if(request('search'))
            <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-search text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No classes found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Try a different search term.</p>
                <a href="{{ route('instructor.classes.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                    <i class="fas fa-times text-xs"></i>
                    Clear search
                </a>
            </div>
        @else
            <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-chalkboard text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No classes yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Create your first class and share the code with students.</p>
                <a href="{{ route('instructor.classes.create') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                    <i class="fas fa-plus text-xs"></i>
                    Create class
                </a>
            </div>
        @endif
    @endif
</div>
@endsection