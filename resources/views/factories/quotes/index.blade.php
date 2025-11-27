@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Quote Requests - {{ $factory->name }}
            </h2>
            <a href="{{ route('factories.my-factory') }}">
                <x-button variant="secondary" size="sm">Back to Dashboard</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('factories.quotes.index', $factory) }}" class="flex items-center space-x-4">
                    <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="quoted" {{ $status === 'quoted' ? 'selected' : '' }}>Quoted</option>
                        <option value="accepted" {{ $status === 'accepted' ? 'selected' : '' }}>Accepted</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </form>
            </x-card>

            @if($requests->count() > 0)
                <div class="space-y-4">
                    @foreach($requests as $request)
                        <x-card>
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold">{{ $request->user->name ?? 'Contractor' }}</h3>
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
                                        <p class="text-lg font-semibold text-primary-600 dark:text-primary-400">
                                            Total: Rp {{ number_format($request->total_cost, 0, ',', '.') }}
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                        Requested: {{ $request->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('factories.quotes.show', [$factory, $request]) }}">
                                        <x-button variant="primary" size="sm">View</x-button>
                                    </a>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $requests->links() }}
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">No quote requests found.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

