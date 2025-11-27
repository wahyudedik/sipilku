<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Store Analytics & Reporting
            </h2>
            <div class="flex items-center space-x-2">
                <select id="dateRange" onchange="updateDateRange()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-sm">
                    <option value="7" {{ $dateRange == 7 ? 'selected' : '' }}>Last 7 days</option>
                    <option value="30" {{ $dateRange == 30 ? 'selected' : '' }}>Last 30 days</option>
                    <option value="90" {{ $dateRange == 90 ? 'selected' : '' }}>Last 90 days</option>
                    <option value="365" {{ $dateRange == 365 ? 'selected' : '' }}>Last year</option>
                </select>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Store View Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <x-card>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Views</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                                    {{ number_format($viewStats['total_views']) }}
                                </p>
                            </div>
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center text-sm">
                            @if($viewStats['views_growth'] > 0)
                                <span class="text-green-600 dark:text-green-400">↑ {{ number_format($viewStats['views_growth'], 1) }}%</span>
                            @elseif($viewStats['views_growth'] < 0)
                                <span class="text-red-600 dark:text-red-400">↓ {{ number_format(abs($viewStats['views_growth']), 1) }}%</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                            <span class="text-gray-500 ml-1">vs previous period</span>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Unique Views</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                                    {{ number_format($viewStats['unique_views']) }}
                                </p>
                            </div>
                            <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Total Sales</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                                    Rp {{ number_format($salesReports['total_sales'], 0, ',', '.') }}
                                </p>
                            </div>
                            <div class="p-3 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                        </div>
                        <div class="mt-2 flex items-center text-sm">
                            @if($salesReports['sales_growth'] > 0)
                                <span class="text-green-600 dark:text-green-400">↑ {{ number_format($salesReports['sales_growth'], 1) }}%</span>
                            @elseif($salesReports['sales_growth'] < 0)
                                <span class="text-red-600 dark:text-red-400">↓ {{ number_format(abs($salesReports['sales_growth']), 1) }}%</span>
                            @else
                                <span class="text-gray-500">-</span>
                            @endif
                            <span class="text-gray-500 ml-1">vs previous period</span>
                        </div>
                    </div>
                </x-card>

                <x-card>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Conversion Rate</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                                    {{ number_format($performanceMetrics['conversion_rate'], 2) }}%
                                </p>
                            </div>
                            <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Performance Metrics -->
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Performance Metrics</h3>
                </x-slot>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Average Response Time</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                            {{ number_format($performanceMetrics['average_response_time'], 1) }} hours
                        </p>
                    </div>
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Completion Rate</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                            {{ number_format($performanceMetrics['completion_rate'], 2) }}%
                        </p>
                    </div>
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Customer Retention</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                            {{ number_format($performanceMetrics['customer_retention_rate'], 2) }}%
                        </p>
                    </div>
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Average Rating</p>
                        <p class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">
                            {{ number_format($performanceMetrics['average_rating'], 1) }}/5
                        </p>
                    </div>
                </div>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Daily Views Chart -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Daily Views</h3>
                    </x-slot>
                    <div class="p-4">
                        <canvas id="dailyViewsChart" height="200"></canvas>
                    </div>
                </x-card>

                <!-- Daily Sales Chart -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Daily Sales</h3>
                    </x-slot>
                    <div class="p-4">
                        <canvas id="dailySalesChart" height="200"></canvas>
                    </div>
                </x-card>
            </div>

            <!-- Product Popularity -->
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Product Popularity Analytics</h3>
                </x-slot>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Requests</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Quantity</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Revenue</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($productAnalytics['product_performance'] as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['product']->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $item['product']->sku ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $item['requests_count'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ number_format($item['total_quantity']) }} {{ $item['product']->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($item['total_revenue'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No product data available
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>

            <!-- Sales Reports -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Sales by Category -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Sales by Category</h3>
                    </x-slot>
                    <div class="p-4">
                        <div class="space-y-3">
                            @forelse($salesReports['sales_by_category'] as $category)
                                <div>
                                    <div class="flex justify-between items-center mb-1">
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $category->category }}</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($category->sales, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $salesReports['total_sales'] > 0 ? ($category->sales / $salesReports['total_sales'] * 100) : 0 }}%"></div>
                                    </div>
                                    <div class="text-xs text-gray-500 mt-1">{{ $category->orders }} orders</div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 dark:text-gray-400">No category data available</p>
                            @endforelse
                        </div>
                    </div>
                </x-card>

                <!-- Top Customers -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Top Customers</h3>
                    </x-slot>
                    <div class="p-4">
                        <div class="space-y-3">
                            @forelse($salesReports['top_customers'] as $customer)
                                <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $customer['user']->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $customer['orders'] }} orders</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900 dark:text-gray-100">
                                            Rp {{ number_format($customer['total_spent'], 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-500 dark:text-gray-400">No customer data available</p>
                            @endforelse
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Store Comparison Report -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Store Comparison Report</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Compare your store performance with platform averages
                    </p>
                </x-slot>
                <div class="p-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Store Views</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($comparisonData['current_store']['views']) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Avg: {{ number_format($comparisonData['all_stores_avg']['avg_views'], 0) }}
                            </p>
                            @php
                                $viewsDiff = $comparisonData['all_stores_avg']['avg_views'] > 0 
                                    ? (($comparisonData['current_store']['views'] - $comparisonData['all_stores_avg']['avg_views']) / $comparisonData['all_stores_avg']['avg_views']) * 100 
                                    : 0;
                            @endphp
                            <p class="text-xs mt-1 {{ $viewsDiff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $viewsDiff >= 0 ? '+' : '' }}{{ number_format($viewsDiff, 1) }}% vs average
                            </p>
                        </div>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Sales</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                Rp {{ number_format($comparisonData['current_store']['sales'], 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Avg: Rp {{ number_format($comparisonData['all_stores_avg']['avg_sales'], 0, ',', '.') }}
                            </p>
                            @php
                                $salesDiff = $comparisonData['all_stores_avg']['avg_sales'] > 0 
                                    ? (($comparisonData['current_store']['sales'] - $comparisonData['all_stores_avg']['avg_sales']) / $comparisonData['all_stores_avg']['avg_sales']) * 100 
                                    : 0;
                            @endphp
                            <p class="text-xs mt-1 {{ $salesDiff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $salesDiff >= 0 ? '+' : '' }}{{ number_format($salesDiff, 1) }}% vs average
                            </p>
                        </div>
                        <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                {{ number_format($comparisonData['current_store']['orders']) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                Avg: {{ number_format($comparisonData['all_stores_avg']['avg_orders'], 0) }}
                            </p>
                            @php
                                $ordersDiff = $comparisonData['all_stores_avg']['avg_orders'] > 0 
                                    ? (($comparisonData['current_store']['orders'] - $comparisonData['all_stores_avg']['avg_orders']) / $comparisonData['all_stores_avg']['avg_orders']) * 100 
                                    : 0;
                            @endphp
                            <p class="text-xs mt-1 {{ $ordersDiff >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $ordersDiff >= 0 ? '+' : '' }}{{ number_format($ordersDiff, 1) }}% vs average
                            </p>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        function updateDateRange() {
            const range = document.getElementById('dateRange').value;
            window.location.href = '{{ route("store.analytics.index") }}?range=' + range;
        }

        // Daily Views Chart
        const dailyViewsCtx = document.getElementById('dailyViewsChart');
        if (dailyViewsCtx) {
            new Chart(dailyViewsCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($viewStats['daily_views']->pluck('date')) !!},
                    datasets: [{
                        label: 'Views',
                        data: {!! json_encode($viewStats['daily_views']->pluck('views')) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }

        // Daily Sales Chart
        const dailySalesCtx = document.getElementById('dailySalesChart');
        if (dailySalesCtx) {
            new Chart(dailySalesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($salesReports['daily_sales']->pluck('date')) !!},
                    datasets: [{
                        label: 'Sales (Rp)',
                        data: {!! json_encode($salesReports['daily_sales']->pluck('sales')) !!},
                        backgroundColor: 'rgba(34, 197, 94, 0.5)',
                        borderColor: 'rgb(34, 197, 94)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'Rp ' + (value / 1000).toFixed(0) + 'k';
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
    @endpush
</x-app-layout>

