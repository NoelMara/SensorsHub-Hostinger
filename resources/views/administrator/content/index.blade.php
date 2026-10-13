@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Administrator · Content
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            {{ $title }}
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mb-8">
            {{ $description }}
        </p>
        <a href="{{ route('administrator.content.create', $type) }}"
           class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
            <i class="fas fa-plus text-xs"></i>
            Add {{ Str::singular($title) }}
        </a>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('administrator.' . $type . '.index') }}" class="mb-8">
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Search {{ strtolower($title) }}..."
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition text-sm">
            </div>
            <button type="submit"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm whitespace-nowrap">
                <i class="fas fa-search text-xs"></i>
                Search
            </button>
            @if(request('search'))
                <a href="{{ route('administrator.' . $type . '.index') }}"
                   class="inline-flex items-center justify-center gap-2 px-5 py-3 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium whitespace-nowrap">
                    <i class="fas fa-times text-xs"></i>
                    Clear
                </a>
            @endif
        </div>
    </form>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-{{ isset($stats['featured']) ? '4' : '3' }} gap-3 mb-12">
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-layer-group text-emerald-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Total</p>
            </div>
            <p class="text-2xl font-semibold text-gray-900 dark:text-white tabular-nums">{{ $stats['total'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-check-circle text-emerald-500 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Active</p>
            </div>
            <p class="text-2xl font-semibold text-emerald-600 dark:text-emerald-400 tabular-nums">{{ $stats['active'] }}</p>
        </div>
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-minus-circle text-gray-400 dark:text-gray-600 text-xs"></i>
                <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Inactive</p>
            </div>
            <p class="text-2xl font-semibold text-gray-500 dark:text-gray-400 tabular-nums">{{ $stats['inactive'] }}</p>
        </div>
        @if(isset($stats['featured']))
            <div class="border border-gray-200 dark:border-gray-800 rounded-lg p-4">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-star text-amber-500 text-xs"></i>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Featured</p>
                </div>
                <p class="text-2xl font-semibold text-amber-600 dark:text-amber-400 tabular-nums">{{ $stats['featured'] }}</p>
            </div>
        @endif
    </div>

    {{-- Table --}}
    @if($items->count() > 0)
        <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-gray-200 dark:border-gray-800">
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ Str::singular($title) }}</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hidden md:table-cell">Details</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Status</th>
                            <th class="px-5 py-3 text-left text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400 hidden lg:table-cell">Created</th>
                            <th class="px-5 py-3 text-right text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                            <tr class="border-b border-gray-100 dark:border-gray-800/50 last:border-0 hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">

                                {{-- Item --}}
                                <td class="px-5 py-4 align-top">
                                    <div class="flex items-center gap-3">
                                        @if($type === 'sensors')
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 overflow-hidden">
                                                @if($item->image)
                                                    <img src="{{ Str::startsWith($item->image, ['http://', 'https://']) ? $item->image : (Str::startsWith($item->image, ['images/', '/images/']) ? asset($item->image) : asset('storage/' . $item->image)) }}"
                                                         alt="{{ $item->name }}" class="w-full h-full object-cover">
                                                @else
                                                    <i class="fas fa-microchip text-gray-400 dark:text-gray-600 text-sm"></i>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item->title ?? $item->name }}</p>
                                            @if(isset($item->description))
                                                <p class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">{{ Str::limit($item->description, 60) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                {{-- Details --}}
                                <td class="px-5 py-4 align-top hidden md:table-cell">
                                    @if($type === 'projects')
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->sensor?->name ?? '—' }}</p>
                                        <p class="text-[10px] font-medium uppercase tracking-wider text-blue-600 dark:text-blue-400 mt-1">{{ $item->difficulty }}</p>
                                    @elseif($type === 'products')
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white tabular-nums">₱{{ number_format((float) $item->price, 2) }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item->category ?? '—' }}</p>
                                    @elseif($type === 'videos')
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $item->category ?? '—' }}</p>
                                    @else
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ Str::limit($item->use_cases ?? '—', 60) }}</p>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-5 py-4 align-top">
                                    <div class="flex flex-col items-start gap-1.5">
                                        @if($item->is_active)
                                            <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-emerald-600 dark:text-emerald-400">● Active</span>
                                        @else
                                            <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">● Inactive</span>
                                        @endif
                                        @if($type === 'projects' && $item->is_featured)
                                            <span class="whitespace-nowrap text-[10px] font-medium uppercase tracking-wider text-amber-600 dark:text-amber-400">● Featured</span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Created --}}
                                <td class="px-5 py-4 align-top hidden lg:table-cell">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $item->created_at->format('M d, Y') }}</span>
                                </td>

                                {{-- Actions --}}
                                <td class="px-5 py-4 align-top text-right whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1">
                                        <a href="{{ route('administrator.content.edit', [$type, $item->id]) }}"
                                           class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition">
                                            <i class="fas fa-edit text-[10px]"></i>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('administrator.content.destroy', [$type, $item->id]) }}"
                                            onsubmit="return confirm('Delete this item?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 h-8 rounded-lg border border-gray-200 dark:border-gray-800 text-xs font-medium text-gray-600 dark:text-gray-400 hover:border-red-500 hover:text-red-500 dark:hover:border-red-500 dark:hover:text-red-500 transition">
                                                <i class="fas fa-trash text-[10px]"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        @if($items->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $items->links() }}
            </div>
        @endif
    @else
        {{-- Empty state --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            @if(request('search'))
                <i class="fas fa-search text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No results found</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Try a different search term.</p>
                <a href="{{ route('administrator.' . $type . '.index') }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 border border-gray-200 dark:border-gray-800 text-gray-600 dark:text-gray-400 rounded-lg hover:border-gray-900 dark:hover:border-white hover:text-gray-900 dark:hover:text-white transition text-sm font-medium">
                    <i class="fas fa-times text-xs"></i>
                    Clear search
                </a>
            @else
                <i class="fas fa-folder-open text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No {{ $title }} yet</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">Create your first {{ Str::singular($title) }} to get started.</p>
                <a href="{{ route('administrator.content.create', $type) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                    <i class="fas fa-plus text-xs"></i>
                    Add {{ Str::singular($title) }}
                </a>
            @endif
        </div>
    @endif
</div>
@endsection