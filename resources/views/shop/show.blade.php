@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-24">

    {{-- Breadcrumb --}}
    <nav class="mb-8">
        <ol class="flex flex-wrap items-center gap-2 text-xs uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400">
            <li><a href="{{ route('home') }}" class="hover:text-gray-900 dark:hover:text-white transition">Home</a></li>
            <li class="text-gray-300 dark:text-gray-700">/</li>
            <li><a href="{{ route('shop.index') }}" class="hover:text-gray-900 dark:hover:text-white transition">Shop</a></li>
            <li class="text-gray-300 dark:text-gray-700">/</li>
            <li class="text-gray-900 dark:text-white">{{ Str::limit($product->name, 40) }}</li>
        </ol>
    </nav>

    {{-- Main card --}}
    <div class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden">
        <div class="grid grid-cols-1 md:grid-cols-2">

            {{-- Image --}}
            <div class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center p-8 aspect-square">
                @if($product->image)
                    <img src="{{ Str::startsWith($product->image, ['http://', 'https://']) ? $product->image : (Str::startsWith($product->image, ['images/', '/images/']) ? asset($product->image) : asset('storage/' . $product->image)) }}" 
                        alt="{{ $product->name }}" 
                        class="max-w-full max-h-full object-contain">
                @else
                    <div class="text-center">
                        <i class="fas fa-box-open text-5xl text-gray-300 dark:text-gray-600 mb-3"></i>
                        <p class="text-xs text-gray-400 dark:text-gray-600">No image</p>
                    </div>
                @endif
            </div>

            {{-- Details --}}
            <div class="p-6 sm:p-8 flex flex-col">

                {{-- Category --}}
                @if($product->category)
                    <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                        {{ $product->category }}
                    </p>
                @endif

                {{-- Title --}}
                <h1 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white mb-4 break-words">
                    {{ $product->name }}
                </h1>

                {{-- Description --}}
                @if($product->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed mb-6">
                        {{ $product->description }}
                    </p>
                @endif

                {{-- Price --}}
                @if($product->price)
                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 mb-6">
                        <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-1.5">
                            Price
                        </p>
                        <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                            ₱{{ number_format($product->price, 2) }}
                        </p>
                    </div>
                @endif

                {{-- Buy --}}
                <div class="mt-auto">
                    @if($product->link)
                        <a href="{{ $product->link }}" 
                           target="_blank" 
                           class="w-full inline-flex items-center justify-center gap-2 px-6 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-100 transition">
                            Buy now
                            <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                        </a>
                    @endif
                    <p class="text-xs text-gray-400 dark:text-gray-600 text-center mt-3">
                        You'll be redirected to an external shop
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Related products --}}
    @if($relatedProducts->count() > 0)
        <section class="mt-20 pt-16 border-t border-gray-200 dark:border-gray-800">
            <div class="mb-8">
                <p class="text-xs font-medium uppercase tracking-[0.15em] text-gray-500 dark:text-gray-400 mb-3">
                    Related
                </p>
                <h2 class="text-2xl sm:text-3xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Similar products
                </h2>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($relatedProducts as $related)
                    <a href="{{ route('shop.show', $related->id) }}" 
                       class="border border-gray-200 dark:border-gray-800 rounded-lg overflow-hidden hover:border-gray-400 dark:hover:border-gray-600 transition group flex flex-col">
                        <div class="aspect-square bg-gray-100 dark:bg-gray-900 flex items-center justify-center p-4 overflow-hidden">
                            @if($related->image)
                                <img src="{{ Str::startsWith($related->image, ['http://', 'https://']) ? $related->image : (Str::startsWith($related->image, ['images/', '/images/']) ? asset($related->image) : asset('storage/' . $related->image)) }}" 
                                    alt="{{ $related->name }}" 
                                    class="max-w-full max-h-full object-contain" loading="lazy">
                            @else
                                <i class="fas fa-box-open text-3xl text-gray-300 dark:text-gray-600"></i>
                            @endif
                        </div>
                        <div class="p-3.5 flex-1 flex flex-col">
                            <p class="text-sm font-medium text-gray-900 dark:text-white line-clamp-2 mb-1.5 group-hover:text-primary transition">
                                {{ $related->name }}
                            </p>
                            @if($related->price)
                                <p class="text-sm font-semibold text-gray-900 dark:text-white mt-auto">
                                    ₱{{ number_format($related->price, 2) }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Back link --}}
    <div class="mt-16 pt-8 border-t border-gray-200 dark:border-gray-800">
        <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xs"></i>
            Back to shop
        </a>
    </div>
</div>
@endsection