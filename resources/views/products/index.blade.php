<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Marketplace Produk Digital
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('products.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <x-text-input 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari produk..." 
                                class="w-full" />
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <x-select-input 
                                name="category" 
                                :options="['' => 'Semua Kategori'] + $categories->pluck('name', 'slug')->toArray()"
                                value="{{ request('category') }}" />
                        </div>

                        <!-- Sort -->
                        <div>
                            <x-select-input 
                                name="sort" 
                                :options="[
                                    'latest' => 'Terbaru',
                                    'price_low' => 'Harga: Rendah ke Tinggi',
                                    'price_high' => 'Harga: Tinggi ke Rendah',
                                    'rating' => 'Rating Tertinggi',
                                    'sales' => 'Terlaris'
                                ]"
                                value="{{ request('sort', 'latest') }}" />
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="min_price" value="Harga Minimum" />
                            <x-text-input 
                                name="min_price" 
                                type="number" 
                                value="{{ request('min_price') }}" 
                                placeholder="0" 
                                class="w-full" />
                        </div>
                        <div>
                            <x-input-label for="max_price" value="Harga Maksimum" />
                            <x-text-input 
                                name="max_price" 
                                type="number" 
                                value="{{ request('max_price') }}" 
                                placeholder="0" 
                                class="w-full" />
                        </div>
                        <div class="flex items-end gap-2">
                            <x-button variant="primary" size="md" type="submit" class="flex-1">
                                Filter
                            </x-button>
                            <a href="{{ route('products.index') }}">
                                <x-button variant="secondary" size="md" type="button">
                                    Reset
                                </x-button>
                            </a>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Products Grid -->
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($products as $product)
                        <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location='{{ route('products.show', $product) }}'">
                            <!-- Product Image -->
                            <div class="relative">
                                @if($product->preview_image)
                                    <img src="{{ Storage::url($product->preview_image) }}" 
                                         alt="{{ $product->title }}"
                                         class="w-full h-48 object-cover rounded-t-lg">
                                @else
                                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                                @if($product->discount_price)
                                    <div class="absolute top-2 right-2">
                                        <x-badge variant="danger" size="sm">
                                            {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                        </x-badge>
                                    </div>
                                @endif
                            </div>

                            <!-- Product Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2 line-clamp-2">
                                    {{ $product->title }}
                                </h3>
                                
                                @if($product->short_description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                        {{ $product->short_description }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                        </span>
                                        @if($product->discount_price)
                                            <span class="text-sm text-gray-500 line-through ml-2">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center space-x-2">
                                        @if($product->rating > 0)
                                            <span class="text-yellow-500">⭐ {{ number_format($product->rating, 1) }}</span>
                                            <span>({{ $product->review_count }})</span>
                                        @else
                                            <span>Belum ada rating</span>
                                        @endif
                                    </div>
                                    <span>{{ $product->sales_count }} penjualan</span>
                                </div>

                                @if($product->category)
                                    <div class="mt-2">
                                        <x-badge variant="default" size="sm">
                                            {{ $product->category->name }}
                                        </x-badge>
                                    </div>
                                @endif
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
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada produk</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada produk yang ditemukan dengan filter yang dipilih.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

