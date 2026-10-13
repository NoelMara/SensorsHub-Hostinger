@extends('layouts.app')

@section('title', 'Announcements - ' . $class->name)

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('dashboard.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Classroom
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Announcements
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            {{ $class->name }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            {{ $announcements->total() }} {{ Str::plural('announcement', $announcements->total()) }} from your instructor
        </p>
    </div>

    @if($announcements->count() > 0)
        <div class="space-y-4">
            @foreach($announcements as $announcement)
                <article class="border border-gray-200 dark:border-gray-800 border-l-2 border-l-amber-500 dark:border-l-amber-400 rounded-lg p-5 sm:p-6">
                    <div class="flex items-start justify-between gap-4 mb-3">
                        <h2 class="text-base font-medium text-gray-900 dark:text-white break-words">
                            {{ $announcement->title }}
                        </h2>
                        <time class="text-xs text-gray-400 dark:text-gray-600 flex-shrink-0 mt-0.5">
                            {{ $announcement->created_at->diffForHumans() }}
                        </time>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed whitespace-pre-line">
                        {{ $announcement->content }}
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                        <p class="text-xs text-gray-400 dark:text-gray-600">
                            {{ $announcement->created_at->format('M d, Y · h:i A') }}
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-bullhorn text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No announcements yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Check back later for updates from your instructor.</p>
        </div>
    @endif

    @if($announcements->hasPages())
        <div class="mt-12 flex justify-center">
            {{ $announcements->links() }}
        </div>
    @endif
</div>
@endsection