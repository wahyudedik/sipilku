@extends('layouts.app')

@section('title', 'Factory Analytics Dashboard - ' . $factory->name)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $factory->name }} - Analytics Dashboard</h1>
                    <p class="text-gray-600 mt-2">{{ $factory->factoryType->name ?? 'Factory' }} Analytics & Reporting</p>
                </div>
                <div class="flex space-x-4">
                    <form method="GET" class="flex items-center space-x-2">
                        <input type="date" name="start_date" value="{{ $startDate }}" class="border rounded px-3 py-2">
                        <input type="date" name="end_date" value="{{ $endDate }}" class="border rounded px-3 py-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            Apply
                        </button>
                    </form>
                    <button onclick="exportReport('overview')"
                        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                        Export Report
                    </button>
                </div>
            </div>
        </div>

        <!-- Overview Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Views</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ number_format($analytics['overview']['total_views']) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ number_format($analytics['overview']['unique_visitors']) }}
                            unique visitors</p>
                    </div>
                    <div class="bg-blue-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Orders</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ number_format($analytics['overview']['total_orders']) }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ number_format($analytics['overview']['completed_orders']) }} completed</p>
                    </div>
                    <div class="bg-green-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Total Revenue</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">Rp
                            {{ number_format($analytics['overview']['total_revenue'], 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-500 mt-1">Avg: Rp
                            {{ number_format($analytics['overview']['total_revenue'] / max($analytics['overview']['completed_orders'], 1), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-yellow-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Average Rating</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2">
                            {{ number_format($analytics['overview']['average_rating'], 2) }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ number_format($analytics['overview']['total_reviews']) }}
                            reviews</p>
                    </div>
                    <div class="bg-purple-100 rounded-full p-3">
                        <svg class="w-8 h-8 text-purple-600" fill="currentColor" viewBox="0 0 20 20">
                            <path
                                d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z">
                            </path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- View Statistics Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">View Statistics</h3>
                <canvas id="viewStatsChart"></canvas>
            </div>

            <!-- Sales Trend Chart -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Sales Trend</h3>
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Product Popularity -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Top Products</h3>
                <a href="{{ route('factory.analytics.product-popularity', $factory->id) }}"
                    class="text-blue-600 hover:underline">
                    View All
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requests</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity Sold</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($analytics['productPopularity']->take(10) as $product)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $product['product_name'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($product['total_requests']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($product['total_quantity_sold']) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    Rp {{ number_format($product['total_revenue'], 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                {{ $product['stock_status'] === 'available' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($product['stock_status']) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No product data available
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Performance Metrics & Quality Trends -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Performance Metrics -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Performance Metrics</h3>
                <div class="space-y-4">
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Fulfillment Rate</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ number_format($analytics['performanceMetrics']['fulfillment_rate'], 2) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-green-600 h-2 rounded-full"
                                style="width: {{ $analytics['performanceMetrics']['fulfillment_rate'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Customer Satisfaction</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ number_format($analytics['performanceMetrics']['customer_satisfaction'], 2) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full"
                                style="width: {{ $analytics['performanceMetrics']['customer_satisfaction'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Repeat Customer Rate</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ number_format($analytics['performanceMetrics']['repeat_customer_rate'], 2) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-purple-600 h-2 rounded-full"
                                style="width: {{ $analytics['performanceMetrics']['repeat_customer_rate'] }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <span class="text-sm text-gray-600">Conversion Rate</span>
                            <span
                                class="text-sm font-medium text-gray-900">{{ number_format($analytics['overview']['conversion_rate'], 2) }}%</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-yellow-600 h-2 rounded-full"
                                style="width: {{ min($analytics['overview']['conversion_rate'], 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quality Trends -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Quality Trends</h3>
                <canvas id="qualityTrendsChart"></canvas>
                <div class="mt-4 grid grid-cols-3 gap-4 text-center">
                    <div>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $analytics['qualityTrends']['average_scores']['quality'] ? number_format($analytics['qualityTrends']['average_scores']['quality'], 2) : 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-600">Quality</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">
                            {{ $analytics['qualityTrends']['average_scores']['delivery'] ? number_format($analytics['qualityTrends']['average_scores']['delivery'], 2) : 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-600">Delivery</p>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $analytics['qualityTrends']['average_scores']['service'] ? number_format($analytics['qualityTrends']['average_scores']['service'], 2) : 'N/A' }}
                        </p>
                        <p class="text-xs text-gray-600">Service</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('factory.analytics.view-statistics', $factory->id) }}"
                class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="font-semibold text-gray-900">View Statistics</h4>
                <p class="text-sm text-gray-600 mt-1">Detailed view analytics</p>
            </a>
            <a href="{{ route('factory.analytics.sales-reports', $factory->id) }}"
                class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="font-semibold text-gray-900">Sales Reports</h4>
                <p class="text-sm text-gray-600 mt-1">Revenue & orders</p>
            </a>
            <a href="{{ route('factory.analytics.performance-metrics', $factory->id) }}"
                class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="font-semibold text-gray-900">Performance</h4>
                <p class="text-sm text-gray-600 mt-1">Key metrics & KPIs</p>
            </a>
            <a href="{{ route('factory.analytics.location-analytics', $factory->id) }}"
                class="bg-white rounded-lg shadow p-4 hover:shadow-lg transition">
                <h4 class="font-semibold text-gray-900">Location Analytics</h4>
                <p class="text-sm text-gray-600 mt-1">Geographic insights</p>
            </a>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
        <script>
            // View Statistics Chart
            const viewCtx = document.getElementById('viewStatsChart').getContext('2d');
            const viewStatsChart = new Chart(viewCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['viewStats']['daily_views']->pluck('date')) !!},
                    datasets: [{
                        label: 'Total Views',
                        data: {!! json_encode($analytics['viewStats']['daily_views']->pluck('total_views')) !!},
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4
                    }, {
                        label: 'Unique Visitors',
                        data: {!! json_encode($analytics['viewStats']['daily_views']->pluck('unique_visitors')) !!},
                        borderColor: 'rgb(16, 185, 129)',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Sales Trend Chart
            const salesCtx = document.getElementById('salesTrendChart').getContext('2d');
            const salesTrendChart = new Chart(salesCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($analytics['salesData']['daily_sales']->pluck('date')) !!},
                    datasets: [{
                        label: 'Revenue (Rp)',
                        data: {!! json_encode($analytics['salesData']['daily_sales']->pluck('total_revenue')) !!},
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: 'rgb(16, 185, 129)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            // Quality Trends Chart
            const qualityCtx = document.getElementById('qualityTrendsChart').getContext('2d');
            const qualityTrendsChart = new Chart(qualityCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($analytics['qualityTrends']['daily_trends']->pluck('date')) !!},
                    datasets: [{
                        label: 'Overall Rating',
                        data: {!! json_encode($analytics['qualityTrends']['daily_trends']->pluck('average_rating')) !!},
                        borderColor: 'rgb(139, 92, 246)',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 5
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    }
                }
            });

            function exportReport(type) {
                window.location.href =
                    `{{ route('factory.analytics.export', $factory->id) }}?report_type=${type}&format=csv&start_date={{ $startDate }}&end_date={{ $endDate }}`;
            }
        </script>
    @endpush
@endsection
