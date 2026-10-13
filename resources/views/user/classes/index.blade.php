@extends('layouts.app')

@section('title', 'My Classroom')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Dashboard
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            My Classroom
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Enter the code from your instructor to join your classroom.
        </p>
    </div>

    @php
        $approvedClasses = $classes;
        $pendingClasses = auth()->user()->classes()->wherePivot('status', 'pending')->get();
        $hasClass = $approvedClasses->count() > 0 || $pendingClasses->count() > 0;
    @endphp

    {{-- Join Class Form — only show if not enrolled --}}
    @if(!$hasClass)
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8 mb-12">
            <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                Join a class
            </p>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                Enter your class code
            </h2>

            <form method="POST" action="{{ route('dashboard.classes.join') }}" class="flex flex-col sm:flex-row gap-3">
                @csrf
                <input type="text" name="code" required maxlength="6"
                    class="flex-1 px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-center text-lg font-semibold tracking-[0.3em] uppercase"
                    placeholder="CODE">
                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                    <i class="fas fa-plus text-xs"></i>
                    Join class
                </button>
            </form>
        </div>
    @endif

    {{-- Pending Approval --}}
    @if($pendingClasses->count() > 0)
        <div class="mb-12">
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Pending
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Awaiting approval
                </h2>
            </div>

            <div class="space-y-3">
                @foreach($pendingClasses as $class)
                    <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-5">
                        <div class="flex items-center justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 text-xs font-semibold text-white dark:text-gray-900">
                                    {{ strtoupper(substr($class->section ?? $class->name, -1)) }}
                                </div>
                                <div class="min-w-0">
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $class->name }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ $class->instructor->name }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-[10px] font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400 flex-shrink-0">
                                ● Waiting
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Enrolled Classes --}}
    @if($approvedClasses->count() > 0)
        <div>
            <div class="mb-4">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Enrolled
                </p>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Your classroom
                </h2>
            </div>

            <div class="space-y-3">
                @foreach($approvedClasses as $class)
                    <a href="{{ route('dashboard.classes.show', $class) }}"
                       class="border border-gray-200 dark:border-gray-800 rounded-lg p-5 hover:border-gray-400 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition group flex items-center gap-4">

                        <div class="w-10 h-10 rounded-lg bg-gray-900 dark:bg-white flex items-center justify-center flex-shrink-0 text-xs font-semibold text-white dark:text-gray-900">
                            {{ strtoupper(substr($class->section ?? $class->name, -1)) }}
                        </div>

                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                {{ $class->name }}
                            </h3>
                            <div class="flex items-center gap-3 mt-1 text-xs text-gray-500 dark:text-gray-400 flex-wrap">
                                <span class="inline-flex items-center gap-1.5">
                                    <i class="fas fa-user text-[10px]"></i>
                                    {{ $class->instructor->name }}
                                </span>
                                @if($class->section)
                                    <span class="text-gray-400 dark:text-gray-600">·</span>
                                    <span>Block {{ $class->section }}</span>
                                @endif
                            </div>
                        </div>

                        <i class="fas fa-arrow-right text-gray-300 dark:text-gray-600 group-hover:text-gray-900 dark:group-hover:text-white group-hover:translate-x-0.5 transition-all flex-shrink-0"></i>
                    </a>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Empty State --}}
    @if($pendingClasses->count() === 0 && $approvedClasses->count() === 0)
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-chalkboard text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No classroom yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Enter a class code above to join your first classroom.
            </p>
        </div>
    @endif
</div>
@endsection