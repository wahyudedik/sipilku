<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Komisi & Penghasilan
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seller.commissions.report') }}">
                    <x-button variant="secondary" size="sm">Laporan</x-button>
                </a>
                <a href="{{ route('seller.commissions.payout') }}">
                    <x-button variant="primary" size="sm">
                        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Tarik Saldo
                    </x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Komisi</p>
                <h3 class="text-2xl font-bold text-green-600 dark:text-green-400">
                    Rp {{ number_format($totalCommissions, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Saldo Tersedia</p>
                <h3 class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Penarikan</p>
                <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($totalPayouts, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Komisi Pending</p>
                <h3 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    Rp {{ number_format($pendingCommissions, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
    </div>

    <!-- Monthly Statistics Chart -->
    @if($monthlyStats->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Statistik Bulanan</h3>
            </x-slot>
            <div class="space-y-4">
                @foreach($monthlyStats as $stat)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700 dark:text-gray-300">{{ $stat->month }}</span>
                        <div class="flex items-center space-x-4">
                            <div class="w-48 bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-primary-600 h-4 rounded-full" style="width: {{ min(100, ($stat->total / $totalCommissions) * 100) }}%"></div>
                            </div>
                            <span class="font-semibold text-gray-900 dark:text-gray-100 w-32 text-right">
                                Rp {{ number_format($stat->total, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>
        </x-card>
    @endif

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('seller.commissions.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[150px]">
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'Semua Status',
                        'completed' => 'Completed',
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'failed' => 'Failed'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div class="flex-1 min-w-[150px]">
                <x-text-input type="date" name="date_from" value="{{ request('date_from') }}" placeholder="Dari Tanggal" />
            </div>
            <div class="flex-1 min-w-[150px]">
                <x-text-input type="date" name="date_to" value="{{ request('date_to') }}" placeholder="Sampai Tanggal" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('seller.commissions.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Commission List -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Riwayat Komisi</h3>
                </x-slot>
                @if($commissions->count() > 0)
                    <div class="space-y-4">
                        @foreach($commissions as $commission)
                            <div class="flex items-start justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                            @if($commission->order && $commission->order->orderable)
                                                {{ $commission->order->orderable->title }}
                                            @else
                                                {{ $commission->description }}
                                            @endif
                                        </h4>
                                        <x-badge :variant="match($commission->status) {
                                            'completed' => 'success',
                                            'processing' => 'warning',
                                            'pending' => 'default',
                                            'failed' => 'danger',
                                            default => 'default'
                                        }">
                                            {{ ucfirst($commission->status) }}
                                        </x-badge>
                                    </div>
                                    @if($commission->order)
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Order: <a href="{{ route('orders.show', $commission->order) }}" class="text-primary-600 hover:underline">
                                                {{ $commission->order->uuid }}
                                            </a>
                                        </p>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $commission->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                                <div class="ml-4 text-right">
                                    <p class="text-xl font-bold text-green-600 dark:text-green-400">
                                        +Rp {{ number_format($commission->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $commissions->links() }}
                    </div>
                @else
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <p>Tidak ada komisi.</p>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Withdrawal History -->
        <div>
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Riwayat Penarikan</h3>
                </x-slot>
                @if($withdrawals->count() > 0)
                    <div class="space-y-3">
                        @foreach($withdrawals as $withdrawal)
                            <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">
                                        Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                    </span>
                                    <x-badge :variant="match($withdrawal->status) {
                                        'completed' => 'success',
                                        'processing' => 'warning',
                                        'pending' => 'default',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($withdrawal->status) }}
                                    </x-badge>
                                </div>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $withdrawal->created_at->format('d M Y') }}
                                </p>
                                <a href="{{ route('seller.commissions.show-withdrawal', $withdrawal) }}" class="text-xs text-primary-600 hover:underline mt-1 block">
                                    Lihat Detail
                                </a>
                            </div>
                        @endforeach
                    </div>
                    @if($withdrawals->hasMorePages())
                        <div class="mt-4 text-center">
                            <a href="{{ route('seller.commissions.index', ['withdrawals_page' => 2]) }}" class="text-sm text-primary-600 hover:underline">
                                Lihat Semua
                            </a>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p class="text-sm">Belum ada penarikan</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

