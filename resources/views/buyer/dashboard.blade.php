<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Dashboard
        </h2>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Pesanan</p>
                <h3 class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $totalOrders }}
                </h3>
                <a href="{{ route('orders.index') }}" class="text-xs text-primary-600 hover:underline mt-2 inline-block">
                    Lihat Semua
                </a>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pesanan Selesai</p>
                <h3 class="text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ $completedOrders }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pesanan Pending</p>
                <h3 class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ $pendingOrders }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Pengeluaran</p>
                <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($totalSpent, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
    </div>

    <!-- Account Balance -->
    <x-card class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Saldo Akun</p>
                <h3 class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($balance, 0, ',', '.') }}
                </h3>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('balance.index') }}">
                    <x-button variant="primary" size="sm">Kelola Saldo</x-button>
                </a>
                <a href="{{ route('balance.top-up') }}">
                    <x-button variant="secondary" size="sm">Top-up</x-button>
                </a>
            </div>
        </div>
    </x-card>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Purchases -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Pembelian Terakhir</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-primary-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </x-slot>
            @if($recentPurchases->count() > 0)
                <div class="space-y-4">
                    @foreach($recentPurchases as $order)
                        <div class="flex items-start space-x-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @if($order->orderable && $order->orderable->preview_image)
                                <img src="{{ Storage::url($order->orderable->preview_image) }}" 
                                     alt="{{ $order->orderable->title }}"
                                     class="w-16 h-16 object-cover rounded">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $order->orderable ? $order->orderable->title : 'Item' }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Order: {{ $order->uuid }}
                                </p>
                                <div class="flex items-center space-x-2 mt-2">
                                    <x-badge :variant="match($order->status) {
                                        'completed' => 'success',
                                        'processing' => 'warning',
                                        'pending' => 'default',
                                        'cancelled' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($order->status) }}
                                    </x-badge>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada pembelian</p>
                    <a href="{{ route('products.index') }}" class="mt-4 inline-block">
                        <x-button variant="primary" size="sm">Jelajahi Produk</x-button>
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Active Service Orders -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Pesanan Jasa Aktif</h3>
                    <a href="{{ route('orders.index', ['type' => 'service']) }}" class="text-sm text-primary-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </x-slot>
            @if($activeServiceOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($activeServiceOrders as $order)
                        <div class="flex items-start space-x-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @if($order->orderable && $order->orderable->preview_image)
                                <img src="{{ Storage::url($order->orderable->preview_image) }}" 
                                     alt="{{ $order->orderable->title }}"
                                     class="w-16 h-16 object-cover rounded">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $order->orderable ? $order->orderable->title : 'Jasa' }}
                                </h4>
                                @if($order->quoteRequest)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Dari Quote Request
                                    </p>
                                @endif
                                <div class="flex items-center space-x-2 mt-2">
                                    <x-badge :variant="match($order->status) {
                                        'processing' => 'warning',
                                        'pending' => 'default',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($order->status) }}
                                    </x-badge>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>
                            <a href="{{ route('orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Tidak ada pesanan jasa aktif</p>
                    <a href="{{ route('services.index') }}" class="mt-4 inline-block">
                        <x-button variant="primary" size="sm">Jelajahi Jasa</x-button>
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Pending Quotes -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Quote Pending</h3>
                    <a href="{{ route('buyer.quote-requests.index') }}" class="text-sm text-primary-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </x-slot>
            @if($pendingQuotes->count() > 0)
                <div class="space-y-4">
                    @foreach($pendingQuotes as $quote)
                        <div class="flex items-start space-x-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @if($quote->service && $quote->service->preview_image)
                                <img src="{{ Storage::url($quote->service->preview_image) }}" 
                                     alt="{{ $quote->service->title }}"
                                     class="w-16 h-16 object-cover rounded">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $quote->service ? $quote->service->title : 'Jasa' }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ \Illuminate\Support\Str::limit($quote->message, 50) }}
                                </p>
                                <div class="flex items-center space-x-2 mt-2">
                                    <x-badge :variant="match($quote->status) {
                                        'quoted' => 'success',
                                        'pending' => 'default',
                                        default => 'default'
                                    }">
                                        {{ $quote->status === 'quoted' ? 'Sudah Diquote' : 'Menunggu Quote' }}
                                    </x-badge>
                                    @if($quote->status === 'quoted' && $quote->quoted_price)
                                        <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($quote->quoted_price, 0, ',', '.') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('buyer.quote-requests.show', $quote) }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Tidak ada quote pending</p>
                    <a href="{{ route('services.index') }}" class="mt-4 inline-block">
                        <x-button variant="primary" size="sm">Request Quote</x-button>
                    </a>
                </div>
            @endif
        </x-card>

        <!-- Download History -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Riwayat Download</h3>
                    <a href="{{ route('downloads.history') }}" class="text-sm text-primary-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </x-slot>
            @if($downloadHistory->count() > 0)
                <div class="space-y-4">
                    @foreach($downloadHistory as $order)
                        <div class="flex items-start space-x-4 p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            @if($order->orderable && $order->orderable->preview_image)
                                <img src="{{ Storage::url($order->orderable->preview_image) }}" 
                                     alt="{{ $order->orderable->title }}"
                                     class="w-16 h-16 object-cover rounded">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $order->orderable ? $order->orderable->title : 'Item' }}
                                </h4>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Downloaded: {{ $order->download_count }}x
                                </p>
                                @if($order->download_expires_at)
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        Expires: {{ $order->download_expires_at->format('d M Y') }}
                                    </p>
                                @endif
                                <div class="mt-2">
                                    @if($order->canDownload())
                                        <a href="{{ route('downloads.download', $order) }}">
                                            <x-button variant="primary" size="sm">Download</x-button>
                                        </a>
                                    @else
                                        <x-badge variant="danger" size="sm">Expired</x-badge>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada download</p>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Quick Actions -->
    <x-card class="mt-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Aksi Cepat</h3>
        </x-slot>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('products.index') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Jelajahi Produk</span>
            </a>
            <a href="{{ route('services.index') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Jelajahi Jasa</span>
            </a>
            <a href="{{ route('profile.edit') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Edit Profil</span>
            </a>
            <a href="{{ route('balance.index') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Kelola Saldo</span>
            </a>
        </div>
    </x-card>
</x-app-with-sidebar>

