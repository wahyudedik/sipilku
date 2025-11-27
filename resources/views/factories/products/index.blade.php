@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Katalog Produk: {{ $factory->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('factories.products.create', $factory) }}">
                    <x-button variant="primary" size="sm">Tambah Produk</x-button>
                </a>
                <a href="{{ route('factories.my-factory') }}">
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
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
                    </div>
                </x-card>
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tersedia</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['available'] }}</p>
                    </div>
                </x-card>
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Tidak Tersedia</p>
                        <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $stats['unavailable'] }}</p>
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
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Featured</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['featured'] }}</p>
                    </div>
                </x-card>
            </div>

            <!-- Filters and Bulk Import -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('factories.products.index', $factory) }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div>
                            <x-text-input 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari produk..." 
                                class="w-full" />
                        </div>
                        @if($factory->factoryType)
                            @php
                                $typeService = app(\App\Services\FactoryTypeProductService::class);
                                $productCategories = $typeService->getProductCategories($factory->factoryType->slug ?? '');
                            @endphp
                            @if(count($productCategories) > 0)
                                <div>
                                    <select name="product_category" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="">Semua Kategori</option>
                                        @foreach($productCategories as $key => $label)
                                            <option value="{{ $key }}" {{ request('product_category') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                        @endif
                        <div>
                            <select name="unit" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Unit</option>
                                @foreach($availableUnits as $unit)
                                    <option value="{{ $unit }}" {{ request('unit') === $unit ? 'selected' : '' }}>
                                        {{ strtoupper($unit) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Status</option>
                                <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Tersedia</option>
                                <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
                                <option value="out_of_stock" {{ request('status') === 'out_of_stock' ? 'selected' : '' }}>Habis</option>
                                <option value="low_stock" {{ request('status') === 'low_stock' ? 'selected' : '' }}>Stok Menipis</option>
                                <option value="in_stock" {{ request('status') === 'in_stock' ? 'selected' : '' }}>Ada Stok</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <x-button variant="primary" size="md" type="submit">Filter</x-button>
                            <a href="{{ route('factories.products.index', $factory) }}">
                                <x-button variant="secondary" size="md" type="button">Reset</x-button>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Bulk Import -->
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <form action="{{ route('factories.products.bulk-import', $factory) }}" method="POST" enctype="multipart/form-data" class="flex items-end space-x-2">
                        @csrf
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Import Produk (Excel/CSV)
                            </label>
                            <input type="file" name="file" accept=".xlsx,.xls,.csv" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100" required>
                            <p class="text-xs text-gray-500 mt-1">Format: name, description, price, unit, stock, dll</p>
                        </div>
                        <x-button variant="primary" size="md" type="submit">Import</x-button>
                    </form>
                </div>
            </x-card>

            <!-- Products Table -->
            @if($products->count() > 0)
                <x-card>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b">
                                    <th class="text-left p-3">Produk</th>
                                    <th class="text-left p-3">Harga</th>
                                    <th class="text-left p-3">Unit</th>
                                    <th class="text-left p-3">Stok</th>
                                    <th class="text-left p-3">Status</th>
                                    <th class="text-left p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($products as $product)
                                    <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="p-3">
                                            <div class="flex items-center space-x-3">
                                                @if($product->images && count($product->images) > 0)
                                                    <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}" class="w-12 h-12 object-cover rounded">
                                                @else
                                                    <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <p class="font-semibold">{{ $product->name }}</p>
                                                    @if($product->code)
                                                        <p class="text-xs text-gray-500">Code: {{ $product->code }}</p>
                                                    @endif
                                                    @if($product->product_category)
                                                        <x-badge variant="default" size="xs" class="mt-1">
                                                            {{ ucfirst(str_replace('-', ' ', $product->product_category)) }}
                                                        </x-badge>
                                                    @endif
                                                    @if($product->quality_grade && isset($product->quality_grade['grade']))
                                                        <x-badge variant="info" size="xs" class="mt-1">
                                                            {{ $product->quality_grade['grade'] }}
                                                        </x-badge>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <div>
                                                <p class="font-semibold text-primary-600 dark:text-primary-400">
                                                    Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                                </p>
                                                @if($product->hasDiscount())
                                                    <p class="text-xs text-gray-500 line-through">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="p-3">
                                            <span class="font-mono text-sm">{{ strtoupper($product->unit) }}</span>
                                        </td>
                                        <td class="p-3">
                                            @if($product->stock === null)
                                                <span class="text-gray-500">Unlimited</span>
                                            @else
                                                <span class="{{ $product->stock <= 0 ? 'text-red-600' : ($product->stock <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                                                    {{ $product->stock }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-3">
                                            <x-badge :variant="$product->is_available ? 'success' : 'default'">
                                                {{ $product->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                                            </x-badge>
                                        </td>
                                        <td class="p-3">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('factories.products.show', [$factory, $product]) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                                                    View
                                                </a>
                                                <a href="{{ route('factories.products.edit', [$factory, $product]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                                                    Edit
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $products->links() }}
                    </div>
                </x-card>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada produk.</p>
                        <a href="{{ route('factories.products.create', $factory) }}">
                            <x-button variant="primary">Tambah Produk Pertama</x-button>
                        </a>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

