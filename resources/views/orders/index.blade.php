<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Riwayat Pesanan
        </h2>
    </x-slot>

    @if($orders->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($orders as $order)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-2">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        @if($order->orderable)
                                            {{ $order->orderable->title }}
                                        @else
                                            Item tidak ditemukan
                                        @endif
                                    </h3>
                                    <div class="flex items-center space-x-2 mt-1">
                                        @if($order->type === 'service')
                                            <x-badge variant="info" size="sm">
                                                Jasa
                                            </x-badge>
                                        @endif
                                        @if($order->quoteRequest)
                                            <x-badge variant="default" size="sm">
                                                Dari Quote
                                            </x-badge>
                                        @endif
                                    </div>
                                </div>
                                <x-badge :variant="match($order->status) {
                                    'completed' => 'success',
                                    'processing' => 'warning',
                                    'pending' => 'default',
                                    'cancelled' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($order->status) }}
                                </x-badge>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                                <p>Tanggal: {{ $order->created_at->format('d M Y H:i') }}</p>
                                <p>Total: <span class="font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($order->total, 0, ',', '.') }}</span></p>
                                <p>Metode Pembayaran: {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="ml-4 flex flex-col items-end space-y-2">
                            <a href="{{ route('orders.show', $order) }}">
                                <x-button variant="primary" size="sm">Detail</x-button>
                            </a>
                            @if($order->canDownload())
                                <a href="{{ route('downloads.download', $order) }}">
                                    <x-button variant="success" size="sm">Download</x-button>
                                </a>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada pesanan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum memiliki pesanan.</p>
                <div class="mt-6">
                    <a href="{{ route('products.index') }}">
                        <x-button variant="primary">Lihat Produk</x-button>
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

