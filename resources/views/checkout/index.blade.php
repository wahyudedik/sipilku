<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Checkout
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Order Items -->
                    <div class="lg:col-span-2 space-y-4">
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Produk yang Dibeli</h3>
                            </x-slot>
                            <div class="space-y-4">
                                @foreach($items as $item)
                                    <div class="flex items-start space-x-4 pb-4 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                        @if($item['product']->preview_image)
                                            <img src="{{ Storage::url($item['product']->preview_image) }}" 
                                                 alt="{{ $item['product']->title }}"
                                                 class="w-16 h-16 object-cover rounded">
                                        @endif
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $item['product']->title }}
                                            </h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $item['product']->category->name ?? 'Uncategorized' }}
                                            </p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-primary-600 dark:text-primary-400">
                                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>

                        <!-- Payment Method -->
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Metode Pembayaran</h3>
                            </x-slot>
                            <div class="space-y-3">
                                <label class="flex items-center p-4 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="radio" name="payment_method" value="balance" class="mr-3" 
                                           {{ auth()->user()->balance >= $total ? 'checked' : '' }}
                                           {{ auth()->user()->balance < $total ? 'disabled' : '' }}>
                                    <div class="flex-1">
                                        <div class="font-medium">Saldo Akun</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Saldo tersedia: Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}
                                        </div>
                                        @if(auth()->user()->balance < $total)
                                            <div class="text-sm text-red-600 dark:text-red-400 mt-1">
                                                Saldo tidak mencukupi
                                            </div>
                                        @endif
                                    </div>
                                </label>
                                @if(config('services.midtrans.server_key'))
                                    <label class="flex items-center p-4 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                        <input type="radio" name="payment_method" value="midtrans" class="mr-3" 
                                               {{ auth()->user()->balance < $total && !old('payment_method') ? 'checked' : (old('payment_method') === 'midtrans' ? 'checked' : '') }}>
                                        <div class="flex-1">
                                            <div class="font-medium">Pembayaran Online (Midtrans)</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                Bayar dengan kartu kredit/debit, e-wallet, atau virtual account
                                            </div>
                                        </div>
                                    </label>
                                @endif
                                <label class="flex items-center p-4 border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <input type="radio" name="payment_method" value="manual" class="mr-3" 
                                           {{ auth()->user()->balance < $total && !config('services.midtrans.server_key') ? 'checked' : (old('payment_method') === 'manual' ? 'checked' : '') }}>
                                    <div class="flex-1">
                                        <div class="font-medium">Transfer Manual</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            Transfer ke rekening dan konfirmasi pembayaran
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </x-card>
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
                                <div class="pt-4 space-y-3">
                                    <x-button variant="primary" size="lg" type="submit" class="w-full">
                                        Konfirmasi Pembayaran
                                    </x-button>
                                    <a href="{{ route('cart.index') }}">
                                        <x-button variant="secondary" size="md" type="button" class="w-full">
                                            Kembali ke Keranjang
                                        </x-button>
                                    </a>
                                </div>
                            </div>
                        </x-card>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

