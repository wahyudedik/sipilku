@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Quote Request Details
            </h2>
            <a href="{{ route('factories.quotes.index', $factory) }}">
                <x-button variant="secondary" size="sm">Back to List</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Request Information -->
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
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Contractor</p>
                        <p class="font-semibold">{{ $factoryRequest->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $factoryRequest->user->email }}</p>
                    </div>
                    @if($factoryRequest->projectLocation)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Project Location</p>
                            <p class="font-semibold">{{ $factoryRequest->projectLocation->name }}</p>
                            @if($factoryRequest->projectLocation->hasCoordinates())
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $factoryRequest->projectLocation->full_address }}
                                </p>
                                @if($distance)
                                    <p class="text-xs text-primary-600 dark:text-primary-400 mt-1">
                                        Distance: {{ number_format($distance, 2) }} km
                                    </p>
                                @endif
                            @endif
                        </div>
                    @endif
                    @if($factoryRequest->deadline)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Deadline</p>
                            <p class="font-semibold">{{ $factoryRequest->deadline->format('d M Y') }}</p>
                        </div>
                    @endif
                    @if($factoryRequest->budget)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Budget</p>
                            <p class="font-semibold">Rp {{ number_format($factoryRequest->budget, 0, ',', '.') }}</p>
                        </div>
                    @endif
                </div>
            </x-card>

            <!-- Requested Items -->
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Requested Items</h3>
                </x-slot>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Unit</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Specifications</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($factoryRequest->items ?? [] as $item)
                                <tr>
                                    <td class="px-4 py-3">
                                        <p class="font-medium">{{ $item['name'] ?? '-' }}</p>
                                        @if(isset($item['description']))
                                            <p class="text-xs text-gray-500">{{ $item['description'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $item['quantity'] ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $item['unit'] ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        @if(isset($item['specifications']) && is_array($item['specifications']))
                                            <div class="text-xs space-y-1">
                                                @foreach($item['specifications'] as $key => $value)
                                                    <div><strong>{{ $key }}:</strong> {{ $value }}</div>
                                                @endforeach
                                            </div>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>

            @if($factoryRequest->message)
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Message from Contractor</h3>
                    </x-slot>
                    <p class="text-gray-700 dark:text-gray-300">{{ $factoryRequest->message }}</p>
                </x-card>
            @endif

            @if($factoryRequest->status === 'pending')
                <!-- Quote Form -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Submit Quote</h3>
                    </x-slot>
                    <form action="{{ route('factories.quotes.quote', [$factory, $factoryRequest]) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <x-form-group label="Product Price *" name="quoted_price" required>
                                <x-text-input type="number" name="quoted_price" step="0.01" min="0" required />
                                <x-input-error :messages="$errors->get('quoted_price')" class="mt-2" />
                            </x-form-group>

                            <x-form-group label="Delivery Cost" name="delivery_cost">
                                <x-text-input type="number" name="delivery_cost" step="0.01" min="0" value="{{ $deliveryCost ?? '' }}" />
                                <x-input-error :messages="$errors->get('delivery_cost')" class="mt-2" />
                                @if($deliveryCost)
                                    <p class="text-xs text-gray-500 mt-1">Calculated: Rp {{ number_format($deliveryCost, 0, ',', '.') }}</p>
                                @endif
                            </x-form-group>

                            <div id="additional-fees-container">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Additional Fees (Optional)
                                </label>
                                <div class="space-y-2" id="fees-list">
                                    <!-- Fees will be added here dynamically -->
                                </div>
                                <button type="button" onclick="addFee()" class="mt-2 text-sm text-primary-600 hover:text-primary-800">
                                    + Add Fee
                                </button>
                            </div>

                            <x-form-group label="Quote Message" name="quote_message">
                                <x-textarea-input name="quote_message" rows="4" placeholder="Optional message to contractor..."></x-textarea-input>
                                <x-input-error :messages="$errors->get('quote_message')" class="mt-2" />
                            </x-form-group>

                            <x-button variant="primary" type="submit">Submit Quote</x-button>
                        </div>
                    </form>
                </x-card>
            @elseif($factoryRequest->status === 'quoted')
                <!-- Quote Details -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Quote Details</h3>
                    </x-slot>
                    @php
                        $breakdown = $factoryRequest->cost_breakdown;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Product Price:</span>
                            <span class="font-semibold">Rp {{ number_format($breakdown['product_price'], 0, ',', '.') }}</span>
                        </div>
                        @if($breakdown['delivery_cost'] > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Delivery Cost:</span>
                                <span class="font-semibold">Rp {{ number_format($breakdown['delivery_cost'], 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($breakdown['additional_fees'] > 0)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Additional Fees:</span>
                                <span class="font-semibold">Rp {{ number_format($breakdown['additional_fees'], 0, ',', '.') }}</span>
                            </div>
                            @if($factoryRequest->additional_fees && is_array($factoryRequest->additional_fees))
                                <div class="ml-4 text-sm text-gray-500 space-y-1">
                                    @foreach($factoryRequest->additional_fees as $fee)
                                        <div>- {{ $fee['name'] ?? 'Fee' }}: Rp {{ number_format($fee['amount'] ?? 0, 0, ',', '.') }}</div>
                                    @endforeach
                                </div>
                            @endif
                        @endif
                        <div class="flex justify-between pt-2 border-t border-gray-200 dark:border-gray-700">
                            <span class="text-lg font-semibold">Total Cost:</span>
                            <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($breakdown['total'], 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    @if($factoryRequest->quote_message)
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $factoryRequest->quote_message }}</p>
                        </div>
                    @endif
                </x-card>
            @elseif($factoryRequest->status === 'accepted')
                <!-- Order Management -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Order Management</h3>
                    </x-slot>
                    <form action="{{ route('factories.quotes.update-delivery-status', [$factory, $factoryRequest]) }}" method="POST" class="space-y-4">
                        @csrf
                        <x-form-group label="Delivery Status" name="delivery_status" required>
                            <x-select-input name="delivery_status" required>
                                <option value="pending" {{ $factoryRequest->delivery_status === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="preparing" {{ $factoryRequest->delivery_status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                                <option value="ready" {{ $factoryRequest->delivery_status === 'ready' ? 'selected' : '' }}>Ready for Delivery</option>
                                <option value="in_transit" {{ $factoryRequest->delivery_status === 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                <option value="delivered" {{ $factoryRequest->delivery_status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            </x-select-input>
                        </x-form-group>

                        <x-form-group label="Tracking Number" name="tracking_number">
                            <x-text-input type="text" name="tracking_number" value="{{ $factoryRequest->tracking_number }}" />
                        </x-form-group>

                        <x-form-group label="Delivery Notes" name="delivery_notes">
                            <x-textarea-input name="delivery_notes" rows="3">{{ $factoryRequest->delivery_notes }}</x-textarea-input>
                        </x-form-group>

                        <x-button variant="primary" type="submit">Update Status</x-button>
                    </form>
                </x-card>

                <!-- Chat Link -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Communication</h3>
                    </x-slot>
                    <a href="{{ route('messages.chat.factory-request', ['user' => $factoryRequest->user, 'factoryRequest' => $factoryRequest]) }}" 
                       class="inline-flex items-center space-x-2 text-primary-600 hover:text-primary-800 dark:text-primary-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                        <span>Chat with Contractor</span>
                    </a>
                </x-card>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        let feeIndex = 0;
        function addFee() {
            const container = document.getElementById('fees-list');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" name="additional_fees[${feeIndex}][name]" placeholder="Fee name" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required>
                <input type="number" name="additional_fees[${feeIndex}][amount]" step="0.01" min="0" placeholder="Amount" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required>
                <button type="button" onclick="this.parentElement.remove()" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Remove</button>
            `;
            container.appendChild(div);
            feeIndex++;
        }
    </script>
    @endpush
</x-app-layout>

