<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Request Payout
            </h2>
            <a href="{{ route('store.withdrawals.index') }}">
                <x-button variant="secondary" size="sm">Back to History</x-button>
            </a>
        </div>
    </x-slot>

    <!-- Balance Info -->
    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Available Balance</h3>
        </x-slot>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Earnings</p>
                <p class="text-xl font-bold text-gray-900 dark:text-gray-100">
                    Rp {{ number_format($earnings['total_earnings'], 0, ',', '.') }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Platform Fee (10%)</p>
                <p class="text-xl font-bold text-red-600">
                    - Rp {{ number_format($earnings['total_commission'], 0, ',', '.') }}
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

    <!-- Payout Form -->
    <x-card>
        <x-slot name="header">
            <h3 class="text-lg font-medium">Payout Request Form</h3>
        </x-slot>
        <form action="{{ route('store.withdrawals.store') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <x-form-group label="Amount" name="amount" required>
                    <x-text-input 
                        name="amount" 
                        type="number" 
                        step="0.01" 
                        min="100000" 
                        max="{{ $availableBalance }}"
                        value="{{ old('amount') }}"
                        placeholder="Minimum: Rp 100.000"
                        required />
                    <p class="text-xs text-gray-500 mt-1">
                        Maximum: Rp {{ number_format($availableBalance, 0, ',', '.') }}
                    </p>
                </x-form-group>

                <x-form-group label="Payment Method" name="method" required>
                    <x-select-input name="method" id="paymentMethod" required onchange="togglePaymentFields()">
                        <option value="">Select Method</option>
                        <option value="bank_transfer" {{ old('method') === 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="e_wallet" {{ old('method') === 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                    </x-select-input>
                </x-form-group>

                <x-form-group label="Account Name" name="account_name" required>
                    <x-text-input name="account_name" value="{{ old('account_name') }}" required />
                </x-form-group>

                <x-form-group label="Account Number" name="account_number" required>
                    <x-text-input name="account_number" value="{{ old('account_number') }}" required />
                </x-form-group>

                <div id="bankFields" style="display: none;">
                    <x-form-group label="Bank Name" name="bank_name">
                        <x-text-input name="bank_name" value="{{ old('bank_name') }}" placeholder="e.g., BCA, Mandiri, BNI" />
                    </x-form-group>
                </div>

                <div id="eWalletFields" style="display: none;">
                    <x-form-group label="E-Wallet Type" name="e_wallet_type">
                        <x-select-input name="e_wallet_type">
                            <option value="">Select E-Wallet</option>
                            <option value="ovo" {{ old('e_wallet_type') === 'ovo' ? 'selected' : '' }}>OVO</option>
                            <option value="dana" {{ old('e_wallet_type') === 'dana' ? 'selected' : '' }}>DANA</option>
                            <option value="gopay" {{ old('e_wallet_type') === 'gopay' ? 'selected' : '' }}>GoPay</option>
                            <option value="linkaja" {{ old('e_wallet_type') === 'linkaja' ? 'selected' : '' }}>LinkAja</option>
                        </x-select-input>
                    </x-form-group>
                </div>

                <div class="flex space-x-4">
                    <x-button variant="primary" type="submit">Submit Request</x-button>
                    <a href="{{ route('store.withdrawals.index') }}">
                        <x-button variant="secondary" type="button">Cancel</x-button>
                    </a>
                </div>
            </div>
        </form>
    </x-card>

    @push('scripts')
    <script>
        function togglePaymentFields() {
            const method = document.getElementById('paymentMethod').value;
            const bankFields = document.getElementById('bankFields');
            const eWalletFields = document.getElementById('eWalletFields');

            if (method === 'bank_transfer') {
                bankFields.style.display = 'block';
                eWalletFields.style.display = 'none';
            } else if (method === 'e_wallet') {
                bankFields.style.display = 'none';
                eWalletFields.style.display = 'block';
            } else {
                bankFields.style.display = 'none';
                eWalletFields.style.display = 'none';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', togglePaymentFields);
    </script>
    @endpush
</x-app-with-sidebar>

