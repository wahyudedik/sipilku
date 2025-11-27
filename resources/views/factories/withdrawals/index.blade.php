<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Withdrawal History - {{ $factory->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('factories.withdrawals.create', $factory) }}">
                    <x-button variant="primary" size="sm">Request Withdrawal</x-button>
                </a>
                <a href="{{ route('factories.dashboard', $factory) }}">
                    <x-button variant="secondary" size="sm">Back to Dashboard</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Available Balance -->
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Available Balance</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($availableBalance, 0, ',', '.') }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">
                        Available for withdrawal
                    </p>
                    <a href="{{ route('factories.withdrawals.create', $factory) }}" class="inline-block mt-4">
                        <x-button variant="primary">Request Withdrawal</x-button>
                    </a>
                </div>
            </x-card>

            <!-- Withdrawal History -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Withdrawal History</h3>
                </x-slot>
                @if($withdrawals->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Method</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Account</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($withdrawals as $withdrawal)
                                    <tr>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $withdrawal->created_at->format('d M Y H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm font-semibold">
                                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ ucfirst(str_replace('_', ' ', $withdrawal->method)) }}
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $withdrawal->account_name }}<br>
                                            <span class="text-xs text-gray-500">{{ $withdrawal->account_number }}</span>
                                        </td>
                                        <td class="px-4 py-3">
                                            <x-badge :variant="match($withdrawal->status) {
                                                'completed' => 'success',
                                                'pending' => 'warning',
                                                'rejected' => 'danger',
                                                default => 'default'
                                            }">
                                                {{ ucfirst($withdrawal->status) }}
                                            </x-badge>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $withdrawals->links() }}
                    </div>
                @else
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">No withdrawal history yet.</p>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>

