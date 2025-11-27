@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Katalog Produk: {{ $store->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('stores.products.create', $store) }}">
                    <x-button variant="primary" size="sm">Tambah Produk</x-button>
                </a>
                <a href="{{ route('stores.my-store') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif

            @if(session('error'))
                <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            <!-- Statistics -->
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                    </div>
                </x-card>
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Aktif</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['active'] }}</p>
                    </div>
                </x-card>
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Nonaktif</p>
                        <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['inactive'] }}</p>
                    </div>
                </x-card>
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Habis</p>
                        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['out_of_stock'] }}</p>
                    </div>
                </x-card>
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Stok Menipis</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['low_stock'] }}</p>
                    </div>
                </x-card>
            </div>

            <!-- Filters and Bulk Import -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('stores.products.index', $store) }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-text-input 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari produk..." 
                                class="w-full" />
                        </div>
                        <div>
                            <select name="category" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->uuid }}" {{ request('category') === $category->uuid ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                                <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                                <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Stok Menipis</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <x-button variant="primary" size="md" type="submit">Filter</x-button>
                            <a href="{{ route('stores.products.index', $store) }}">
                                <x-button variant="secondary" size="md" type="button">Reset</x-button>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Bulk Import -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <form action="{{ route('stores.products.bulk-import', $store) }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-2">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Import Produk (Excel/CSV)
                            </label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" required
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </div>
                        <x-button variant="primary" size="md" type="submit">Import</x-button>
                    </form>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                        Format: name, description, sku, brand, price, discount_price, unit, stock, min_order, is_active
                    </p>
                </div>
            </x-card>

            <!-- Products List -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 gap-4">
                    @foreach($products as $product)
                        <x-card>
                            <div class="flex items-start space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0">
                                    @if($product->images && count($product->images) > 0)
                                        <img src="{{ Storage::url($product->images[0]) }}" 
                                             alt="{{ $product->name }}"
                                             class="w-24 h-24 object-cover rounded-lg">
                                    @else
                                        <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Product Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $product->name }}
                                            </h3>
                                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                                {{ Str::limit($product->description, 100) }}
                                            </p>
                                            <div class="mt-2 flex items-center space-x-4">
                                                <div>
                                                    @if($product->hasDiscount())
                                                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                            Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                                        </span>
                                                        <span class="text-sm text-gray-500 line-through ml-2">
                                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                                        </span>
                                                    @else
                                                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                                        </span>
                                                    @endif
                                                    <span class="text-sm text-gray-500">/ {{ $product->unit }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-sm {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                                        Stok: {{ $product->stock ?? '∞' }}
                                                    </span>
                                                    @if($product->category)
                                                        <x-badge variant="info" size="sm">{{ $product->category->name }}</x-badge>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mt-2 flex items-center space-x-2">
                                                @if($product->is_active)
                                                    <x-badge variant="success" size="sm">Aktif</x-badge>
                                                @else
                                                    <x-badge variant="default" size="sm">Nonaktif</x-badge>
                                                @endif
                                                @if($product->is_featured)
                                                    <x-badge variant="warning" size="sm">Featured</x-badge>
                                                @endif
                                                <span class="text-xs text-gray-500">
                                                    {{ $product->view_count }} views | {{ $product->sold_count }} terjual
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex space-x-2 ml-4">
                                            <a href="{{ route('stores.products.edit', [$store, $product]) }}">
                                                <x-button variant="secondary" size="sm">Edit</x-button>
                                            </a>
                                            <form action="{{ route('stores.products.destroy', [$store, $product]) }}" method="POST" onsubmit="return confirm('Hapus produk ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <x-button variant="danger" size="sm" type="submit">Hapus</x-button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $products->links() }}
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada produk.</p>
                        <a href="{{ route('stores.products.create', $store) }}">
                            <x-button variant="primary">Tambah Produk Pertama</x-button>
                        </a>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

