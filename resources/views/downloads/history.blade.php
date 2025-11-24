<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Riwayat Download
        </h2>
    </x-slot>

    @if($orders->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($orders as $order)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-4 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    @if($order->orderable)
                                        {{ $order->orderable->title }}
                                    @else
                                        Produk tidak ditemukan
                                    @endif
                                </h3>
                                @if($order->canDownload())
                                    <x-badge variant="success" size="sm">Dapat Diunduh</x-badge>
                                @else
                                    <x-badge variant="danger" size="sm">Tidak Dapat Diunduh</x-badge>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                                <p>Diunduh: {{ $order->download_count }} / {{ $order->max_downloads }} kali</p>
                                @if($order->download_expires_at)
                                    <p>Berlaku hingga: {{ $order->download_expires_at->format('d M Y H:i') }}</p>
                                @endif
                                <p>Tanggal pembelian: {{ $order->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                        <div class="ml-4 flex flex-col items-end space-y-2">
                            @if($order->canDownload())
                                <a href="{{ route('downloads.download', $order) }}">
                                    <x-button variant="primary" size="sm">Download</x-button>
                                </a>
                            @else
                                <x-button variant="secondary" size="sm" disabled>Download</x-button>
                            @endif
                            <a href="{{ route('orders.show', $order) }}">
                                <x-button variant="secondary" size="sm">Detail</x-button>
                            </a>
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
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada riwayat download</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum memiliki riwayat download.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

