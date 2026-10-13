@extends('layouts.app')

@section('title', 'Shop')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Header --}}
    <div class="mb-12">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Shop
        </p>
        <h1 class="text-4xl sm:text-5xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4">
            Component shop
        </h1>
        <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl">
            Buy sensors and components for your projects — reviewed and recommended.
        </p>
    </div>

    {{-- Search & Filter --}}
    <form method="GET" action="{{ route('shop.index') }}" class="mb-12">
        <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
            <div class="sm:col-span-4 relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
                <input type="text" name="search" value="{{ request('search') }}" 
                    placeholder="Search products..." 
                    class="w-full pl-11 pr-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-600 outline-none focus:border-emerald-500 transition">
            </div>
            <div class="sm:col-span-3">
                <select name="category" class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition">
                    <option value="">All Categories</option>
                    <option value="Microcontrollers" {{ request('category') == 'Microcontrollers' ? 'selected' : '' }}>Microcontrollers</option>
                    <option value="Sensor Kits" {{ request('category') == 'Sensor Kits' ? 'selected' : '' }}>Sensor Kits</option>
                    <option value="Components" {{ request('category') == 'Components' ? 'selected' : '' }}>Components</option>
                    <option value="Tools" {{ request('category') == 'Tools' ? 'selected' : '' }}>Tools</option>
                </select>
            </div>
            <div class="sm:col-span-3">
                <select name="sort" class="w-full px-4 py-3 rounded-lg border border-gray-200 dark:border-gray-800 bg-white dark:bg-gray-900 text-gray-900 dark:text-white outline-none focus:border-emerald-500 transition">
                    <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price: Low → High</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price: High → Low</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name: A-Z</option>
                </select>
            </div>
            <div class="sm:col-span-2">
                <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    Search
                </button>
            </div>
        </div>
        @if(request('search') || request('category') || request('sort'))
            <p class="mt-3 text-sm text-gray-500 dark:text-gray-400 flex items-center gap-3 flex-wrap">
                <span>
                    @if(request('search'))
                        Results for "<span class="font-medium text-gray-900 dark:text-white">{{ request('search') }}</span>"
                    @endif
                    @if(request('search') && (request('category') || request('sort'))) · @endif
                    @if(request('category'))
                        Category: <span class="font-medium text-gray-900 dark:text-white">{{ request('category') }}</span>
                    @endif
                    @if(request('category') && request('sort')) · @endif
                    @if(request('sort') && request('sort') !== 'latest')
                        Sorted: <span class="font-medium text-gray-900 dark:text-white">{{ str_replace('_', ' ', request('sort')) }}</span>
                    @endif
                </span>
                <a href="{{ route('shop.index') }}" class="text-gray-900 dark:text-white hover:underline">
                    Clear
                </a>
            </p>
        @endif
    </form>

    {{-- Products Grid --}}
    @if($products->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:border-gray-400 dark:hover:border-gray-600 transition flex flex-col group">

                    {{-- Image --}}
                    <a href="{{ route('shop.show', $product->id) }}" class="block aspect-square bg-gray-100 dark:bg-gray-900 overflow-hidden relative">
                        @if($product->image)
                            <img src="{{ Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : (Str::startsWith($product->image, ['images/', '/images/']) ? asset($product->image) : asset('storage/' . $product->image)) }}" 
                                alt="{{ $product->name }}" 
                                class="w-full h-full object-cover" loading="lazy">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-box-open text-4xl text-gray-400 dark:text-gray-600"></i>
                            </div>
                        @endif

                        @if($product->category)
                            <span class="absolute top-3 left-3 bg-white/90 dark:bg-black/70 backdrop-blur-sm text-gray-900 dark:text-white px-2.5 py-1 rounded-full text-[10px] font-medium uppercase tracking-wider">
                                {{ $product->category }}
                            </span>
                        @endif
                    </a>

                    {{-- Content --}}
                    <div class="p-4 flex-1 flex flex-col">
                        <a href="{{ route('shop.show', $product->id) }}">
                            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-primary transition">
                                {{ $product->name }}
                            </h3>
                        </a>

                        @if($product->description)
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2 flex-1 mb-4">
                                {{ Str::limit($product->description, 80) }}
                            </p>
                        @else
                            <div class="flex-1"></div>
                        @endif

                        {{-- Price --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800 mb-3">
                            @if($product->price)
                                <p class="text-xl font-semibold text-gray-900 dark:text-white">
                                    ₱{{ number_format($product->price, 2) }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Price varies
                                </p>
                            @endif
                        </div>

                        {{-- Buy button --}}
                        @if($product->link)
                            <a href="{{ $product->link }}" 
                               target="_blank" 
                               class="inline-flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition text-sm">
                                Buy now
                                <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                            </a>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($products->hasPages())
            <div class="mt-12 flex justify-center">
                {{ $products->links() }}
            </div>
        @endif
    @else
        {{-- Empty State --}}
        <div class="text-center py-20 border border-dashed border-gray-200 dark:border-gray-800 rounded-lg">
            <i class="fas fa-shopping-cart text-5xl text-gray-300 dark:text-gray-600 mb-4"></i>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No products found</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                @if(request('search') || request('category'))
                    Try different filters.
                @else
                    Check back later for new products.
                @endif
            </p>
            @if(request('search') || request('category'))
                <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                    Clear filters
                </a>
            @endif
        </div>
    @endif

    {{-- Disclaimer --}}
    <div class="mt-16 border border-gray-200 dark:border-gray-800 rounded-lg p-6 sm:p-8">
        <div class="flex items-start gap-4">
            <i class="fas fa-info-circle text-xl text-gray-400 dark:text-gray-600 flex-shrink-0 mt-0.5"></i>
            <div>
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-2">
                    Link disclaimer
                </p>
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                    Some links on this page point to external products and shops that we personally reviewed and selected. While we are not officially affiliated with these sellers, we only link to sources we believe are reliable and relevant for your learning. If you have any concerns about a linked product, feel free to contact us.
                </p>
            </div>
        </div>
    </div>

    {{-- CTA --}}
    <div class="mt-8 bg-gray-900 dark:bg-black rounded-2xl p-8 sm:p-12 text-center">
        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
            Need help?
        </p>
        <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-white mb-4">
            Not sure what to buy?
        </h2>
        <p class="text-gray-400 mb-8 max-w-xl mx-auto">
            Check out our project guides for recommended parts and kits.
        </p>
        <a href="{{ route('projects.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-white text-gray-900 font-medium rounded-lg hover:bg-gray-100 transition">
            Browse Projects
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</div>
@endsection