<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Withdrawal Detail
            </h2>
            <a href="{{ route('admin.withdrawals.index') }}">
                <x-button variant="secondary" size="sm">Back to List</x-button>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <x-slot name="header">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium">Withdrawal Information</h3>
                        <x-badge :variant="match($withdrawal->status) {
                            'completed' => 'success',
                            'pending' => 'warning',
                            'rejected' => 'danger',
                            default => 'default'
                        }">
                            {{ ucfirst($withdrawal->status) }}
                        </x-badge>
                    </div>
                </x-slot>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">User</p>
                        <p class="font-semibold text-lg">{{ $withdrawal->user->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $withdrawal->user->email }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Amount</p>
                        <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Payment Method</p>
                        <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $withdrawal->method)) }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Request Date</p>
                        <p class="font-semibold">{{ $withdrawal->created_at->format('d M Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Account Name</p>
                        <p class="font-semibold">{{ $withdrawal->account_name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Account Number</p>
                        <p class="font-semibold">{{ $withdrawal->account_number }}</p>
                    </div>

                    @if($withdrawal->bank_name)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Bank Name</p>
                            <p class="font-semibold">{{ $withdrawal->bank_name }}</p>
                        </div>
                    @endif

                    @if($withdrawal->e_wallet_type)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">E-Wallet Type</p>
                            <p class="font-semibold">{{ ucfirst($withdrawal->e_wallet_type) }}</p>
                        </div>
                    @endif

                    @if($withdrawal->processed_at)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Processed At</p>
                            <p class="font-semibold">{{ $withdrawal->processed_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>

                @if($withdrawal->rejection_reason)
                    <div class="mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                        <p class="text-sm font-semibold text-red-800 dark:text-red-200 mb-1">Rejection Reason:</p>
                        <p class="text-sm text-red-700 dark:text-red-300">{{ $withdrawal->rejection_reason }}</p>
                    </div>
                @endif

                @if($withdrawal->admin_notes)
                    <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-1">Admin Notes:</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $withdrawal->admin_notes }}</p>
                    </div>
                @endif
            </x-card>

            @if($transaction)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Related Transaction</h3>
                    </x-slot>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Transaction ID:</span>
                            <span class="font-semibold">{{ $transaction->uuid }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Status:</span>
                            <x-badge :variant="match($transaction->status) {
                                'completed' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                default => 'default'
                            }" size="sm">
                                {{ ucfirst($transaction->status) }}
                            </x-badge>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Amount:</span>
                            <span class="font-semibold">Rp {{ number_format(abs($transaction->amount), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Created:</span>
                            <span class="font-semibold">{{ $transaction->created_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            @if($withdrawal->status === 'pending')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Actions</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui withdrawal ini?')">
                            @csrf
                            <x-form-group label="Admin Notes (Optional)" name="admin_notes">
                                <x-textarea-input name="admin_notes" rows="3" placeholder="Catatan admin (opsional)"></x-textarea-input>
                            </x-form-group>
                            <x-button variant="success" size="md" type="submit" class="w-full">Approve Withdrawal</x-button>
                        </form>
                        <button type="button" onclick="showRejectModal()" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Reject Withdrawal
                        </button>
                    </div>
                </x-card>
            @endif

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">User Information</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Name:</span>
                        <span class="font-semibold">{{ $withdrawal->user->name }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Email:</span>
                        <span class="font-semibold">{{ $withdrawal->user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Current Balance:</span>
                        <span class="font-semibold">Rp {{ number_format($withdrawal->user->balance, 0, ',', '.') }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Reject Modal -->
    <x-modal id="rejectModal" title="Reject Withdrawal">
        <form action="{{ route('admin.withdrawals.reject', $withdrawal) }}" method="POST">
            @csrf
            <x-form-group label="Rejection Reason" name="rejection_reason" required>
                <x-textarea-input name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan..."></x-textarea-input>
            </x-form-group>
            <x-form-group label="Admin Notes (Optional)" name="admin_notes">
                <x-textarea-input name="admin_notes" rows="3" placeholder="Catatan admin (opsional)"></x-textarea-input>
            </x-form-group>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <x-button variant="danger" type="submit">Reject Withdrawal</x-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        function showRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }
    </script>
    @endpush
</x-app-with-sidebar>

