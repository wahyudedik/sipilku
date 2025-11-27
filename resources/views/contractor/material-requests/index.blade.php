<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Material Requests
            </h2>
            <a href="{{ route('contractor.material-requests.create') }}">
                <x-button variant="primary">Create Request</x-button>
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    @if($materialRequests->count() > 0)
        @if($requestGroups->count() > 0)
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Compare Quotes</h3>
                </x-slot>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">
                    You have multiple requests that can be compared:
                </p>
                <div class="space-y-2">
                    @foreach($requestGroups as $groupId)
                        @php
                            $groupRequests = $materialRequests->where('request_group_id', $groupId);
                            $quotedCount = $groupRequests->where('status', 'quoted')->count();
                        @endphp
                        @if($quotedCount > 0)
                            <a href="{{ route('contractor.material-requests.compare', ['request_group_id' => $groupId]) }}" 
                               class="block p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800">
                                <div class="flex justify-between items-center">
                                    <span class="font-medium">Request Group</span>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $quotedCount }} quote(s) available
                                    </span>
                                </div>
                            </a>
                        @endif
                    @endforeach
                </div>
            </x-card>
        @endif

        <div class="space-y-4">
            @foreach($materialRequests as $request)
                <x-card>
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold">{{ $request->store->name ?? 'Store' }}</h3>
                                <x-badge :variant="match($request->status) {
                                    'quoted' => 'success',
                                    'accepted' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    default => 'default'
                                }">{{ ucfirst($request->status) }}</x-badge>
                                @if($request->delivery_status)
                                    <x-badge variant="info">{{ $request->delivery_status_label }}</x-badge>
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
                            @if($request->quoted_price)
                                <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($request->quoted_price, 0, ',', '.') }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                Created: {{ $request->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('contractor.material-requests.show', $request) }}">
                                <x-button variant="secondary" size="sm">View</x-button>
                            </a>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $materialRequests->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No material requests yet.</p>
                <a href="{{ route('contractor.material-requests.create') }}">
                    <x-button variant="primary">Create Your First Request</x-button>
                </a>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

