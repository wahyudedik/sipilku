<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Tarik Saldo
            </h2>
            <a href="{{ route('seller.commissions.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Payout Form -->
        <div class="lg:col-span-2">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Penarikan Saldo</h3>
                </x-slot>
                <form action="{{ route('seller.commissions.process-payout') }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Available Balance -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Saldo Tersedia</p>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($availableBalance, 0, ',', '.') }}
                            </p>
                        </div>

                        <!-- Amount Input -->
                        <x-form-group>
                            <x-slot name="label">Jumlah Penarikan</x-slot>
                            <x-text-input 
                                type="number" 
                                name="amount" 
                                id="amount" 
                                placeholder="Masukkan jumlah penarikan"
                                min="50000"
                                step="1000"
                                value="{{ old('amount') }}"
                                required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Minimum: Rp 50.000
                            </p>
                        </x-form-group>

                        <!-- Payment Method -->
                        <x-form-group>
                            <x-slot name="label">Metode Penarikan</x-slot>
                            <x-select-input 
                                name="method" 
                                id="method"
                                :options="[
                                    'bank_transfer' => 'Transfer Bank',
                                    'e_wallet' => 'E-Wallet'
                                ]" 
                                value="{{ old('method', 'bank_transfer') }}"
                                required />
                        </x-form-group>

                        <!-- Bank Transfer Fields -->
                        <div id="bankFields" class="space-y-4">
                            <x-form-group>
                                <x-slot name="label">Nama Bank</x-slot>
                                <x-select-input 
                                    name="bank_name" 
                                    :options="[
                                        'BCA' => 'BCA',
                                        'Mandiri' => 'Mandiri',
                                        'BNI' => 'BNI',
                                        'BRI' => 'BRI',
                                        'CIMB' => 'CIMB',
                                        'Danamon' => 'Danamon',
                                        'Permata' => 'Permata',
                                        'Lainnya' => 'Lainnya'
                                    ]" 
                                    value="{{ old('bank_name') }}" />
                            </x-form-group>
                        </div>

                        <!-- E-Wallet Fields -->
                        <div id="ewalletFields" class="space-y-4 hidden">
                            <x-form-group>
                                <x-slot name="label">Jenis E-Wallet</x-slot>
                                <x-select-input 
                                    name="e_wallet_type" 
                                    :options="[
                                        'gopay' => 'GoPay',
                                        'ovo' => 'OVO',
                                        'dana' => 'DANA',
                                        'linkaja' => 'LinkAja',
                                        'shopeepay' => 'ShopeePay'
                                    ]" 
                                    value="{{ old('e_wallet_type') }}" />
                            </x-form-group>
                        </div>

                        <!-- Account Name -->
                        <x-form-group>
                            <x-slot name="label">Nama Pemilik Akun</x-slot>
                            <x-text-input 
                                type="text" 
                                name="account_name" 
                                placeholder="Nama sesuai rekening/e-wallet"
                                value="{{ old('account_name') }}"
                                required />
                        </x-form-group>

                        <!-- Account Number -->
                        <x-form-group>
                            <x-slot name="label">Nomor Rekening/E-Wallet</x-slot>
                            <x-text-input 
                                type="text" 
                                name="account_number" 
                                placeholder="Nomor rekening atau nomor e-wallet"
                                value="{{ old('account_number') }}"
                                required />
                        </x-form-group>

                        <!-- Info Alert -->
                        <x-alert type="info">
                            <strong>Catatan:</strong> Penarikan saldo akan diproses dalam 1-3 hari kerja setelah permintaan disetujui oleh admin. Saldo akan ditahan hingga penarikan selesai diproses.
                        </x-alert>

                        <!-- Submit Button -->
                        <div>
                            <x-button variant="primary" size="lg" type="submit" class="w-full">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ajukan Penarikan
                            </x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>

        <!-- Recent Withdrawals -->
        <div>
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Penarikan Terakhir</h3>
                </x-slot>
                @if($recentWithdrawals->count() > 0)
                    <div class="space-y-3">
                        @foreach($recentWithdrawals as $withdrawal)
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
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <p class="text-sm">Belum ada penarikan</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const methodSelect = document.getElementById('method');
            const bankFields = document.getElementById('bankFields');
            const ewalletFields = document.getElementById('ewalletFields');

            function toggleFields() {
                if (methodSelect.value === 'bank_transfer') {
                    bankFields.classList.remove('hidden');
                    ewalletFields.classList.add('hidden');
                    // Make bank_name required
                    bankFields.querySelector('select[name="bank_name"]').required = true;
                    ewalletFields.querySelector('select[name="e_wallet_type"]').required = false;
                } else {
                    bankFields.classList.add('hidden');
                    ewalletFields.classList.remove('hidden');
                    // Make e_wallet_type required
                    bankFields.querySelector('select[name="bank_name"]').required = false;
                    ewalletFields.querySelector('select[name="e_wallet_type"]').required = true;
                }
            }

            methodSelect.addEventListener('change', toggleFields);
            toggleFields(); // Initial call
        });
    </script>
    @endpush
</x-app-with-sidebar>

