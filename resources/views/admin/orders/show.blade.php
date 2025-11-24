<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Pesanan
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.orders.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Info -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Informasi Pesanan</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Order ID</span>
                        <span class="font-mono text-sm">{{ $order->uuid }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Status</span>
                        <x-badge :variant="match($order->status) {
                            'completed' => 'success',
                            'processing' => 'warning',
                            'pending' => 'warning',
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

            <!-- Product Info -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Produk</h3>
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
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">Produk tidak ditemukan</p>
                @endif
            </x-card>

            <!-- Buyer Info -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Informasi Pembeli</h3>
                </x-slot>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nama</span>
                        <span class="font-medium">{{ $order->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Email</span>
                        <span class="font-medium">{{ $order->user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Saldo</span>
                        <span class="font-medium">Rp {{ number_format($order->user->balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>

            <!-- Payment Confirmation (for manual payment) -->
            @if($order->status === 'pending' && $order->payment_method === 'manual')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Konfirmasi Pembayaran</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <x-alert type="warning">
                            <strong>Pesanan ini menunggu konfirmasi pembayaran manual.</strong>
                        </x-alert>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="font-medium mb-2">Instruksi yang diberikan ke pembeli:</p>
                            <div class="text-sm space-y-1">
                                <p><strong>Bank:</strong> BCA / Mandiri / BRI</p>
                                <p><strong>No. Rekening:</strong> 1234567890</p>
                                <p><strong>Atas Nama:</strong> Sipilku Marketplace</p>
                                <p><strong>Jumlah:</strong> <span class="text-lg font-bold text-primary-600">Rp {{ number_format($order->total, 0, ',', '.') }}</span></p>
                            </div>
                        </div>
                        <div class="pt-4">
                            <form action="{{ route('admin.orders.confirm-payment', $order) }}" 
                                  method="POST"
                                  onsubmit="return confirm('Konfirmasi pembayaran untuk pesanan ini? Setelah dikonfirmasi, pembeli akan dapat mengunduh file.')">
                                @csrf
                                <x-button variant="success" size="lg" type="submit" class="w-full">
                                    ✓ Konfirmasi Pembayaran
                                </x-button>
                            </form>
                        </div>
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

            @if($order->status === 'completed')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Informasi Download</h3>
                    </x-slot>
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Download Count</span>
                            <span class="font-medium">{{ $order->download_count }} / {{ $order->max_downloads }}</span>
                        </div>
                        @if($order->download_expires_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Berlaku Hingga</span>
                                <span class="font-medium">{{ $order->download_expires_at->format('d M Y H:i') }}</span>
                            </div>
                        @endif
                        @if($order->download_token)
                            <div class="pt-2">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Download Token:</p>
                                <p class="font-mono text-xs break-all">{{ $order->download_token }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-with-sidebar>

