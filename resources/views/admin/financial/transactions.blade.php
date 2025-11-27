<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Transaction Monitoring
            </h2>
            <a href="{{ route('admin.financial.reports') }}">
                <x-button variant="secondary" size="sm">Financial Reports</x-button>
            </a>
        </div>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Revenue</p>
                <h3 class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Commissions</p>
                <h3 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    Rp {{ number_format($totalCommissions, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Today Revenue</p>
                <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($todayRevenue, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Month Revenue</p>
                <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($monthRevenue, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
    </div>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.financial.transactions') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search transactions..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="type" 
                    :options="[
                        'all' => 'All Types',
                        'purchase' => 'Purchase',
                        'commission' => 'Commission',
                        'payout' => 'Payout',
                        'top-up' => 'Top-up',
                        'refund' => 'Refund'
                    ]" 
                    value="{{ request('type', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'All Status',
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'failed' => 'Failed'
                    ]" 
                    value="{{ request('status', 'all') }}" />
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
                <a href="{{ route('admin.financial.transactions') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($transactions->count() > 0)
        <x-card>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Date</th>
                            <th class="text-left p-3">User</th>
                            <th class="text-left p-3">Type</th>
                            <th class="text-left p-3">Description</th>
                            <th class="text-left p-3">Amount</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Payment Method</th>
                            <th class="text-left p-3">Reference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700">
                                <td class="p-3 text-sm">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                                <td class="p-3">
                                    <div>
                                        <p class="font-medium">{{ $transaction->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $transaction->user->email }}</p>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <x-badge :variant="match($transaction->type) {
                                        'purchase' => 'primary',
                                        'commission' => 'success',
                                        'payout' => 'danger',
                                        'top-up' => 'info',
                                        'refund' => 'warning',
                                        default => 'default'
                                    }" size="sm">
                                        {{ ucfirst($transaction->type) }}
                                    </x-badge>
                                </td>
                                <td class="p-3 text-sm">{{ Str::limit($transaction->description, 50) }}</td>
                                <td class="p-3">
                                    <span class="font-semibold {{ $transaction->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $transaction->amount < 0 ? '-' : '+' }}Rp {{ number_format(abs($transaction->amount), 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="p-3">
                                    <x-badge :variant="match($transaction->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'failed' => 'danger',
                                        default => 'default'
                                    }" size="sm">
                                        {{ ucfirst($transaction->status) }}
                                    </x-badge>
                                </td>
                                <td class="p-3 text-sm">{{ $transaction->payment_method ?? '-' }}</td>
                                <td class="p-3 text-xs text-gray-500">{{ $transaction->payment_reference ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $transactions->links() }}
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>No transactions found.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

