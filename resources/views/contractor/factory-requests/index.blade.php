<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Factory Requests
            </h2>
            <a href="{{ route('contractor.factory-requests.create') }}">
                <x-button variant="primary">Create Request</x-button>
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <form method="GET" action="{{ route('contractor.factory-requests.index') }}" class="mb-6">
        <x-card>
            <x-form-group label="Filter by Factory Type" name="factory_type">
                <x-select-input name="factory_type" onchange="this.form.submit()">
                    <option value="">All Types</option>
                    @foreach($factoryTypes as $type)
                        <option value="{{ $type->uuid }}" {{ $factoryTypeFilter === $type->uuid ? 'selected' : '' }}>
                            {{ $type->name }}
                        </option>
                    @endforeach
                </x-select-input>
            </x-form-group>
        </x-card>
    </form>

    @if($factoryRequests->count() > 0)
        @php
            // Group requests by request_group_id for comparison
            $groupedRequests = $factoryRequests->groupBy('request_group_id');
        @endphp
        
        @foreach($groupedRequests as $groupId => $groupRequests)
            @if($groupRequests->count() > 1 && $groupRequests->where('status', 'quoted')->count() > 1)
                <x-card class="mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold mb-2">Quote Group: {{ $groupRequests->count() }} Factories</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $groupRequests->where('status', 'quoted')->count() }} quoted, 
                                {{ $groupRequests->where('status', 'pending')->count() }} pending
                            </p>
                        </div>
                        <a href="{{ route('contractor.factory-requests.compare', ['request_group_id' => $groupId]) }}">
                            <x-button variant="primary">Compare Quotes</x-button>
                        </a>
                    </div>
                </x-card>
            @endif
        @endforeach

        <div class="space-y-4">
            @foreach($factoryRequests as $request)
                <x-card>
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold">{{ $request->factory->name ?? 'Factory' }}</h3>
                                <x-badge variant="secondary" size="sm">
                                    {{ $request->factory->factoryType->name ?? 'Factory' }}
                                </x-badge>
                                <x-badge :variant="match($request->status) {
                                    'quoted' => 'success',
                                    'accepted' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    default => 'default'
                                }">{{ ucfirst($request->status) }}</x-badge>
                                @if($request->delivery_status)
                                    <x-badge variant="info" size="sm">
                                        {{ ucfirst(str_replace('_', ' ', $request->delivery_status)) }}
                                    </x-badge>
                                @endif
                            </div>
                            @if($request->projectLocation)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                    Project: {{ $request->projectLocation->name }}
                                </p>
                            @endif
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                Items: {{ count($request->items ?? []) }} items
                            </p>
                            @if($request->total_cost)
                                <div class="space-y-1">
                                    <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                                        Total: Rp {{ number_format($request->total_cost, 0, ',', '.') }}
                                    </p>
                                    @php
                                        $breakdown = $request->cost_breakdown;
                                    @endphp
                                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-0.5">
                                        <div>Product: Rp {{ number_format($breakdown['product_price'], 0, ',', '.') }}</div>
                                        @if($breakdown['delivery_cost'] > 0)
                                            <div>Delivery: Rp {{ number_format($breakdown['delivery_cost'], 0, ',', '.') }}</div>
                                        @endif
                                        @if($breakdown['additional_fees'] > 0)
                                            <div>Additional Fees: Rp {{ number_format($breakdown['additional_fees'], 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($request->tracking_number)
                                <div class="mt-2">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Tracking: <span class="font-mono">{{ $request->tracking_number }}</span>
                                    </p>
                                </div>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Created: {{ $request->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('contractor.factory-requests.show', $request) }}">
                                <x-button variant="secondary" size="sm">View</x-button>
                            </a>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $factoryRequests->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No factory requests yet.</p>
                <a href="{{ route('contractor.factory-requests.create') }}">
                    <x-button variant="primary">Create Your First Request</x-button>
                </a>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

