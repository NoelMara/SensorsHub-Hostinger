@extends('layouts.app')

@section('title', 'Import Announcements - ' . $class->name)

@section('content')
<div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.announcements.index', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Announcements
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Instructor · Import
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Import announcements
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Copy announcements from your other classes into {{ $class->name }}.
        </p>
    </div>

    @if($otherClasses->count() > 0)
        @php $hasAnyAnnouncements = $otherClasses->contains(fn($c) => $c->announcements()->count() > 0); @endphp

        @if($hasAnyAnnouncements)
            <div class="space-y-6">
                @foreach($otherClasses as $otherClass)
                    @if($otherClass->announcements()->count() > 0)
                        <section class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">

                            {{-- Section header --}}
                            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-800 bg-gray-50 dark:bg-gray-900/50">
                                <h2 class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $otherClass->name }}
                                </h2>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                    {{ $otherClass->announcements()->count() }} {{ Str::plural('announcement', $otherClass->announcements()->count()) }}
                                </p>
                            </div>

                            {{-- Form --}}
                            <form method="POST" action="{{ route('instructor.classes.announcements.copy', $class) }}">
                                @csrf
                                <input type="hidden" name="from_class" value="{{ $otherClass->id }}">

                                {{-- Announcement list --}}
                                <div>
                                    @foreach($otherClass->announcements as $announcement)
                                        <label class="flex items-start gap-3 px-5 py-4 cursor-pointer border-b border-gray-100 dark:border-gray-800 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                                            <input type="checkbox" name="announcements[]" value="{{ $announcement->id }}" checked
                                                class="mt-0.5 h-4 w-4 rounded border-gray-300 dark:border-gray-700 text-emerald-500 focus:ring-emerald-500 cursor-pointer flex-shrink-0">
                                            <div class="min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $announcement->title }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">{{ Str::limit($announcement->content, 80) }}</p>
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
            {{-- No announcements in any other class --}}
            <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
                <i class="fas fa-copy text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Nothing to import</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">None of your other classes have announcements yet.</p>
            </div>
        @endif
    @else
        {{-- No other classes at all --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-copy text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No other classes</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">You don't have any other classes with announcements to import from.</p>
        </div>
    @endif
</div>
@endsection