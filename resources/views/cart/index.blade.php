<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Keranjang Belanja
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(count($items) > 0)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        @foreach($items as $item)
                            <x-card>
                                <div class="flex items-start space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0">
                                        @if($item['product']->preview_image)
                                            <img src="{{ Storage::url($item['product']->preview_image) }}" 
                                                 alt="{{ $item['product']->title }}"
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
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                            <a href="{{ route('products.show', $item['product']) }}" class="hover:text-primary-600">
                                                {{ $item['product']->title }}
                                            </a>
                                        </h3>
                                        @if($item['product']->category)
                                            <x-badge variant="default" size="sm" class="mb-2">
                                                {{ $item['product']->category->name }}
                                            </x-badge>
                                        @endif
                                        <div class="flex items-center justify-between mt-4">
                                            <div>
                                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                                    Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                </p>
                                                @if($item['product']->discount_price)
                                                    <p class="text-sm text-gray-500 line-through">
                                                        Rp {{ number_format($item['product']->price, 0, ',', '.') }}
                                                    </p>
                                                @endif
                                            </div>
                                            <form action="{{ route('cart.remove', $item['product']) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </x-card>
                        @endforeach

                        <div class="flex justify-end">
                            <form action="{{ route('cart.clear') }}" method="POST">
                                @csrf
                                <x-button variant="secondary" size="md" type="submit">
                                    Kosongkan Keranjang
                                </x-button>
                            </form>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Ringkasan Pesanan</h3>
                            </x-slot>
                            <div class="space-y-3">
                                <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                    <span>Subtotal</span>
                                    <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                                </div>
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                                    <div class="flex justify-between text-lg font-bold">
                                        <span>Total</span>
                                        <span class="text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($total, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="pt-4">
                                    @auth
                                        <a href="{{ route('checkout.index') }}">
                                            <x-button variant="primary" size="lg" class="w-full">
                                                Lanjut ke Checkout
                                            </x-button>
                                        </a>
                                    @else
                                        <a href="{{ route('login') }}">
                                            <x-button variant="primary" size="lg" class="w-full">
                                                Login untuk Checkout
                                            </x-button>
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </x-card>
                    </div>
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Keranjang kosong</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai belanja untuk menambahkan produk ke keranjang.</p>
                        <div class="mt-6">
                            <a href="{{ route('products.index') }}">
                                <x-button variant="primary">Lihat Produk</x-button>
                            </a>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

