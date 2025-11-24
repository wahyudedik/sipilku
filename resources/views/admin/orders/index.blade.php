<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Manajemen Pesanan
            </h2>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari Order ID, nama, atau email..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'Semua Status',
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'cancelled' => 'Cancelled'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="payment_method" 
                    :options="[
                        'all' => 'Semua Metode',
                        'balance' => 'Saldo',
                        'manual' => 'Manual'
                    ]" 
                    value="{{ request('payment_method', 'all') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('admin.orders.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

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
                                <x-badge :variant="match($order->status) {
                                    'completed' => 'success',
                                    'processing' => 'warning',
                                    'pending' => 'warning',
                                    'cancelled' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($order->status) }}
                                </x-badge>
                                @if($order->payment_method === 'manual' && $order->status === 'pending')
                                    <x-badge variant="warning" size="sm">
                                        Menunggu Konfirmasi
                                    </x-badge>
                                @endif
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 space-y-1">
                                <p><strong>Order ID:</strong> {{ $order->uuid }}</p>
                                <p><strong>Pembeli:</strong> {{ $order->user->name }} ({{ $order->user->email }})</p>
                                <p><strong>Tanggal:</strong> {{ $order->created_at->format('d M Y H:i') }}</p>
                                <p><strong>Total:</strong> <span class="font-bold text-gray-900 dark:text-gray-100">Rp {{ number_format($order->total, 0, ',', '.') }}</span></p>
                                <p><strong>Metode:</strong> {{ ucfirst($order->payment_method ?? 'N/A') }}</p>
                            </div>
                        </div>
                        <div class="ml-4 flex flex-col items-end space-y-2">
                            <a href="{{ route('admin.orders.show', $order) }}">
                                <x-button variant="primary" size="sm">Detail</x-button>
                            </a>
                            @if($order->status === 'pending' && $order->payment_method === 'manual')
                                <form action="{{ route('admin.orders.confirm-payment', $order) }}" 
                                      method="POST"
                                      onsubmit="return confirm('Konfirmasi pembayaran untuk pesanan ini?')">
                                    @csrf
                                    <x-button variant="success" size="sm" type="submit">Konfirmasi Pembayaran</x-button>
                                </form>
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
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada pesanan yang ditemukan.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

