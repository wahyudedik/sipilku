<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Saldo Saya
            </h2>
            <a href="{{ route('balance.top-up') }}">
                <x-button variant="primary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Top-up Saldo
                </x-button>
            </a>
        </div>
    </x-slot>

    @if(request('status') === 'success')
        <x-alert type="success" class="mb-6">
            Top-up saldo berhasil! Saldo Anda telah ditambahkan.
        </x-alert>
    @elseif(request('status') === 'pending')
        <x-alert type="warning" class="mb-6">
            Top-up saldo sedang diproses. Anda akan menerima notifikasi setelah pembayaran dikonfirmasi.
        </x-alert>
    @elseif(request('status') === 'error')
        <x-alert type="error" class="mb-6">
            Top-up saldo gagal. Silakan coba lagi atau hubungi customer service.
        </x-alert>
    @endif

    <!-- Balance Card -->
    <x-card class="mb-6">
        <div class="text-center py-8">
            <div class="mb-4">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Saldo Tersedia</p>
                <h1 class="text-5xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($user->balance, 0, ',', '.') }}
                </h1>
            </div>
            <div class="flex justify-center space-x-6 mt-6">
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Deposit</p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">
                        Rp {{ number_format($totalDeposits, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Pembelian</p>
                    <p class="text-lg font-semibold text-red-600 dark:text-red-400">
                        Rp {{ number_format($totalPurchases, 0, ',', '.') }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-xs text-gray-500 dark:text-gray-400">Total Komisi</p>
                    <p class="text-lg font-semibold text-blue-600 dark:text-blue-400">
                        Rp {{ number_format($totalCommissions, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('balance.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[150px]">
                <x-select-input 
                    name="type" 
                    :options="[
                        'all' => 'Semua Tipe',
                        'deposit' => 'Deposit',
                        'purchase' => 'Pembelian',
                        'commission' => 'Komisi',
                        'withdrawal' => 'Penarikan',
                        'refund' => 'Refund',
                        'payout' => 'Payout'
                    ]" 
                    value="{{ request('type', 'all') }}" />
            </div>
            <div class="flex-1 min-w-[150px]">
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'Semua Status',
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                        'cancelled' => 'Cancelled'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('balance.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Transaction History -->
    @if($transactions->count() > 0)
        <div class="space-y-4">
            @foreach($transactions as $transaction)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    @if($transaction->type === 'deposit')
                                        Top-up Saldo
                                    @elseif($transaction->type === 'purchase')
                                        Pembelian
                                    @elseif($transaction->type === 'withdrawal')
                                        Penarikan
                                    @elseif($transaction->type === 'commission')
                                        Komisi
                                    @elseif($transaction->type === 'refund')
                                        Refund
                                    @elseif($transaction->type === 'payout')
                                        Payout
                                    @else
                                        {{ ucfirst($transaction->type) }}
                                    @endif
                                </h3>
                                <x-badge :variant="match($transaction->status) {
                                    'completed' => 'success',
                                    'processing' => 'warning',
                                    'pending' => 'default',
                                    'failed' => 'danger',
                                    'cancelled' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($transaction->status) }}
                                </x-badge>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ $transaction->description }}
                            </p>
                            @if($transaction->order)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Order: <a href="{{ route('orders.show', $transaction->order) }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        {{ $transaction->order->uuid }}
                                    </a>
                                </p>
                            @endif
                            @if($transaction->payment_reference)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Reference: {{ $transaction->payment_reference }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                                {{ $transaction->created_at->format('d M Y H:i') }}
                                @if($transaction->completed_at)
                                    • Selesai: {{ $transaction->completed_at->format('d M Y H:i') }}
                                @endif
                            </p>
                        </div>
                        <div class="ml-4 text-right">
                            <p class="text-2xl font-bold {{ $transaction->amount < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ $transaction->amount < 0 ? '-' : '+' }}Rp {{ number_format(abs($transaction->amount), 0, ',', '.') }}
                            </p>
                            @if($transaction->payment_method)
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ ucfirst($transaction->payment_method) }}
                                </p>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $transactions->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada transaksi</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum memiliki transaksi saldo.</p>
                <div class="mt-6">
                    <a href="{{ route('balance.top-up') }}">
                        <x-button variant="primary">Top-up Saldo</x-button>
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

