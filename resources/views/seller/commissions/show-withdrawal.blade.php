<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Penarikan
            </h2>
            <a href="{{ route('seller.commissions.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <x-card>
        <div class="space-y-6">
            <!-- Status -->
            <div class="flex items-center justify-between">
                <span class="text-gray-600 dark:text-gray-400">Status</span>
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

            <!-- Amount -->
            <div class="flex items-center justify-between">
                <span class="text-gray-600 dark:text-gray-400">Jumlah</span>
                <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                </span>
            </div>

            <!-- Method -->
            <div class="flex items-center justify-between">
                <span class="text-gray-600 dark:text-gray-400">Metode</span>
                <span class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $withdrawal->method === 'bank_transfer' ? 'Transfer Bank' : 'E-Wallet' }}
                </span>
            </div>

            <!-- Account Details -->
            @if($withdrawal->method === 'bank_transfer')
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Bank</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $withdrawal->bank_name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nama Pemilik</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $withdrawal->account_name }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nomor Rekening</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $withdrawal->account_number }}
                        </span>
                    </div>
                </div>
            @else
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Jenis E-Wallet</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ ucfirst($withdrawal->e_wallet_type ?? 'N/A') }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nama Pemilik</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $withdrawal->account_name }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Nomor E-Wallet</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $withdrawal->account_number }}
                        </span>
                    </div>
                </div>
            @endif

            <!-- Dates -->
            <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Tanggal Permintaan</span>
                    <span class="font-medium text-gray-900 dark:text-gray-100">
                        {{ $withdrawal->created_at->format('d M Y H:i') }}
                    </span>
                </div>
                @if($withdrawal->processed_at)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Tanggal Diproses</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $withdrawal->processed_at->format('d M Y H:i') }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Admin Notes -->
            @if($withdrawal->admin_notes)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Catatan Admin</p>
                    <p class="text-gray-900 dark:text-gray-100">{{ $withdrawal->admin_notes }}</p>
                </div>
            @endif

            <!-- Rejection Reason -->
            @if($withdrawal->rejection_reason)
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                    <x-alert type="error">
                        <strong>Alasan Penolakan:</strong> {{ $withdrawal->rejection_reason }}
                    </x-alert>
                </div>
            @endif
        </div>
    </x-card>
</x-app-with-sidebar>

