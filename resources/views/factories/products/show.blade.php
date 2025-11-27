@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Detail Produk
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('factories.products.edit', [$factory, $product]) }}">
                    <x-button variant="primary" size="sm">Edit</x-button>
                </a>
                <a href="{{ route('factories.products.index', $factory) }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Image Gallery -->
                    <div>
                        @if($product->images && count($product->images) > 0)
                            <div class="space-y-4">
                                <div>
                                    <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-96 object-cover rounded-lg">
                                </div>
                                @if(count($product->images) > 1)
                                    <div class="grid grid-cols-4 gap-2">
                                        @foreach(array_slice($product->images, 1) as $image)
                                            <img src="{{ Storage::url($image) }}" alt="{{ $product->name }}" class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75">
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

                    <!-- Product Information -->
                    <div class="space-y-4">
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">{{ $product->name }}</h1>
                            @if($product->code)
                                <p class="text-sm text-gray-500">Code: {{ $product->code }}</p>
                            @endif
                        </div>

                        <div>
                            <div class="flex items-center space-x-4 mb-4">
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Harga</p>
                                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                    </p>
                                    @if($product->hasDiscount())
                                        <p class="text-sm text-gray-500 line-through">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Unit</p>
                                    <p class="text-xl font-semibold">{{ strtoupper($product->unit) }}</p>
                                </div>
                            </div>
                        </div>

                        @if($product->description)
                            <div>
                                <h3 class="font-semibold mb-2">Deskripsi</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $product->description }}</p>
                            </div>
                        @endif

                        @if($product->quality_grade && isset($product->quality_grade['grade']))
                            <div>
                                <h3 class="font-semibold mb-2">Grade Kualitas</h3>
                                <x-badge variant="info" size="lg">{{ $product->quality_grade['grade'] }}</x-badge>
                                @if(isset($product->quality_grade['value']))
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $product->quality_grade['value'] }}</p>
                                @endif
                                @if(isset($product->quality_grade['description']))
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">{{ $product->quality_grade['description'] }}</p>
                                @endif
                            </div>
                        @endif

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Stok</p>
                                <p class="text-lg font-semibold">
                                    @if($product->stock === null)
                                        <span class="text-green-600">Unlimited</span>
                                    @else
                                        <span class="{{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                            {{ $product->stock }}
                                        </span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Minimal Pemesanan</p>
                                <p class="text-lg font-semibold">{{ $product->min_order }}</p>
                            </div>
                        </div>

                        @if($product->available_units && count($product->available_units) > 0)
                            <div>
                                <h3 class="font-semibold mb-2">Unit Alternatif</h3>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($product->available_units as $unit)
                                        <x-badge variant="default">{{ strtoupper($unit) }}</x-badge>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div>
                            <x-badge :variant="$product->is_available ? 'success' : 'default'">
                                {{ $product->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                            </x-badge>
                            @if($product->is_featured)
                                <x-badge variant="info" class="ml-2">Featured</x-badge>
                            @endif
                        </div>
                    </div>
                </div>
            </x-card>

            <!-- Specifications -->
            @if($product->specifications && count($product->specifications) > 0)
                <x-card class="mt-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Spesifikasi Teknis</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($product->specifications as $key => $value)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $key }}</p>
                                <p class="font-semibold">{{ $value }}</p>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

