@extends('layouts.app')

@section('title', 'Announcements - ' . $class->name)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Back link --}}
    <a href="{{ route('instructor.classes.show', $class) }}"
       class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition mb-10">
        <i class="fas fa-arrow-left text-xs"></i>
        Back to Class
    </a>

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Announcements · {{ $class->name }}
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Announcements
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-8">
            {{ $announcements->total() }} {{ Str::plural('announcement', $announcements->total()) }} posted to this class.
        </p>

        {{-- Actions --}}
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('instructor.classes.announcements.create', $class) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                <i class="fas fa-plus text-xs"></i>
                New announcement
            </a>
            <a href="{{ route('instructor.classes.announcements.import', $class) }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                <i class="fas fa-download text-xs"></i>
                Import
            </a>
        </div>
    </div>

    @if($announcements->count() > 0)
        <div class="space-y-3">
            @foreach($announcements as $announcement)
                <article class="border border-gray-200 dark:border-gray-800 border-l-2 border-l-amber-500 dark:border-l-amber-400 rounded-lg p-5">

                    {{-- Top row: title + status + time --}}
                    <div class="flex items-start justify-between gap-4 mb-2">
                        <h3 class="text-base font-medium text-gray-900 dark:text-white break-words min-w-0" title="{{ $announcement->title }}">
                            {{ Str::limit($announcement->title, 60) }}
                        </h3>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if($announcement->is_published)
                                <span class="text-[10px] font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                                    ● Published
                                </span>
                            @else
                                <span class="text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                    ● Draft
                                </span>
                            @endif
                            <time class="text-xs text-gray-400 dark:text-gray-600">
                                {{ $announcement->created_at->diffForHumans() }}
                            </time>
                        </div>
                    </div>

                    {{-- Content --}}
                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4">
                        {{ Str::limit($announcement->content, 150) }}
                    </p>

                    {{-- Footer: exact date + actions --}}
                    <div class="flex items-center justify-between gap-4 pt-3 border-t border-gray-100 dark:border-gray-800">
                        <p class="text-xs text-gray-400 dark:text-gray-600">
                            {{ $announcement->created_at->format('M d, Y · h:i A') }}
                        </p>
                        <div class="flex items-center gap-2">
                            <a href="{{ route('instructor.classes.announcements.edit', [$class, $announcement]) }}"
                               class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                                <i class="fas fa-edit text-[10px]"></i>
                                Edit
                            </a>
                            <form action="{{ route('instructor.classes.announcements.destroy', [$class, $announcement]) }}" method="POST"
                                onsubmit="return confirm('Delete this announcement?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                                    <i class="fas fa-trash text-[10px]"></i>
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>

        @if($announcements->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $announcements->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-bullhorn text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No announcements yet</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Use the button above to post your first announcement.</p>
        </div>
    @endif
</div>
@endsection