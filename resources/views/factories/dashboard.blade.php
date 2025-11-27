@php
    use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Factory Dashboard - {{ $factory->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('factories.show', $factory) }}">
                    <x-button variant="secondary" size="sm">View Factory</x-button>
                </a>
                <a href="{{ route('factories.edit', $factory) }}">
                    <x-button variant="primary" size="sm">Edit Profile</x-button>
                </a>
                <a href="{{ route('factories.analytics.index', $factory) }}">
                    <x-button variant="default" size="sm">Analytics</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    @if (session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <!-- Sales Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Sales</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($salesStats['total_sales'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $salesStats['sales_growth'] >= 0 ? '+' : '' }}{{ number_format($salesStats['sales_growth'], 1) }}%
                        from last month
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Monthly Sales</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($salesStats['monthly_sales'], 0, ',', '.') }}
                    </p>
                    <p class="text-xs text-gray-500 mt-1">This month</p>
                </div>
                <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $salesStats['total_orders'] }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $salesStats['monthly_orders'] }} this month</p>
                </div>
                <div class="p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </x-card>

        <x-card>
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Avg Order Value</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                        Rp {{ number_format($salesStats['average_order_value'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Order Statistics -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Order Status</h3>
            </x-slot>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pending Quotes</span>
                    <span
                        class="font-semibold text-gray-900 dark:text-gray-100">{{ $orderStats['pending_quotes'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Quoted</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $orderStats['quoted'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Preparing</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $orderStats['preparing'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ready</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $orderStats['ready'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">In Transit</span>
                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $orderStats['in_transit'] }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Delivered</span>
                    <span class="font-bold text-green-600 dark:text-green-400">{{ $orderStats['delivered'] }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-gray-200 dark:border-gray-700">
                    <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Total</span>
                    <span class="font-bold text-primary-600 dark:text-primary-400">{{ $orderStats['total'] }}</span>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('factories.quotes.index', $factory) }}"
                    class="text-sm text-primary-600 hover:underline">
                    View All Orders →
                </a>
            </div>
        </x-card>

        <!-- Earnings & Commission -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Earnings & Commission</h3>
            </x-slot>
            <div class="space-y-3">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Available Balance</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($earnings['available_balance'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Total Earnings</span>
                        <span class="font-semibold">Rp
                            {{ number_format($earnings['total_earnings'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Platform Commission</span>
                        <span class="font-semibold">Rp
                            {{ number_format($earnings['total_commission'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Net Earnings</span>
                        <span class="font-semibold text-green-600 dark:text-green-400">Rp
                            {{ number_format($earnings['net_earnings'], 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm pt-2 border-t border-gray-200 dark:border-gray-700">
                        <span class="text-gray-600 dark:text-gray-400">Monthly Net</span>
                        <span class="font-semibold">Rp
                            {{ number_format($earnings['monthly_net_earnings'], 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="pt-3">
                    <a href="{{ route('factories.withdrawals.index', $factory) }}"
                        class="block w-full text-center px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                        Request Withdrawal
                    </a>
                </div>
            </div>
        </x-card>

        <!-- Capacity & Availability -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Capacity & Availability</h3>
            </x-slot>
            <div class="space-y-3">
                @if ($capacityStatus['capacity'])
                    <div>
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600 dark:text-gray-400">Capacity Usage</span>
                            <span class="font-semibold">{{ $capacityStatus['capacity_used'] }} /
                                {{ $capacityStatus['capacity'] }}</span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                            <div class="bg-{{ $capacityStatus['is_available'] ? 'green' : 'yellow' }}-600 h-3 rounded-full transition-all"
                                style="width: {{ min(100, $capacityStatus['capacity_percentage']) }}%"></div>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ number_format($capacityStatus['capacity_percentage'], 1) }}% used</p>
                    </div>
                @else
                    <p class="text-sm text-gray-600 dark:text-gray-400">Capacity not set</p>
                @endif
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                        <x-badge :variant="$capacityStatus['is_available'] ? 'success' : 'warning'">
                            {{ $capacityStatus['status'] }}
                        </x-badge>
                    </div>
                </div>
                <div class="pt-3">
                    <a href="{{ route('factories.edit', $factory) }}"
                        class="text-sm text-primary-600 hover:underline">
                        Update Capacity →
                    </a>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Orders -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Recent Orders</h3>
                    <a href="{{ route('factories.quotes.index', $factory) }}"
                        class="text-sm text-primary-600 hover:underline">
                        View All
                    </a>
                </div>
            </x-slot>
            <div class="space-y-3">
                @forelse($orders as $order)
                    <div
                        class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $order['customer'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $order['project_location'] ?? 'No location' }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $order['created_at']->format('d M Y') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-sm">Rp {{ number_format($order['amount'], 0, ',', '.') }}</p>
                            <x-badge :variant="match ($order['status']) {
                                'delivered' => 'success',
                                'in_transit' => 'info',
                                'ready' => 'info',
                                'preparing' => 'warning',
                                default => 'default',
                            }" size="xs">
                                {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                            </x-badge>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">No orders yet</p>
                @endforelse
            </div>
        </x-card>

        <!-- Recent Quote Requests -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Recent Quote Requests</h3>
                    <a href="{{ route('factories.quotes.index', $factory) }}"
                        class="text-sm text-primary-600 hover:underline">
                        View All
                    </a>
                </div>
            </x-slot>
            <div class="space-y-3">
                @forelse($recentQuoteRequests as $request)
                    <div
                        class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium text-sm">{{ $request->user->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ count($request->items ?? []) }} items
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $request->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <x-badge :variant="match ($request->status) {
                                'quoted' => 'success',
                                'accepted' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'default',
                            }" size="xs">
                                {{ ucfirst($request->status) }}
                            </x-badge>
                            <a href="{{ route('factories.quotes.show', [$factory, $request]) }}"
                                class="block text-xs text-primary-600 hover:underline mt-1">
                                View →
                            </a>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-gray-400 py-4">No quote requests yet</p>
                @endforelse
            </div>
        </x-card>
    </div>

    <!-- Factory Type Specific Section -->
    @if (isset($typeSpecificData) && !empty($typeSpecificData))
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">{{ $typeSpecificData['type_name'] ?? 'Factory' }} Specific Information
                </h3>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @if (isset($typeSpecificData['active_productions']))
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Active Productions</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $typeSpecificData['active_productions'] }}</p>
                    </div>
                @endif
                @if (isset($typeSpecificData['ready_mix_orders']))
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Ready Mix Orders</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                            {{ $typeSpecificData['ready_mix_orders'] }}</p>
                    </div>
                @endif
                @if (isset($typeSpecificData['stock_levels']))
                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Total Stock</p>
                        <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">
                            {{ number_format($typeSpecificData['stock_levels']) }}</p>
                    </div>
                @endif
                @if (isset($typeSpecificData['production_queue']))
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Production Queue</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $typeSpecificData['production_queue'] }}</p>
                    </div>
                @endif
            </div>
        </x-card>
    @endif

    <!-- Delivery Schedule Calendar -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Delivery Schedule (Next 7 Days)</h3>
        </x-slot>
        @if (count($upcomingDeliveries) > 0)
            <div class="space-y-3">
                @foreach ($upcomingDeliveries as $delivery)
                    <div
                        class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex-1">
                            <p class="font-medium">{{ $delivery['customer'] }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ $delivery['project_location'] ?? 'No location' }}
                            </p>
                            @if ($delivery['ready_at'])
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Ready: {{ $delivery['ready_at']->format('d M Y H:i') }}
                                </p>
                            @endif
                        </div>
                        <div class="text-right">
                            <x-badge :variant="match ($delivery['status']) {
                                'in_transit' => 'info',
                                'ready' => 'success',
                                default => 'default',
                            }" size="xs">
                                {{ ucfirst(str_replace('_', ' ', $delivery['status'])) }}
                            </x-badge>
                            @if ($delivery['tracking_number'])
                                <p class="text-xs font-mono text-gray-500 mt-1">{{ $delivery['tracking_number'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 dark:text-gray-400 py-4">No upcoming deliveries</p>
        @endif
    </x-card>

    <!-- Quick Actions -->
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium">Quick Actions</h3>
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('factories.products.index', $factory) }}"
                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <div>
                        <p class="font-medium">Product Catalog</p>
                        <p class="text-xs text-gray-500">Manage products</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('factories.edit', $factory) }}"
                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    <div>
                        <p class="font-medium">Edit Profile</p>
                        <p class="text-xs text-gray-500">Update information</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('factories.reviews.index', $factory) }}"
                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <div>
                        <p class="font-medium">Reviews</p>
                        <p class="text-xs text-gray-500">Manage reviews</p>
                    </div>
                </div>
            </a>
            <a href="{{ route('factories.withdrawals.index', $factory) }}"
                class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <div class="flex items-center space-x-3">
                    <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="font-medium">Withdrawals</p>
                        <p class="text-xs text-gray-500">View history</p>
                    </div>
                </div>
            </a>
        </div>
    </x-card>
</x-app-layout>
