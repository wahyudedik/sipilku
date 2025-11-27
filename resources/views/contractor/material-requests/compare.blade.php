<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Compare Material Quotes
        </h2>
    </x-slot>

    @if($quoted->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Quoted Requests ({{ $quoted->count() }})</h3>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Store</th>
                            <th class="text-left p-3">Quoted Price</th>
                            <th class="text-left p-3">Quote Message</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($quotedSorted as $request)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="p-3">
                                    <div class="flex items-center space-x-2">
                                        @if($request->store->logo)
                                            <img src="{{ Storage::url($request->store->logo) }}" 
                                                 alt="{{ $request->store->name }}" 
                                                 class="w-10 h-10 rounded">
                                        @endif
                                        <div>
                                            <p class="font-semibold">{{ $request->store->name }}</p>
                                            @if($request->store->rating > 0)
                                                <p class="text-xs text-gray-500">⭐ {{ $request->store->rating }}/5</p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($request->quoted_price, 0, ',', '.') }}
                                    </p>
                                </td>
                                <td class="p-3">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                        {{ $request->quote_message ?? '-' }}
                                    </p>
                                </td>
                                <td class="p-3">
                                    <x-badge :variant="match($request->status) {
                                        'quoted' => 'success',
                                        'accepted' => 'success',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($request->status) }}
                                    </x-badge>
                                </td>
                                <td class="p-3">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('contractor.material-requests.show', $request) }}" 
                                           class="text-primary-600 hover:text-primary-800">
                                            View Details
                                        </a>
                                        @if($request->status === 'quoted')
                                            <form action="{{ route('contractor.material-requests.accept', $request) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:text-green-800">
                                                    Accept
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

    @if($pending->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Pending Requests ({{ $pending->count() }})</h3>
            </x-slot>
            <p class="text-gray-600 dark:text-gray-400">
                Waiting for quotes from {{ $pending->count() }} store(s).
            </p>
        </x-card>
    @endif

    @if($accepted->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Accepted Requests ({{ $accepted->count() }})</h3>
            </x-slot>
            <div class="space-y-3">
                @foreach($accepted as $request)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="font-semibold">{{ $request->store->name }}</p>
                                <p class="text-lg text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($request->quoted_price, 0, ',', '.') }}
                                </p>
                            </div>
                            <a href="{{ route('contractor.material-requests.show', $request) }}" 
                               class="text-primary-600 hover:text-primary-800">
                                View Details →
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('contractor.material-requests.index') }}">
            <x-button variant="secondary">Back to List</x-button>
        </a>
    </div>
</x-app-with-sidebar>

