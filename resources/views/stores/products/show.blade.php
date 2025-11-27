@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Produk: {{ $product->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('stores.products.edit', [$store, $product]) }}">
                    <x-button variant="primary" size="sm">Edit</x-button>
                </a>
                <a href="{{ route('stores.products.index', $store) }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Images -->
                    <div>
                        @if($product->images && count($product->images) > 0)
                            <div class="space-y-4">
                                <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-lg">
                                @if(count($product->images) > 1)
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach($product->images as $image)
                                            <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="w-full h-96 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-24 h-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        @endif
                    </div>

                    <!-- Product Info -->
                    <div class="space-y-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $product->name }}</h1>
                            @if($product->category)
                                <x-badge variant="info" size="sm" class="mt-2">{{ $product->category->name }}</x-badge>
                            @endif
                        </div>

                        <div>
                            @if($product->hasDiscount())
                                <div class="flex items-center space-x-2">
                                    <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                    </span>
                                    <span class="text-lg text-gray-500 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                            @else
                                <span class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </span>
                            @endif
                            <span class="text-gray-500">/ {{ $product->unit }}</span>
                        </div>

                        @if($product->description)
                            <div>
                                <h3 class="font-semibold mb-2">Deskripsi</h3>
                                <p class="text-gray-600 dark:text-gray-400">{{ $product->description }}</p>
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">SKU</p>
                                <p class="font-semibold">{{ $product->sku ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Brand</p>
                                <p class="font-semibold">{{ $product->brand ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Stok</p>
                                <p class="font-semibold {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ $product->stock ?? '∞' }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Min. Pemesanan</p>
                                <p class="font-semibold">{{ $product->min_order }}</p>
                            </div>
                        </div>

                        @if($product->specifications && count($product->specifications) > 0)
                            <div>
                                <h3 class="font-semibold mb-2">Spesifikasi</h3>
                                <div class="space-y-2">
                                    @foreach($product->specifications as $key => $value)
                                        <div class="flex justify-between border-b border-gray-200 dark:border-gray-700 pb-2">
                                            <span class="text-gray-600 dark:text-gray-400">{{ $key }}</span>
                                            <span class="font-semibold">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="flex items-center space-x-4">
                            @if($product->is_active)
                                <x-badge variant="success" size="sm">Aktif</x-badge>
                            @else
                                <x-badge variant="default" size="sm">Nonaktif</x-badge>
                            @endif
                            @if($product->is_featured)
                                <x-badge variant="warning" size="sm">Featured</x-badge>
                            @endif
                        </div>

                        <div class="text-sm text-gray-500">
                            <p>{{ $product->view_count }} views | {{ $product->sold_count }} terjual</p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>

