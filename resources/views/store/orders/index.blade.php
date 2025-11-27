<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Order Management
            </h2>
            <a href="{{ route('store.dashboard') }}">
                <x-button variant="secondary" size="sm">Back to Dashboard</x-button>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('store.orders.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-form-group label="Search" name="search">
                    <x-text-input name="search" value="{{ request('search') }}" placeholder="Customer name or tracking number" />
                </x-form-group>
            </div>
            <div class="w-48">
                <x-form-group label="Status" name="status">
                    <x-select-input name="status">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                        <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                        <option value="in_transit" {{ request('status') === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    </x-select-input>
                </x-form-group>
            </div>
            <div class="flex items-end">
                <x-button variant="primary" type="submit">Filter</x-button>
            </div>
        </form>
    </x-card>

    <!-- Orders Table -->
    <x-card>
        @if($orders->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Order ID</th>
                            <th class="text-left p-3">Customer</th>
                            <th class="text-left p-3">Amount</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Tracking</th>
                            <th class="text-left p-3">Date</th>
                            <th class="text-left p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="p-3">
                                    <span class="font-mono text-sm">{{ substr($order->uuid, 0, 8) }}...</span>
                                </td>
                                <td class="p-3">
                                    <p class="font-semibold">{{ $order->user->name }}</p>
                                    @if($order->projectLocation)
                                        <p class="text-xs text-gray-500">{{ $order->projectLocation->name }}</p>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <p class="font-semibold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($order->quoted_price, 0, ',', '.') }}
                                    </p>
                                </td>
                                <td class="p-3">
                                    <x-badge :variant="match($order->delivery_status) {
                                        'delivered' => 'success',
                                        'in_transit' => 'info',
                                        'ready' => 'warning',
                                        'preparing' => 'warning',
                                        default => 'default'
                                    }">
                                        {{ $order->delivery_status_label }}
                                    </x-badge>
                                </td>
                                <td class="p-3">
                                    @if($order->tracking_number)
                                        <span class="font-mono text-sm">{{ $order->tracking_number }}</span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $order->accepted_at?->format('d M Y') ?? $order->created_at->format('d M Y') }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <a href="{{ route('store.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 text-sm">
                                        View Details →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $orders->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">No orders found</p>
            </div>
        @endif
    </x-card>
</x-app-with-sidebar>

