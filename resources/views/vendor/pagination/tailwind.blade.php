@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="w-full">

        {{-- Mobile: simple Prev / Next --}}
        <div class="flex sm:hidden items-center justify-center gap-3">

            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center justify-center px-4 h-9 text-xs font-medium text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-800 rounded-lg cursor-not-allowed">
                    <i class="fas fa-chevron-left mr-1.5 text-[10px]"></i>
                    Prev
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                   class="inline-flex items-center justify-center px-4 h-9 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-800 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                    <i class="fas fa-chevron-left mr-1.5 text-[10px]"></i>
                    Prev
                </a>
            @endif

            <span class="text-xs text-gray-500 dark:text-gray-400 tabular-nums">
                {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}
            </span>

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                   class="inline-flex items-center justify-center px-4 h-9 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-800 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                    Next
                    <i class="fas fa-chevron-right ml-1.5 text-[10px]"></i>
                </a>
            @else
                <span class="inline-flex items-center justify-center px-4 h-9 text-xs font-medium text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-800 rounded-lg cursor-not-allowed">
                    Next
                    <i class="fas fa-chevron-right ml-1.5 text-[10px]"></i>
                </span>
            @endif
        </div>

        {{-- Desktop: full pagination --}}
        <div class="hidden sm:flex items-center justify-between gap-4">

            <p class="text-xs text-gray-500 dark:text-gray-400">
                Showing
                @if ($paginator->firstItem())
                    <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->firstItem() }}</span>
                    to
                    <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->lastItem() }}</span>
                @else
                    {{ $paginator->count() }}
                @endif
                of
                <span class="font-medium text-gray-900 dark:text-white">{{ $paginator->total() }}</span>
                results
            </p>

            <div class="flex items-center gap-1">

                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="inline-flex items-center justify-center w-9 h-9 text-xs text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-800 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
                       class="inline-flex items-center justify-center w-9 h-9 text-xs text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-800 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                        <i class="fas fa-chevron-left text-[10px]"></i>
                    </a>
                @endif

                {{-- Page numbers --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="inline-flex items-center justify-center min-w-[2.25rem] h-9 text-xs text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-800 rounded-lg cursor-default px-2">
                            {{ $element }}
                        </span>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="inline-flex items-center justify-center min-w-[2.25rem] h-9 text-xs font-semibold text-white dark:text-gray-900 bg-gray-900 dark:bg-white rounded-lg px-2 tabular-nums cursor-default">
                                    {{ $page }}
                                </span>
                            @else
                                <a href="{{ $url }}"
                                   class="inline-flex items-center justify-center min-w-[2.25rem] h-9 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-800 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition px-2 tabular-nums">
                                    {{ $page }}
                                </a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next"
                       class="inline-flex items-center justify-center w-9 h-9 text-xs text-gray-600 dark:text-gray-400 border border-gray-200 dark:border-gray-800 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </a>
                @else
                    <span class="inline-flex items-center justify-center w-9 h-9 text-xs text-gray-400 dark:text-gray-600 border border-gray-200 dark:border-gray-800 rounded-lg cursor-not-allowed">
                        <i class="fas fa-chevron-right text-[10px]"></i>
                    </span>
                @endif
            </div>
        </div>
    </nav>
@endif