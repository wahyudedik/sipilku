<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Withdrawal Approval
            </h2>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending Withdrawals</p>
                <h3 class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">
                    {{ $pendingWithdrawals }}
                </h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Rp {{ number_format($pendingAmount, 0, ',', '.') }}
                </p>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Withdrawals</p>
                <h3 class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    Rp {{ number_format($totalWithdrawals, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>

        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Month Withdrawals</p>
                <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    Rp {{ number_format($monthWithdrawals, 0, ',', '.') }}
                </h3>
            </div>
        </x-card>
    </div>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Search withdrawals..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'All Status',
                        'pending' => 'Pending',
                        'completed' => 'Completed',
                        'rejected' => 'Rejected'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="method" 
                    :options="[
                        'all' => 'All Methods',
                        'bank_transfer' => 'Bank Transfer',
                        'e_wallet' => 'E-Wallet'
                    ]" 
                    value="{{ request('method', 'all') }}" />
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
                <a href="{{ route('admin.withdrawals.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($withdrawals->count() > 0)
        <!-- Bulk Actions -->
        @if($pendingWithdrawals > 0)
            <x-card class="mb-6">
                <form id="bulkActionForm" action="{{ route('admin.withdrawals.bulk-approve') }}" method="POST">
                    @csrf
                    <div class="flex items-center gap-4">
                        <x-button variant="success" size="md" type="submit" id="bulkApproveBtn" disabled>
                            Approve Selected
                        </x-button>
                        <span id="selectedCount" class="text-sm text-gray-600 dark:text-gray-400">0 withdrawals selected</span>
                    </div>
                </form>
            </x-card>
        @endif

        <div class="space-y-4">
            @foreach($withdrawals as $withdrawal)
                <x-card>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 pt-1">
                            @if($withdrawal->status === 'pending')
                                <input type="checkbox" name="withdrawal_ids[]" value="{{ $withdrawal->uuid }}" 
                                       class="withdrawal-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('admin.withdrawals.show', $withdrawal) }}" class="hover:text-primary-600">
                                        {{ $withdrawal->user->name }}
                                    </a>
                                </h3>
                                <x-badge :variant="match($withdrawal->status) {
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($withdrawal->status) }}
                                </x-badge>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Amount</p>
                                    <p class="font-semibold text-lg text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Method</p>
                                    <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $withdrawal->method)) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Account</p>
                                    <p class="font-medium">{{ $withdrawal->account_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $withdrawal->account_number }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-600 dark:text-gray-400">Date</p>
                                    <p class="font-medium">{{ $withdrawal->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            @if($withdrawal->rejection_reason)
                                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                    <strong>Rejection Reason:</strong> {{ $withdrawal->rejection_reason }}
                                </p>
                            @endif
                            @if($withdrawal->admin_notes)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                                    <strong>Admin Notes:</strong> {{ $withdrawal->admin_notes }}
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('admin.withdrawals.show', $withdrawal) }}">
                                <x-button variant="secondary" size="sm">View Details</x-button>
                            </a>
                            @if($withdrawal->status === 'pending')
                                <form action="{{ route('admin.withdrawals.approve', $withdrawal) }}" method="POST" class="inline">
                                    @csrf
                                    <x-button variant="success" size="sm" type="submit">Approve</x-button>
                                </form>
                                <button type="button" onclick="showRejectModal('{{ $withdrawal->uuid }}', '{{ $withdrawal->user->name }}')" class="w-full px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                    Reject
                                </button>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $withdrawals->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>No withdrawals found.</p>
            </div>
        </x-card>
    @endif

    <!-- Reject Modal -->
    <x-modal id="rejectModal" title="Reject Withdrawal">
        <form id="rejectForm" method="POST">
            @csrf
            <x-form-group label="User" name="user_name">
                <p id="modalUserName" class="text-gray-700 dark:text-gray-300"></p>
            </x-form-group>
            <x-form-group label="Rejection Reason" name="rejection_reason" required>
                <x-textarea-input name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan..."></x-textarea-input>
            </x-form-group>
            <x-form-group label="Admin Notes (Optional)" name="admin_notes">
                <x-textarea-input name="admin_notes" rows="3" placeholder="Catatan admin (opsional)"></x-textarea-input>
            </x-form-group>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <x-button variant="danger" type="submit">Reject Withdrawal</x-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        // Bulk action checkbox handling
        const checkboxes = document.querySelectorAll('.withdrawal-checkbox');
        const bulkApproveBtn = document.getElementById('bulkApproveBtn');
        const selectedCount = document.getElementById('selectedCount');

        function updateBulkAction() {
            const checked = document.querySelectorAll('.withdrawal-checkbox:checked');
            selectedCount.textContent = `${checked.length} withdrawals selected`;
            bulkApproveBtn.disabled = checked.length === 0;

            // Update form with selected IDs
            const form = document.getElementById('bulkActionForm');
            checked.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'withdrawal_ids[]';
                input.value = checkbox.value;
                if (!form.querySelector(`input[value="${checkbox.value}"]`)) {
                    form.appendChild(input);
                }
            });
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkAction);
        });

        // Reject modal
        function showRejectModal(withdrawalId, userName) {
            document.getElementById('modalUserName').textContent = userName;
            document.getElementById('rejectForm').action = `/admin/withdrawals/${withdrawalId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-with-sidebar>

