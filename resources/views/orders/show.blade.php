<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Pesanan
            </h2>
            <a href="{{ route('orders.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">
                        Informasi {{ $order->type === 'service' ? 'Jasa' : 'Produk' }}
                    </h3>
                </x-slot>
                @if($order->orderable)
                    <div class="flex items-start space-x-4">
                        @if($order->orderable->preview_image)
                            <img src="{{ Storage::url($order->orderable->preview_image) }}" 
                                 alt="{{ $order->orderable->title }}"
                                 class="w-24 h-24 object-cover rounded-lg">
                        @endif
                        <div class="flex-1">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $order->orderable->title }}
                            </h4>
                            @if($order->orderable->category)
                                <x-badge variant="default" size="sm" class="mt-2">
                                    {{ $order->orderable->category->name }}
                                </x-badge>
                            @endif
                            @if($order->quoteRequest)
                                <div class="mt-2">
                                    <x-badge variant="info" size="sm">
                                        Dari Quote Request
                                    </x-badge>
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Item tidak ditemukan</p>
                @endif
            </x-card>

            <!-- Quote Request Info (for service orders) -->
            @if($order->quoteRequest)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Informasi Quote Request</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pesan/Kebutuhan Awal</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ Str::limit($order->quoteRequest->message, 200) }}</p>
                        </div>
                        @if($order->quoteRequest->requirements && count($order->quoteRequest->requirements) > 0)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Requirements</p>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach($order->quoteRequest->requirements as $requirement)
                                        <li class="text-gray-700 dark:text-gray-300">{{ $requirement }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if($order->quoteRequest->quote_message)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pesan Quote dari Seller</p>
                                <p class="text-gray-700 dark:text-gray-300">{{ Str::limit($order->quoteRequest->quote_message, 200) }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif

            <!-- Order Status -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Status Pesanan</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Status</span>
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
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Tanggal Pesanan</span>
                        <span class="font-medium">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>
                    @if($order->completed_at)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Tanggal Selesai</span>
                            <span class="font-medium">{{ $order->completed_at->format('d M Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Payment Instructions (for manual payment) -->
            @if($order->status === 'pending' && $order->payment_method === 'manual')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Instruksi Pembayaran</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <x-alert type="warning">
                            <strong>Pesanan Anda menunggu konfirmasi pembayaran.</strong>
                        </x-alert>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg space-y-2">
                            <p class="font-medium text-gray-900 dark:text-gray-100">Lakukan transfer ke:</p>
                            <div class="space-y-1 text-sm">
                                <p><span class="font-medium">Bank:</span> BCA / Mandiri / BRI</p>
                                <p><span class="font-medium">No. Rekening:</span> 1234567890</p>
                                <p><span class="font-medium">Atas Nama:</span> Sipilku Marketplace</p>
                                <p><span class="font-medium">Jumlah:</span> <span class="text-lg font-bold text-primary-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span></p>
                            </div>
                        </div>
                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <p class="text-sm text-gray-700 dark:text-gray-300">
                                <strong>Catatan:</strong> Setelah melakukan transfer, silakan hubungi admin untuk konfirmasi pembayaran. 
                                Setelah pembayaran dikonfirmasi, Anda akan dapat mengunduh file produk.
                            </p>
                        </div>
                        <div class="pt-2">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <strong>Order ID:</strong> {{ $order->uuid }}<br>
                                Gunakan Order ID ini saat konfirmasi pembayaran.
                            </p>
                        </div>
                    </div>
                </x-card>
            @endif

            <!-- Payment Button (for Midtrans payment) -->
            @if($order->status === 'pending' && $order->payment_method === 'midtrans')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Pembayaran Online</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <x-alert type="info">
                            <strong>Pesanan Anda menunggu pembayaran.</strong>
                        </x-alert>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">
                                Silakan klik tombol di bawah untuk melakukan pembayaran online melalui Midtrans.
                            </p>
                            <a href="{{ route('payments.process', $order) }}">
                                <x-button variant="primary" size="lg" class="w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    Bayar Sekarang - Rp {{ number_format($order->total, 0, ',', '.') }}
                                </x-button>
                            </a>
                        </div>
                    </div>
                </x-card>
            @endif

            <!-- Download Info -->
            @if($order->status === 'completed')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Informasi Download</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Download Tersisa</span>
                            <span class="font-medium">{{ $order->max_downloads - $order->download_count }} / {{ $order->max_downloads }}</span>
                        </div>
                        @if($order->download_expires_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Berlaku Hingga</span>
                                <span class="font-medium">{{ $order->download_expires_at->format('d M Y H:i') }}</span>
                            </div>
                        @endif
                        @if($order->canDownload())
                            <div class="pt-4">
                                <a href="{{ route('downloads.download', $order) }}">
                                    <x-button variant="primary" size="lg" class="w-full">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download File
                                    </x-button>
                                </a>
                                @if($order->download_token)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 text-center">
                                        Atau gunakan link: 
                                        <a href="{{ route('downloads.token', $order->download_token) }}" class="text-primary-600 hover:underline">
                                            {{ route('downloads.token', $order->download_token) }}
                                        </a>
                                    </p>
                                @endif
                            </div>
                        @else
                            <x-alert type="error">
                                @if($order->download_expires_at && $order->download_expires_at->isPast())
                                    Link download telah kedaluwarsa. Silakan hubungi admin.
                                @elseif($order->download_count >= $order->max_downloads)
                                    Batas download telah tercapai. Silakan hubungi admin.
                                @else
                                    Download tidak tersedia.
                                @endif
                            </x-alert>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Ringkasan Pembayaran</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                        <span>Subtotal</span>
                        <span>Rp {{ number_format($order->amount, 0, ',', '.') }}</span>
                    </div>
                    @if($order->discount > 0)
                        <div class="flex justify-between text-green-600 dark:text-green-400">
                            <span>Diskon</span>
                            <span>- Rp {{ number_format($order->discount, 0, ',', '.') }}</span>
                        </div>
                    @endif
                    <div class="border-t border-gray-200 dark:border-gray-700 pt-3">
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total</span>
                            <span class="text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($order->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600 dark:text-gray-400">Metode Pembayaran</span>
                            <span class="font-medium">{{ ucfirst($order->payment_method ?? 'N/A') }}</span>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

