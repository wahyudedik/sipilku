<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Commission Management
            </h2>
        </div>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Commissions</p>
                <h3 class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($totalCommissions, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending Commissions</p>
                <h3 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    Rp {{ number_format($pendingCommissions, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Month Commissions</p>
                <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($monthCommissions, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
    </div>

    <!-- Top Earners -->
    @if($topEarners->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Top Earners</h3>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Rank</th>
                            <th class="text-left p-3">Seller</th>
                            <th class="text-left p-3">Total Commissions</th>
                            <th class="text-left p-3">Transaction Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topEarners as $index => $earner)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-3 font-bold">#{{ $index + 1 }}</td>
                                <td class="p-3">
                                    <div>
                                        <p class="font-medium">{{ $earner->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $earner->user->email }}</p>
                                    </div>
                                </td>
                                <td class="p-3 font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($earner->total, 0, ',', '.') }}
                                </td>
                                <td class="p-3">{{ $earner->count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-card>
    @endif

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.financial.commissions') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search commissions..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'All Status',
                        'completed' => 'Completed',
                        'pending' => 'Pending'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="user_id" 
                    :options="['' => 'All Sellers'] + $sellers->pluck('name', 'id')->toArray()" 
                    value="{{ request('user_id', '') }}" />
            </div>
            <div>
                <x-text-input 
                    name="date_from" 
                    type="date" 
                    value="{{ request('date_from') }}" 
                    placeholder="From Date" />
            </div>
            <div>
                <x-text-input 
                    name="date_to" 
                    type="date" 
                    value="{{ request('date_to') }}" 
                    placeholder="To Date" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('admin.financial.commissions') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($commissions->count() > 0)
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Date</th>
                            <th class="text-left p-3">Seller</th>
                            <th class="text-left p-3">Order</th>
                            <th class="text-left p-3">Description</th>
                            <th class="text-left p-3">Amount</th>
                            <th class="text-left p-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commissions as $commission)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-3 text-sm">{{ $commission->created_at->format('d M Y H:i') }}</td>
                                <td class="p-3">
                                    <div>
                                        <p class="font-medium">{{ $commission->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $commission->user->email }}</p>
                                    </div>
                                </td>
                                <td class="p-3 text-sm">
                                    @if($commission->order)
                                        <a href="{{ route('admin.orders.show', $commission->order) }}" class="text-primary-600 hover:underline">
                                            {{ $commission->order->uuid }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="p-3 text-sm">{{ Str::limit($commission->description, 50) }}</td>
                                <td class="p-3 font-semibold text-green-600 dark:text-green-400">
                                    Rp {{ number_format($commission->amount, 0, ',', '.') }}
                                </td>
                                <td class="p-3">
                                    <x-badge :variant="match($commission->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        default => 'default'
                                    }" size="sm">
                                        {{ ucfirst($commission->status) }}
                                    </x-badge>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $commissions->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>No commissions found.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

