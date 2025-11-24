<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Laporan Komisi
            </h2>
            <a href="{{ route('seller.commissions.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <!-- Date Range Filter -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('seller.commissions.report') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[150px]">
                <x-form-group>
                    <x-slot name="label">Dari Tanggal</x-slot>
                    <x-text-input type="date" name="date_from" value="{{ $dateFrom }}" required />
                </x-form-group>
            </div>
            <div class="flex-1 min-w-[150px]">
                <x-form-group>
                    <x-slot name="label">Sampai Tanggal</x-slot>
                    <x-text-input type="date" name="date_to" value="{{ $dateTo }}" required />
                </x-form-group>
            </div>
            <div class="flex items-end gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('seller.commissions.report') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Summary -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Ringkasan</h3>
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Komisi</p>
                <h3 class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($totalAmount, 0, ',', '.') }}
                </h3>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Jumlah Transaksi</p>
                <h3 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                    {{ $totalCount }}
                </h3>
            </div>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Rata-rata per Transaksi</p>
                <h3 class="text-3xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ $totalCount > 0 ? number_format($totalAmount / $totalCount, 0, ',', '.') : 0 }}
                </h3>
            </div>
        </div>
    </x-card>

    <!-- By Month -->
    @if($byMonth->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Komisi per Bulan</h3>
            </x-slot>
            <div class="space-y-4">
                @foreach($byMonth as $monthData)
                    <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $monthData['month'] }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $monthData['count'] }} transaksi</p>
                        </div>
                        <p class="text-xl font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($monthData['total'], 0, ',', '.') }}
                        </p>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- By Item -->
    @if($byItem->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Komisi per Produk/Jasa</h3>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produk/Jasa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah Transaksi</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($byItem as $itemData)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $itemData['item'] ? $itemData['item']->title : 'Unknown' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $itemData['count'] }} transaksi
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($itemData['total'], 0, ',', '.') }}
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

    <!-- Detailed List -->
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium">Detail Transaksi</h3>
        </x-slot>
        @if($commissions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Produk/Jasa</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order ID</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Komisi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($commissions as $commission)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 dark:text-gray-400">
                                    {{ $commission->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $commission->order && $commission->order->orderable ? $commission->order->orderable->title : 'N/A' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($commission->order)
                                        <a href="{{ route('orders.show', $commission->order) }}" class="text-sm text-primary-600 hover:underline">
                                            {{ $commission->order->uuid }}
                                        </a>
                                    @else
                                        <span class="text-sm text-gray-500">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-semibold text-green-600 dark:text-green-400">
                                    +Rp {{ number_format($commission->amount, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>Tidak ada data komisi untuk periode ini.</p>
            </div>
        @endif
    </x-card>
</x-app-with-sidebar>

