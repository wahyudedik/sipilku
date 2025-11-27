<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Factory Request Details
        </h2>
    </x-slot>

    <x-card class="mb-6">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium">Request Information</h3>
                <x-badge :variant="match($factoryRequest->status) {
                    'quoted' => 'success',
                    'accepted' => 'success',
                    'pending' => 'warning',
                    'rejected' => 'danger',
                    default => 'default'
                }">{{ ucfirst($factoryRequest->status) }}</x-badge>
            </div>
        </x-slot>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Factory</p>
                <p class="font-semibold">{{ $factoryRequest->factory->name }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $factoryRequest->factory->factoryType->name ?? 'Factory' }}
                </p>
            </div>
            @if($factoryRequest->projectLocation)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Project Location</p>
                    <p class="font-semibold">{{ $factoryRequest->projectLocation->name }}</p>
                </div>
            @endif
            @if($factoryRequest->total_cost)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Cost</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($factoryRequest->total_cost, 0, ',', '.') }}
                    </p>
                    @php
                        $breakdown = $factoryRequest->cost_breakdown;
                    @endphp
                    <div class="mt-2 space-y-1 text-sm text-gray-600 dark:text-gray-400">
                        <div>Product Price: Rp {{ number_format($breakdown['product_price'], 0, ',', '.') }}</div>
                        @if($breakdown['delivery_cost'] > 0)
                            <div>Delivery Cost: Rp {{ number_format($breakdown['delivery_cost'], 0, ',', '.') }}</div>
                        @endif
                        @if($breakdown['additional_fees'] > 0)
                            <div>Additional Fees: Rp {{ number_format($breakdown['additional_fees'], 0, ',', '.') }}</div>
                            @if($factoryRequest->additional_fees && is_array($factoryRequest->additional_fees))
                                <div class="ml-4 text-xs">
                                    @foreach($factoryRequest->additional_fees as $fee)
                                        <div>- {{ $fee['name'] ?? 'Fee' }}: Rp {{ number_format($fee['amount'] ?? 0, 0, ',', '.') }}</div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            @endif
            @if($factoryRequest->deadline)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Deadline</p>
                    <p class="font-semibold">{{ $factoryRequest->deadline->format('d M Y') }}</p>
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
                        <th class="text-left p-2">Specifications</th>
                        <th class="text-left p-2">Description</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($factoryRequest->items ?? [] as $item)
                        <tr class="border-b">
                            <td class="p-2">{{ $item['name'] ?? '-' }}</td>
                            <td class="p-2">{{ $item['quantity'] ?? '-' }}</td>
                            <td class="p-2">{{ $item['unit'] ?? '-' }}</td>
                            <td class="p-2">
                                @if(isset($item['specifications']))
                                    <pre class="text-xs">{{ json_encode($item['specifications'], JSON_PRETTY_PRINT) }}</pre>
                                @else
                                    -
                                @endif
                            </td>
                            <td class="p-2">{{ $item['description'] ?? '-' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-card>

    @if($factoryRequest->message)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Message</h3>
            </x-slot>
            <p class="text-gray-700 dark:text-gray-300">{{ $factoryRequest->message }}</p>
        </x-card>
    @endif

    @if($factoryRequest->status === 'quoted')
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Quote Details</h3>
            </x-slot>
            @if($factoryRequest->quote_message)
                <p class="text-gray-700 dark:text-gray-300 mb-4">{{ $factoryRequest->quote_message }}</p>
            @endif
            <div class="flex space-x-4">
                <form action="{{ route('contractor.factory-requests.accept', $factoryRequest) }}" method="POST">
                    @csrf
                    <x-button variant="success" type="submit">Accept Quote</x-button>
                </form>
                <button type="button" onclick="document.getElementById('rejectForm').classList.toggle('hidden')" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                    Reject Quote
                </button>
                @if($factoryRequest->request_group_id)
                    <a href="{{ route('contractor.factory-requests.compare', ['request_group_id' => $factoryRequest->request_group_id]) }}">
                        <x-button variant="primary" type="button">Compare with Others</x-button>
                    </a>
                @endif
            </div>
            <form id="rejectForm" action="{{ route('contractor.factory-requests.reject', $factoryRequest) }}" method="POST" class="hidden mt-4">
                @csrf
                <x-form-group label="Rejection Reason" name="rejection_reason" required>
                    <x-textarea-input name="rejection_reason" rows="3" required></x-textarea-input>
                </x-form-group>
                <x-button variant="danger" type="submit">Submit Rejection</x-button>
            </form>
        </x-card>
    @endif

    @if($factoryRequest->status === 'accepted')
        <!-- Order Tracking -->
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Order Tracking</h3>
            </x-slot>
            @if($factoryRequest->tracking_number)
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tracking Number</p>
                    <p class="font-mono text-lg font-semibold">{{ $factoryRequest->tracking_number }}</p>
                </div>
            @endif
            <div class="space-y-4">
                <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $factoryRequest->status === 'accepted' ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="font-medium">Order Accepted</p>
                        <p class="text-sm text-gray-500">{{ $factoryRequest->accepted_at ? $factoryRequest->accepted_at->format('d M Y H:i') : '-' }}</p>
                    </div>
                </div>
                @if($factoryRequest->delivery_status)
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $factoryRequest->isPreparing() ? 'bg-blue-500' : ($factoryRequest->preparing_at ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center">
                            @if($factoryRequest->preparing_at)
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <div class="w-3 h-3 rounded-full bg-white"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium">Preparing</p>
                            <p class="text-sm text-gray-500">{{ $factoryRequest->preparing_at ? $factoryRequest->preparing_at->format('d M Y H:i') : 'Pending' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $factoryRequest->isReady() ? 'bg-blue-500' : ($factoryRequest->ready_at ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center">
                            @if($factoryRequest->ready_at)
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <div class="w-3 h-3 rounded-full bg-white"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium">Ready for Delivery</p>
                            <p class="text-sm text-gray-500">{{ $factoryRequest->ready_at ? $factoryRequest->ready_at->format('d M Y H:i') : 'Pending' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $factoryRequest->isInTransit() ? 'bg-blue-500' : ($factoryRequest->in_transit_at ? 'bg-green-500' : 'bg-gray-300') }} flex items-center justify-center">
                            @if($factoryRequest->in_transit_at)
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <div class="w-3 h-3 rounded-full bg-white"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium">In Transit</p>
                            <p class="text-sm text-gray-500">{{ $factoryRequest->in_transit_at ? $factoryRequest->in_transit_at->format('d M Y H:i') : 'Pending' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $factoryRequest->isDelivered() ? 'bg-green-500' : 'bg-gray-300' }} flex items-center justify-center">
                            @if($factoryRequest->delivered_at)
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <div class="w-3 h-3 rounded-full bg-white"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-medium">Delivered</p>
                            <p class="text-sm text-gray-500">{{ $factoryRequest->delivered_at ? $factoryRequest->delivered_at->format('d M Y H:i') : 'Pending' }}</p>
                        </div>
                    </div>
                @endif
                @if($factoryRequest->delivery_notes)
                    <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-800 rounded">
                        <p class="text-sm font-medium mb-1">Delivery Notes</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $factoryRequest->delivery_notes }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Chat Link -->
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Communication</h3>
            </x-slot>
            <a href="{{ route('messages.chat.factory-request', $factoryRequest) }}" class="inline-flex items-center space-x-2 text-primary-600 hover:text-primary-800 dark:text-primary-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <span>Chat with Factory</span>
            </a>
        </x-card>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('contractor.factory-requests.index') }}">
            <x-button variant="secondary">Back to List</x-button>
        </a>
    </div>
</x-app-with-sidebar>

