<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Order Details
            </h2>
            <a href="{{ route('store.orders.index') }}">
                <x-button variant="secondary" size="sm">Back to Orders</x-button>
            </a>
        </div>
    </x-slot>

    <x-card class="mb-6">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium">Order Information</h3>
                <x-badge :variant="match($materialRequest->delivery_status) {
                    'delivered' => 'success',
                    'in_transit' => 'info',
                    'ready' => 'warning',
                    'preparing' => 'warning',
                    default => 'default'
                }">
                    {{ $materialRequest->delivery_status_label }}
                </x-badge>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Customer</p>
                <p class="font-semibold">{{ $materialRequest->user->name }}</p>
            </div>
            @if($materialRequest->projectLocation)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Project Location</p>
                    <p class="font-semibold">{{ $materialRequest->projectLocation->name }}</p>
                    <p class="text-sm text-gray-500">{{ $materialRequest->projectLocation->full_address }}</p>
                </div>
            @endif
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Order Amount</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($materialRequest->quoted_price, 0, ',', '.') }}
                </p>
            </div>
            @if($materialRequest->tracking_number)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tracking Number</p>
                    <p class="font-mono font-semibold">{{ $materialRequest->tracking_number }}</p>
                </div>
            @endif
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Accepted Date</p>
                <p class="font-semibold">{{ $materialRequest->accepted_at?->format('d M Y H:i') ?? '-' }}</p>
            </div>
            @if($materialRequest->delivered_at)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivered Date</p>
                    <p class="font-semibold">{{ $materialRequest->delivered_at->format('d M Y H:i') }}</p>
                </div>
            @endif
        </div>
    </x-card>

    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Requested Items</h3>
        </x-slot>
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left p-3">Item</th>
                        <th class="text-left p-3">Quantity</th>
                        <th class="text-left p-3">Unit</th>
                        <th class="text-left p-3">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materialRequest->items ?? [] as $item)
                        <tr class="border-b">
                            <td class="p-3">{{ $item['name'] ?? '-' }}</td>
                            <td class="p-3">{{ $item['quantity'] ?? '-' }}</td>
                            <td class="p-3">{{ $item['unit'] ?? '-' }}</td>
                            <td class="p-3">{{ $item['description'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    @if($materialRequest->delivery_notes)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Delivery Notes</h3>
            </x-slot>
            <p class="text-gray-700 dark:text-gray-300">{{ $materialRequest->delivery_notes }}</p>
        </x-card>
    @endif

    @if($materialRequest->delivery_status !== 'delivered' && $materialRequest->delivery_status !== 'cancelled')
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Update Delivery Status</h3>
            </x-slot>
            <form action="{{ route('stores.material-requests.update-delivery-status', $materialRequest) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <x-form-group label="Delivery Status" name="delivery_status" required>
                        <x-select-input name="delivery_status" required>
                            <option value="preparing" {{ $materialRequest->delivery_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                            <option value="ready" {{ $materialRequest->delivery_status === 'ready' ? 'selected' : '' }}>Ready</option>
                            <option value="in_transit" {{ $materialRequest->delivery_status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                            <option value="delivered" {{ $materialRequest->delivery_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                        </x-select-input>
                    </x-form-group>
                    <x-form-group label="Tracking Number (Optional)" name="tracking_number">
                        <x-text-input name="tracking_number" value="{{ $materialRequest->tracking_number }}" placeholder="Enter tracking number" />
                    </x-form-group>
                    <x-form-group label="Delivery Notes (Optional)" name="delivery_notes">
                        <x-textarea-input name="delivery_notes" rows="3" placeholder="Any delivery notes...">{{ $materialRequest->delivery_notes }}</x-textarea-input>
                    </x-form-group>
                    <x-button variant="primary" type="submit">Update Status</x-button>
                </div>
            </form>
        </x-card>
    @endif
</x-app-with-sidebar>

