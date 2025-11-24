<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Status Pembayaran
        </h2>
    </x-slot>

    <x-card>
        <div class="text-center py-8">
            @if(request('status') === 'success')
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900 mb-4">
                    <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Pembayaran Berhasil!</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Pembayaran Anda telah berhasil diproses. Pesanan akan segera diproses.
                </p>
            @elseif(request('status') === 'pending')
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-yellow-100 dark:bg-yellow-900 mb-4">
                    <svg class="h-8 w-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Pembayaran Pending</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Pembayaran Anda sedang menunggu konfirmasi. Kami akan mengirimkan notifikasi setelah pembayaran dikonfirmasi.
                </p>
            @else
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900 mb-4">
                    <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">Pembayaran Gagal</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    Pembayaran Anda gagal diproses. Silakan coba lagi atau gunakan metode pembayaran lain.
                </p>
            @endif

            <div class="space-y-4">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 text-left max-w-md mx-auto">
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Order ID:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">{{ $order->uuid }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total:</span>
                            <span class="font-medium text-gray-900 dark:text-gray-100">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                        @if($transaction)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                <x-badge :variant="match($transaction->status) {
                                    'completed' => 'success',
                                    'processing' => 'warning',
                                    'pending' => 'default',
                                    'failed' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($transaction->status) }}
                                </x-badge>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="flex justify-center space-x-4">
                    <a href="{{ route('orders.show', $order) }}">
                        <x-button variant="primary">Lihat Detail Pesanan</x-button>
                    </a>
                    @if(request('status') === 'error' || request('status') === 'pending')
                        <a href="{{ route('payments.process', $order) }}">
                            <x-button variant="secondary">Coba Lagi</x-button>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </x-card>
</x-app-with-sidebar>

