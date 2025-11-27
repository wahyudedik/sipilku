<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Contractor Dashboard
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('contractor.material-procurement') }}">
                    <x-button variant="primary" size="sm">Material Procurement</x-button>
                </a>
                <a href="{{ route('contractor.factory-procurement') }}">
                    <x-button variant="secondary" size="sm">Factory Procurement</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <!-- Service Earnings -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Service Earnings</p>
                <h3 class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($serviceEarnings['total'], 0, ',', '.') }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Bulan ini: Rp {{ number_format($serviceEarnings['monthly'], 0, ',', '.') }}
                </p>
            </div>
        </x-card>

        <!-- Store Integration Stats -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Material Requests</p>
                <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                    {{ $storeStats['total_requests'] }}
                </h3>
                <div class="flex justify-center space-x-2 mt-2 text-xs">
                    <span class="text-yellow-600">{{ $storeStats['pending'] }} pending</span>
                    <span class="text-green-600">{{ $storeStats['accepted'] }} accepted</span>
                </div>
            </div>
        </x-card>

        <!-- Factory Integration Stats -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Factory Requests</p>
                <h3 class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                    {{ $factoryStats['total_requests'] }}
                </h3>
                <div class="flex justify-center space-x-2 mt-2 text-xs">
                    <span class="text-yellow-600">{{ $factoryStats['pending'] }} pending</span>
                    <span class="text-green-600">{{ $factoryStats['accepted'] }} accepted</span>
                </div>
            </div>
        </x-card>

        <!-- Active Service Orders -->
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Active Service Orders</p>
                <h3 class="text-3xl font-bold text-green-600 dark:text-green-400">
                    {{ $activeServiceOrders->count() }}
                </h3>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Active Service Orders -->
        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Active Service Orders</h3>
            </x-slot>
            @if($activeServiceOrders->count() > 0)
                <div class="space-y-4">
                    @foreach($activeServiceOrders as $order)
                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">{{ $order->orderable->title ?? 'Service Order' }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Status: <x-badge variant="warning">{{ ucfirst($order->status) }}</x-badge>
                                    </p>
                                    <p class="text-sm font-semibold mt-1">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </p>
                                </div>
                                <a href="{{ route('orders.show', $order) }}" class="text-primary-600 hover:underline">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-4">No active service orders</p>
            @endif
        </x-card>

        <!-- Material Requests -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Material Requests & Quotes</h3>
                    <a href="{{ route('contractor.material-requests.index') }}" class="text-sm text-primary-600 hover:underline">
                        View All
                    </a>
                </div>
            </x-slot>
            @if($materialRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($materialRequests as $request)
                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">{{ $request->store->name ?? 'Store' }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Status: <x-badge :variant="match($request->status) {
                                            'quoted' => 'success',
                                            'accepted' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger',
                                            default => 'default'
                                        }">{{ ucfirst($request->status) }}</x-badge>
                                    </p>
                                    @if($request->quoted_price)
                                        <p class="text-sm font-semibold mt-1">
                                            Rp {{ number_format($request->quoted_price, 0, ',', '.') }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('contractor.material-requests.show', $request) }}" class="text-primary-600 hover:underline">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-4">No material requests</p>
            @endif
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Factory Requests -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Factory Product Requests & Quotes</h3>
                    <a href="{{ route('contractor.factory-requests.index') }}" class="text-sm text-primary-600 hover:underline">
                        View All
                    </a>
                </div>
            </x-slot>
            @if($factoryRequests->count() > 0)
                <div class="space-y-4">
                    @foreach($factoryRequests as $request)
                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">{{ $request->factory->name ?? 'Factory' }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $request->factory->factoryType->name ?? 'Factory Type' }}
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Status: <x-badge :variant="match($request->status) {
                                            'quoted' => 'success',
                                            'accepted' => 'success',
                                            'pending' => 'warning',
                                            'rejected' => 'danger',
                                            default => 'default'
                                        }">{{ ucfirst($request->status) }}</x-badge>
                                    </p>
                                    @if($request->total_cost)
                                        <p class="text-sm font-semibold mt-1">
                                            Total: Rp {{ number_format($request->total_cost, 0, ',', '.') }}
                                            @if($request->delivery_cost)
                                                <span class="text-xs text-gray-500">(Delivery: Rp {{ number_format($request->delivery_cost, 0, ',', '.') }})</span>
                                            @endif
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('contractor.factory-requests.show', $request) }}" class="text-primary-600 hover:underline">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-4">No factory requests</p>
            @endif
        </x-card>

        <!-- Project Locations -->
        <x-card>
            <x-slot name="header">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-medium">Project Locations</h3>
                    <a href="{{ route('contractor.project-locations.create') }}" class="text-sm text-primary-600 hover:underline">
                        Add New
                    </a>
                </div>
            </x-slot>
            @if($projectLocations->count() > 0)
                <div class="space-y-4">
                    @foreach($projectLocations as $location)
                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-medium">{{ $location->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $location->full_address }}
                                    </p>
                                    @if($location->hasCoordinates())
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $location->latitude }}, {{ $location->longitude }}
                                        </p>
                                    @endif
                                </div>
                                <a href="{{ route('contractor.project-locations.edit', $location) }}" class="text-primary-600 hover:underline">
                                    Edit
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 dark:text-gray-400 py-4">No project locations. <a href="{{ route('contractor.project-locations.create') }}" class="text-primary-600 hover:underline">Add one</a></p>
            @endif
        </x-card>
    </div>

    <!-- Recommended Stores Nearby -->
    @if($recommendedStores->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Recommended Stores Nearby</h3>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($recommendedStores as $recommended)
                    <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <h4 class="font-medium">{{ $recommended['store']->name }}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $recommended['store_location']->full_address }}
                        </p>
                        <p class="text-sm font-semibold text-primary-600 dark:text-primary-400 mt-2">
                            {{ number_format($recommended['distance'], 2) }} km away
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            Project: {{ $recommended['project_location']->name }}
                        </p>
                        <a href="{{ route('contractor.material-procurement', ['store' => $recommended['store']->uuid]) }}" class="text-sm text-primary-600 hover:underline mt-2 inline-block">
                            View Store →
                        </a>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Recommended Factories Nearby -->
    @if($recommendedFactories->count() > 0)
        @php
            // Transform recommended factories data to match widget format
            $factoryRecommendations = $recommendedFactories->map(function($recommended) {
                return [
                    'factory' => $recommended['factory'],
                    'distance' => $recommended['distance'] ?? 0,
                    'delivery_cost' => $recommended['delivery_cost'] ?? 0,
                    'nearest_location' => $recommended['factory_location'] ?? $recommended['factory']->locations->first(),
                    'recommendation_score' => null, // Can be calculated if needed
                ];
            });
        @endphp
        
        <x-recommended-factories-widget 
            :recommendations="$factoryRecommendations" 
            title="Rekomendasi Pabrik Terdekat"
            :showViewAll="false" />
    @endif

    <!-- Quick Actions -->
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium">Quick Actions</h3>
        </x-slot>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('contractor.material-procurement') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <span class="text-sm font-medium text-center">Material Procurement</span>
            </a>
            <a href="{{ route('contractor.factory-procurement') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span class="text-sm font-medium text-center">Factory Procurement</span>
            </a>
            <a href="{{ route('contractor.factory-cost-calculator') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                <span class="text-sm font-medium text-center">Cost Calculator</span>
            </a>
            <a href="{{ route('contractor.project-locations.create') }}" class="flex flex-col items-center p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-8 h-8 text-primary-600 dark:text-primary-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="text-sm font-medium text-center">Add Project Location</span>
            </a>
        </div>
    </x-card>
</x-app-with-sidebar>

