<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Dashboard Seller
        </h2>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Products -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Produk</p>
                <h3 class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $totalProducts }}
                </h3>
                <div class="flex justify-center space-x-4 mt-2 text-xs">
                    <span class="text-green-600 dark:text-green-400">{{ $approvedProducts }} Disetujui</span>
                    <span class="text-yellow-600 dark:text-yellow-400">{{ $pendingProducts }} Pending</span>
                </div>
                <a href="{{ route('seller.products.index') }}" class="mt-4 inline-block">
                    <x-button variant="primary" size="sm">Kelola Produk</x-button>
                </a>
            </div>
        </x-card>

        <!-- Services -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Jasa</p>
                <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $totalServices }}
                </h3>
                <div class="flex justify-center space-x-4 mt-2 text-xs">
                    <span class="text-green-600 dark:text-green-400">{{ $approvedServices }} Disetujui</span>
                    <span class="text-yellow-600 dark:text-yellow-400">{{ $pendingServices }} Pending</span>
                </div>
                <a href="{{ route('seller.services.index') }}" class="mt-4 inline-block">
                    <x-button variant="primary" size="sm">Kelola Jasa</x-button>
                </a>
            </div>
        </x-card>

        <!-- Total Revenue -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Pendapatan</p>
                <h3 class="text-3xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
                <div class="flex justify-center space-x-4 mt-2 text-xs">
                    <span>Produk: Rp {{ number_format($totalProductRevenue, 0, ',', '.') }}</span>
                    <span>Jasa: Rp {{ number_format($totalServiceRevenue, 0, ',', '.') }}</span>
                </div>
            </div>
        </x-card>

        <!-- Available Balance -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Saldo Tersedia</p>
                <h3 class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </h3>
                <div class="flex justify-center space-x-2 mt-2">
                    <a href="{{ route('seller.commissions.payout') }}">
                        <x-button variant="primary" size="sm">Tarik Saldo</x-button>
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Sales & Commission Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Penjualan Produk</h3>
            </x-slot>
            <div class="text-center">
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ $totalProductSales }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Penjualan</p>
            </div>
        </x-card>

        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Pesanan Jasa</h3>
            </x-slot>
            <div class="text-center">
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $totalServiceOrders }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Total Pesanan</p>
            </div>
        </x-card>

        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Total Komisi</h3>
            </x-slot>
            <div class="text-center">
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($totalCommissions, 0, ',', '.') }}
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    Pending: Rp {{ number_format($pendingCommissions, 0, ',', '.') }}
                </p>
            </div>
        </x-card>
    </div>

    <!-- Monthly Revenue Chart -->
    @if($monthlyRevenue->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Pendapatan Bulanan (6 Bulan Terakhir)</h3>
            </x-slot>
            <div class="space-y-4">
                @foreach($monthlyRevenue->reverse() as $revenue)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">
                            {{ \Carbon\Carbon::create($revenue->year, $revenue->month, 1)->format('F Y') }}
                        </span>
                        <div class="flex items-center space-x-4">
                            <div class="w-48 bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-primary-600 h-4 rounded-full" style="width: {{ min(100, ($revenue->total / ($totalRevenue > 0 ? $totalRevenue : 1)) * 100) }}%"></div>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100 w-32 text-right">
                                Rp {{ number_format($revenue->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Pesanan Terbaru</h3>
                    <a href="{{ route('orders.index') }}" class="text-sm text-primary-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </x-slot>
            @if($recentOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($recentOrders as $order)
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
                                    Pembeli: {{ $order->user->name }}
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
                    <p>Belum ada pesanan</p>
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
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Pembeli: {{ $order->user->name }}
                                </p>
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
                </div>
            @endif
        </x-card>

        <!-- Top Selling Products -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Produk Terlaris</h3>
                    <a href="{{ route('seller.products.index') }}" class="text-sm text-primary-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </x-slot>
            @if($topProducts->count() > 0)
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                        <div class="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            @if($product->preview_image)
                                <img src="{{ Storage::url($product->preview_image) }}" 
                                     alt="{{ $product->title }}"
                                     class="w-12 h-12 object-cover rounded">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $product->title }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Terjual: {{ $product->sales_count }}x
                                </p>
                            </div>
                            <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($product->final_price, 0, ',', '.') }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada produk</p>
                </div>
            @endif
        </x-card>

        <!-- Top Services -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Jasa Terpopuler</h3>
                    <a href="{{ route('seller.services.index') }}" class="text-sm text-primary-600 hover:underline">
                        Lihat Semua
                    </a>
                </div>
            </x-slot>
            @if($topServices->count() > 0)
                <div class="space-y-3">
                    @foreach($topServices as $service)
                        <div class="flex items-center space-x-3 p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            @if($service->preview_image)
                                <img src="{{ Storage::url($service->preview_image) }}" 
                                     alt="{{ $service->title }}"
                                     class="w-12 h-12 object-cover rounded">
                            @endif
                            <div class="flex-1 min-w-0">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100 truncate">
                                    {{ $service->title }}
                                </h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    Selesai: {{ $service->completed_orders }}x
                                </p>
                            </div>
                            @if($service->package_prices && count($service->package_prices) > 0)
                                <span class="text-sm font-semibold text-blue-600 dark:text-blue-400">
                                    Mulai: Rp {{ number_format(min(array_column($service->package_prices, 'price')), 0, ',', '.') }}
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    <p>Belum ada jasa</p>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Withdrawal History -->
    <x-card class="mt-6">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium">Riwayat Penarikan</h3>
                <a href="{{ route('seller.commissions.index') }}" class="text-sm text-primary-600 hover:underline">
                    Lihat Semua
                </a>
            </div>
        </x-slot>
        @if($recentWithdrawals->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Metode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($recentWithdrawals as $withdrawal)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $withdrawal->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $withdrawal->method === 'bank_transfer' ? 'Transfer Bank' : 'E-Wallet' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <x-badge :variant="match($withdrawal->status) {
                                        'completed' => 'success',
                                        'processing' => 'warning',
                                        'pending' => 'default',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($withdrawal->status) }}
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                    <a href="{{ route('seller.commissions.show-withdrawal', $withdrawal) }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                <p>Belum ada penarikan</p>
                <a href="{{ route('seller.commissions.payout') }}" class="mt-4 inline-block">
                    <x-button variant="primary" size="sm">Ajukan Penarikan</x-button>
                </a>
            </div>
        @endif
    </x-card>

    <!-- Quick Actions -->
    <x-card class="mt-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Aksi Cepat</h3>
        </x-slot>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('seller.products.create') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Tambah Produk</span>
            </a>
            <a href="{{ route('seller.services.create') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Tambah Jasa</span>
            </a>
            <a href="{{ route('seller.commissions.payout') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Tarik Saldo</span>
            </a>
            <a href="{{ route('seller.commissions.report') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Laporan Komisi</span>
            </a>
        </div>
    </x-card>
</x-app-with-sidebar>

