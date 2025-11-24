<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Produk Saya
            </h2>
            <a href="{{ route('seller.products.create') }}">
                <x-button variant="primary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Produk
                </x-button>
            </a>
        </div>
    </x-slot>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($products as $product)
                <x-card>
                    <div class="flex items-start space-x-4">
                        <!-- Preview Image -->
                        <div class="flex-shrink-0">
                            @if($product->preview_image)
                                <img src="{{ Storage::url($product->preview_image) }}" 
                                     alt="{{ $product->title }}"
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
                                        {{ $product->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ $product->short_description ?? Str::limit($product->description, 100) }}
                                    </p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                        </span>
                                        @if($product->discount_price)
                                            <span class="text-sm text-gray-500 line-through">
                                                Rp {{ number_format($product->price, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <x-badge :variant="match($product->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($product->status) }}
                                    </x-badge>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($product->category)
                                        <span>{{ $product->category->name }}</span>
                                    @endif
                                    <span>{{ $product->sales_count }} penjualan</span>
                                    <span>{{ $product->download_count }} download</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('seller.products.show', $product) }}" 
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        Lihat
                                    </a>
                                    <a href="{{ route('seller.products.edit', $product) }}" 
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        Edit
                                    </a>
                                    <form action="{{ route('seller.products.destroy', $product) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

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
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat produk pertama Anda.</p>
                <div class="mt-6">
                    <a href="{{ route('seller.products.create') }}">
                        <x-button variant="primary">
                            Tambah Produk
                        </x-button>
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

