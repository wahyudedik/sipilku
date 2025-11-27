<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Withdrawal History
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('store.withdrawals.create') }}">
                    <x-button variant="primary" size="sm">Request Payout</x-button>
                </a>
                <a href="{{ route('store.dashboard') }}">
                    <x-button variant="secondary" size="sm">Back to Dashboard</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Balance Summary -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Balance Summary</h3>
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Earnings</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                    Rp {{ number_format($earnings['total_earnings'], 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Net Earnings</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($earnings['net_earnings'], 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Available Balance</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </p>
            </div>
        </div>
    </x-card>

    <!-- Withdrawals Table -->
    <x-card>
        @if($withdrawals->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left p-3">Date</th>
                            <th class="text-left p-3">Amount</th>
                            <th class="text-left p-3">Method</th>
                            <th class="text-left p-3">Account</th>
                            <th class="text-left p-3">Status</th>
                            <th class="text-left p-3">Processed At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($withdrawals as $withdrawal)
                            <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="p-3">
                                    <span class="text-sm">{{ $withdrawal->created_at->format('d M Y') }}</span>
                                </td>
                                <td class="p-3">
                                    <p class="font-semibold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                    </p>
                                </td>
                                <td class="p-3">
                                    <span class="capitalize">{{ str_replace('_', ' ', $withdrawal->method) }}</span>
                                </td>
                                <td class="p-3">
                                    <p class="font-semibold">{{ $withdrawal->account_name }}</p>
                                    <p class="text-sm text-gray-500">{{ $withdrawal->account_number }}</p>
                                    @if($withdrawal->bank_name)
                                        <p class="text-xs text-gray-400">{{ $withdrawal->bank_name }}</p>
                                    @endif
                                </td>
                                <td class="p-3">
                                    <x-badge :variant="match($withdrawal->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($withdrawal->status) }}
                                    </x-badge>
                                </td>
                                <td class="p-3">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $withdrawal->processed_at?->format('d M Y H:i') ?? '-' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $withdrawals->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No withdrawal history</p>
                <a href="{{ route('store.withdrawals.create') }}">
                    <x-button variant="primary">Request Your First Payout</x-button>
                </a>
            </div>
        @endif
    </x-card>
</x-app-with-sidebar>

