<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Financial Reports
            </h2>
            <a href="{{ route('admin.financial.transactions') }}">
                <x-button variant="secondary" size="sm">Transaction Monitoring</x-button>
            </a>
        </div>
    </x-slot>

    <!-- Date Range Filter -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.financial.reports') }}" class="flex flex-wrap gap-4">
            <div>
                <x-form-group label="From Date" name="date_from">
                    <x-text-input name="date_from" type="date" value="{{ $dateFrom }}" />
                </x-form-group>
            </div>
            <div>
                <x-form-group label="To Date" name="date_to">
                    <x-text-input name="date_to" type="date" value="{{ $dateTo }}" />
                </x-form-group>
            </div>
            <div class="flex items-end">
                <x-button variant="primary" size="md" type="submit">Generate Report</x-button>
            </div>
        </form>
    </x-card>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Revenue</p>
                <h3 class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Commissions</p>
                <h3 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    Rp {{ number_format($totalCommissions, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Payouts</p>
                <h3 class="text-2xl font-bold text-red-600 dark:text-red-400">
                    Rp {{ number_format($totalPayouts, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Platform Profit</p>
                <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($platformProfit, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Revenue by Type -->
        @if($revenueByType->count() > 0)
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Revenue by Type</h3>
                </x-slot>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3">Type</th>
                                <th class="text-right p-3">Total</th>
                                <th class="text-right p-3">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenueByType as $item)
                                <tr class="border-b">
                                    <td class="p-3 font-medium">{{ ucfirst($item->type) }}</td>
                                    <td class="p-3 text-right font-semibold">
                                        {{ $item->type === 'purchase' ? '+' : '' }}Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right">{{ $item->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif

        <!-- Revenue by Payment Method -->
        @if($revenueByPaymentMethod->count() > 0)
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Revenue by Payment Method</h3>
                </x-slot>
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b">
                                <th class="text-left p-3">Payment Method</th>
                                <th class="text-right p-3">Total</th>
                                <th class="text-right p-3">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($revenueByPaymentMethod as $item)
                                <tr class="border-b">
                                    <td class="p-3 font-medium">{{ ucfirst($item->payment_method ?? 'Unknown') }}</td>
                                    <td class="p-3 text-right font-semibold">
                                        Rp {{ number_format($item->total, 0, ',', '.') }}
                                    </td>
                                    <td class="p-3 text-right">{{ $item->count }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif
    </div>

    <!-- Revenue by Month -->
    @if($revenueByMonth->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Revenue by Month</h3>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Month</th>
                            <th class="text-right p-3">Total Revenue</th>
                            <th class="text-right p-3">Order Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($revenueByMonth as $item)
                            <tr class="border-b">
                                <td class="p-3 font-medium">{{ $item->year }}-{{ str_pad($item->month, 2, '0', STR_PAD_LEFT) }}</td>
                                <td class="p-3 text-right font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($item->total, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right">{{ $item->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

    <!-- Top Sellers -->
    @if($topSellers->count() > 0)
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Top Sellers by Commission</h3>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Rank</th>
                            <th class="text-left p-3">Seller</th>
                            <th class="text-right p-3">Total Commissions</th>
                            <th class="text-right p-3">Transaction Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topSellers as $index => $seller)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-3 font-bold">#{{ $index + 1 }}</td>
                                <td class="p-3">
                                    <div>
                                        <p class="font-medium">{{ $seller->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $seller->user->email }}</p>
                                    </div>
                                </td>
                                <td class="p-3 text-right font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($seller->total, 0, ',', '.') }}
                                </td>
                                <td class="p-3 text-right">{{ $seller->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

