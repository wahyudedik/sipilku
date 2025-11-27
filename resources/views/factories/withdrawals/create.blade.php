<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Request Withdrawal - {{ $factory->name }}
            </h2>
            <a href="{{ route('factories.withdrawals.index', $factory) }}">
                <x-button variant="secondary" size="sm">Back</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <!-- Available Balance -->
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Available Balance</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($availableBalance, 0, ',', '.') }}
                    </p>
                </div>
            </x-card>

            <!-- Withdrawal Form -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Withdrawal Request</h3>
                </x-slot>
                <form action="{{ route('factories.withdrawals.store', $factory) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <x-form-group label="Amount *" name="amount" required>
                            <x-text-input type="number" name="amount" step="0.01" min="10000" 
                                         max="{{ $availableBalance }}" required />
                            <x-input-error :messages="$errors->get('amount')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Minimum: Rp 10.000</p>
                        </x-form-group>

                        <x-form-group label="Withdrawal Method *" name="method" required>
                            <x-select-input name="method" id="withdrawal_method" onchange="toggleMethodFields()" required>
                                <option value="">Select Method</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="e_wallet">E-Wallet</option>
                            </x-select-input>
                            <x-input-error :messages="$errors->get('method')" class="mt-2" />
                        </x-form-group>

                        <x-form-group label="Account Name *" name="account_name" required>
                            <x-text-input type="text" name="account_name" required />
                            <x-input-error :messages="$errors->get('account_name')" class="mt-2" />
                        </x-form-group>

                        <x-form-group label="Account Number *" name="account_number" required>
                            <x-text-input type="text" name="account_number" required />
                            <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                        </x-form-group>

                        <div id="bank_fields" style="display: none;">
                            <x-form-group label="Bank Name *" name="bank_name">
                                <x-text-input type="text" name="bank_name" />
                                <x-input-error :messages="$errors->get('bank_name')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <div id="ewallet_fields" style="display: none;">
                            <x-form-group label="E-Wallet Type *" name="e_wallet_type">
                                <x-select-input name="e_wallet_type">
                                    <option value="">Select E-Wallet</option>
                                    <option value="gopay">GoPay</option>
                                    <option value="ovo">OVO</option>
                                    <option value="dana">DANA</option>
                                    <option value="linkaja">LinkAja</option>
                                </x-select-input>
                                <x-input-error :messages="$errors->get('e_wallet_type')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <div class="flex justify-end">
                            <x-button variant="primary" type="submit">Submit Request</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function toggleMethodFields() {
            const method = document.getElementById('withdrawal_method').value;
            const bankFields = document.getElementById('bank_fields');
            const ewalletFields = document.getElementById('ewallet_fields');
            
            if (method === 'bank_transfer') {
                bankFields.style.display = 'block';
                ewalletFields.style.display = 'none';
            } else if (method === 'e_wallet') {
                bankFields.style.display = 'none';
                ewalletFields.style.display = 'block';
            } else {
                bankFields.style.display = 'none';
                ewalletFields.style.display = 'none';
            }
        }
    </script>
    @endpush
</x-app-layout>

