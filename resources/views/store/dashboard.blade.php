<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Store Dashboard
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('stores.show', $store) }}">
                    <x-button variant="secondary" size="sm">View Store</x-button>
                </a>
                <a href="{{ route('store.analytics.index') }}">
                    <x-button variant="secondary" size="sm">Analytics</x-button>
                </a>
                <a href="{{ route('stores.edit', $store) }}">
                    <x-button variant="primary" size="sm">Edit Profile</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
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
                        {{ $salesStats['sales_growth'] >= 0 ? '+' : '' }}{{ number_format($salesStats['sales_growth'], 1) }}% from last month
                    </p>
                </div>
                <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                    <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
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
                    <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
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
                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
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
                    <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                    <span class="font-semibold">{{ $orderStats['pending'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Preparing</span>
                    <span class="font-semibold">{{ $orderStats['preparing'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Ready</span>
                    <span class="font-semibold">{{ $orderStats['ready'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">In Transit</span>
                    <span class="font-semibold">{{ $orderStats['in_transit'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Delivered</span>
                    <span class="font-semibold">{{ $orderStats['delivered'] }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="font-semibold">Total</span>
                        <span class="font-bold text-primary-600 dark:text-primary-400">{{ $orderStats['total'] }}</span>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <a href="{{ route('store.orders.index') }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                    View All Orders →
                </a>
            </div>
        </x-card>

        <!-- Earnings -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Earnings & Commission</h3>
            </x-slot>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Total Earnings</span>
                    <span class="font-semibold">Rp {{ number_format($earnings['total_earnings'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Platform Fee (10%)</span>
                    <span class="font-semibold text-red-600">- Rp {{ number_format($earnings['total_commission'], 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Net Earnings</span>
                    <span class="font-semibold text-green-600">Rp {{ number_format($earnings['net_earnings'], 0, ',', '.') }}</span>
                </div>
                <div class="pt-3 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <span class="text-sm font-semibold">Available Balance</span>
                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($earnings['available_balance'], 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="mt-4 flex space-x-2">
                <a href="{{ route('store.withdrawals.create') }}" class="flex-1">
                    <x-button variant="primary" size="sm" class="w-full">Request Payout</x-button>
                </a>
                <a href="{{ route('store.withdrawals.index') }}" class="flex-1">
                    <x-button variant="secondary" size="sm" class="w-full">Withdrawal History</x-button>
                </a>
            </div>
        </x-card>

        <!-- Inventory Alerts -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Inventory Alerts</h3>
            </x-slot>
            @if($inventoryAlerts['low_stock_count'] > 0 || $inventoryAlerts['out_of_stock_count'] > 0)
                <div class="space-y-3">
                    @if($inventoryAlerts['out_of_stock_count'] > 0)
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-red-800 dark:text-red-200">Out of Stock</span>
                                <span class="text-red-600 dark:text-red-400 font-bold">{{ $inventoryAlerts['out_of_stock_count'] }}</span>
                            </div>
                            <p class="text-xs text-red-700 dark:text-red-300">Products need restocking</p>
                        </div>
                    @endif
                    @if($inventoryAlerts['low_stock_count'] > 0)
                        <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <span class="font-semibold text-yellow-800 dark:text-yellow-200">Low Stock</span>
                                <span class="text-yellow-600 dark:text-yellow-400 font-bold">{{ $inventoryAlerts['low_stock_count'] }}</span>
                            </div>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300">Products running low</p>
                        </div>
                    @endif
                </div>
                <div class="mt-4">
                    <a href="{{ route('stores.products.index', $store) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                        Manage Inventory →
                    </a>
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-4">No inventory alerts</p>
            @endif
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Recent Orders</h3>
                    <a href="{{ route('store.orders.index') }}" class="text-sm text-primary-600 hover:text-primary-800">
                        View All →
                    </a>
                </div>
            </x-slot>
            @if(count($orders) > 0)
                <div class="space-y-3">
                    @foreach(array_slice($orders, 0, 5) as $order)
                        <div class="flex justify-between items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div>
                                <p class="font-semibold">{{ $order['customer'] }}</p>
                                <p class="text-sm text-gray-500">Rp {{ number_format($order['amount'], 0, ',', '.') }}</p>
                            </div>
                            <x-badge :variant="match($order['status']) {
                                'delivered' => 'success',
                                'in_transit' => 'info',
                                'ready' => 'warning',
                                default => 'default'
                            }">
                                {{ ucfirst(str_replace('_', ' ', $order['status'])) }}
                            </x-badge>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No orders yet</p>
            @endif
        </x-card>

        <!-- Recent Material Requests -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Recent Material Requests</h3>
                    <a href="{{ route('stores.material-requests.index') }}" class="text-sm text-primary-600 hover:text-primary-800">
                        View All →
                    </a>
                </div>
            </x-slot>
            @if($recentMaterialRequests->count() > 0)
                <div class="space-y-3">
                    @foreach($recentMaterialRequests as $request)
                        <div class="flex justify-between items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div>
                                <p class="font-semibold">{{ $request->user->name }}</p>
                                <p class="text-sm text-gray-500">
                                    {{ $request->status === 'quoted' ? 'Rp ' . number_format($request->quoted_price, 0, ',', '.') : ucfirst($request->status) }}
                                </p>
                            </div>
                            <x-badge :variant="match($request->status) {
                                'quoted' => 'success',
                                'accepted' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'default'
                            }">
                                {{ ucfirst($request->status) }}
                            </x-badge>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No material requests yet</p>
            @endif
        </x-card>

        <!-- Recent Reviews -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Recent Reviews</h3>
                    <a href="{{ route('store.reviews.index') }}" class="text-sm text-primary-600 hover:text-primary-800">
                        View All →
                    </a>
                </div>
            </x-slot>
            @if($recentReviews->count() > 0)
                <div class="space-y-3">
                    @foreach($recentReviews as $review)
                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <p class="font-semibold">{{ $review->user->name }}</p>
                                    <div class="flex items-center mt-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                            </div>
                            @if($review->comment)
                                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-2">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 dark:text-gray-400 text-center py-8">No reviews yet</p>
            @endif
        </x-card>

        <!-- Quick Actions -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Quick Actions</h3>
            </x-slot>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('stores.products.index', $store) }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition text-center">
                    <svg class="w-8 h-8 text-primary-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <p class="font-semibold text-sm">Products</p>
                </a>
                <a href="{{ route('stores.material-requests.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition text-center">
                    <svg class="w-8 h-8 text-primary-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="font-semibold text-sm">Requests</p>
                </a>
                <a href="{{ route('store.orders.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition text-center">
                    <svg class="w-8 h-8 text-primary-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <p class="font-semibold text-sm">Orders</p>
                </a>
                <a href="{{ route('store.reviews.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition text-center">
                    <svg class="w-8 h-8 text-primary-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                    <p class="font-semibold text-sm">Reviews</p>
                </a>
            </div>
        </x-card>
    </div>
</x-app-with-sidebar>

