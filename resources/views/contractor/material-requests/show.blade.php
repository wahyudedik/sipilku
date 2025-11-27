<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Material Request Details
        </h2>
    </x-slot>

    <x-card class="mb-6">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium">Request Information</h3>
                <x-badge :variant="match($materialRequest->status) {
                    'quoted' => 'success',
                    'accepted' => 'success',
                    'pending' => 'warning',
                    'rejected' => 'danger',
                    default => 'default'
                }">{{ ucfirst($materialRequest->status) }}</x-badge>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Store</p>
                <p class="font-semibold">{{ $materialRequest->store->name }}</p>
            </div>
            @if($materialRequest->projectLocation)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Project Location</p>
                    <p class="font-semibold">{{ $materialRequest->projectLocation->name }}</p>
                </div>
            @endif
            @if($materialRequest->quoted_price)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Quoted Price</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($materialRequest->quoted_price, 0, ',', '.') }}
                    </p>
                </div>
            @endif
            @if($materialRequest->deadline)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Deadline</p>
                    <p class="font-semibold">{{ $materialRequest->deadline->format('d M Y') }}</p>
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
                        <th class="text-left p-2">Item</th>
                        <th class="text-left p-2">Quantity</th>
                        <th class="text-left p-2">Unit</th>
                        <th class="text-left p-2">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materialRequest->items ?? [] as $item)
                        <tr class="border-b">
                            <td class="p-2">{{ $item['name'] ?? '-' }}</td>
                            <td class="p-2">{{ $item['quantity'] ?? '-' }}</td>
                            <td class="p-2">{{ $item['unit'] ?? '-' }}</td>
                            <td class="p-2">{{ $item['description'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    @if($materialRequest->message)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Message</h3>
            </x-slot>
            <p class="text-gray-700 dark:text-gray-300">{{ $materialRequest->message }}</p>
        </x-card>
    @endif

    @if($materialRequest->status === 'quoted')
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Quote Details</h3>
            </x-slot>
            @if($materialRequest->quote_message)
                <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $materialRequest->quote_message }}</p>
            @endif
            <div class="flex space-x-4">
                <form action="{{ route('contractor.material-requests.accept', $materialRequest) }}" method="POST">
                    @csrf
                    <x-button variant="success" type="submit">Accept Quote</x-button>
                </form>
                <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Reject Quote
                </button>
            </div>
            <form id="rejectForm" action="{{ route('contractor.material-requests.reject', $materialRequest) }}" method="POST" class="hidden mt-4">
                @csrf
                <x-form-group label="Rejection Reason" name="rejection_reason" required>
                    <x-textarea-input name="rejection_reason" rows="3" required></x-textarea-input>
                </x-form-group>
                <x-button variant="danger" type="submit">Submit Rejection</x-button>
            </form>
        </x-card>
    @endif

    @if($materialRequest->status === 'accepted' && $materialRequest->delivery_status)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Order Tracking</h3>
            </x-slot>
            <div class="space-y-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Status</p>
                    <x-badge :variant="match($materialRequest->delivery_status) {
                        'delivered' => 'success',
                        'in_transit' => 'info',
                        'ready' => 'warning',
                        'preparing' => 'warning',
                        'cancelled' => 'danger',
                        default => 'default'
                    }">
                        {{ $materialRequest->delivery_status_label }}
                    </x-badge>
                </div>
                @if($materialRequest->tracking_number)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tracking Number</p>
                        <p class="font-semibold">{{ $materialRequest->tracking_number }}</p>
                    </div>
                @endif
                @if($materialRequest->delivery_notes)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Delivery Notes</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $materialRequest->delivery_notes }}</p>
                    </div>
                @endif
                <div class="space-y-2">
                    @if($materialRequest->preparing_at)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Preparing: {{ $materialRequest->preparing_at->format('d M Y H:i') }}
                        </p>
                    @endif
                    @if($materialRequest->ready_at)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Ready: {{ $materialRequest->ready_at->format('d M Y H:i') }}
                        </p>
                    @endif
                    @if($materialRequest->in_transit_at)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            In Transit: {{ $materialRequest->in_transit_at->format('d M Y H:i') }}
                        </p>
                    @endif
                    @if($materialRequest->delivered_at)
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Delivered: {{ $materialRequest->delivered_at->format('d M Y H:i') }}
                        </p>
                    @endif
                </div>
            </div>
        </x-card>
    @endif

    <!-- Chat Integration -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Communication</h3>
        </x-slot>
        <a href="{{ route('messages.chat.material-request', [$materialRequest->store->user, $materialRequest]) }}" 
           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            Chat with Store
        </a>
    </x-card>

    <div class="flex justify-between">
        @if($materialRequest->request_group_id)
            <a href="{{ route('contractor.material-requests.compare', ['request_group_id' => $materialRequest->request_group_id]) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Compare Quotes
            </a>
        @endif
        <a href="{{ route('contractor.material-requests.index') }}">
            <x-button variant="secondary">Back to List</x-button>
        </a>
    </div>
</x-app-with-sidebar>

