@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Manajemen Pabrik
            </h2>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.factories.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari pabrik..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'Semua Status',
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                        'suspended' => 'Suspended'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="factory_type" 
                    :options="['all' => 'Semua Tipe'] + $factoryTypes->pluck('name', 'uuid')->toArray()" 
                    value="{{ request('factory_type', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="category" 
                    :options="[
                        'all' => 'Semua',
                        'industri' => 'Industri',
                        'umkm' => 'UMKM'
                    ]" 
                    value="{{ request('category', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="is_verified" 
                    :options="[
                        '' => 'Semua',
                        '1' => 'Verified',
                        '0' => 'Unverified'
                    ]" 
                    value="{{ request('is_verified', '') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('admin.factories.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($factories->count() > 0)
        <!-- Bulk Actions -->
        <x-card class="mb-6">
            <form id="bulkActionForm" action="{{ route('admin.factories.bulk-action') }}" method="POST">
                @csrf
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <select name="action" id="bulkAction" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                            <option value="">Pilih Aksi</option>
                            <option value="approve">Setujui</option>
                            <option value="reject">Tolak</option>
                            <option value="suspend">Tangguhkan</option>
                            <option value="activate">Aktifkan</option>
                        </select>
                    </div>
                    <div id="rejectionReasonContainer" class="hidden flex-1 min-w-[200px]">
                        <input type="text" name="rejection_reason" placeholder="Alasan..." 
                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <x-button variant="primary" size="md" type="submit" id="bulkActionBtn" disabled>
                        Terapkan ke Item Terpilih
                    </x-button>
                    <span id="selectedCount" class="text-sm text-gray-600 dark:text-gray-400">0 item dipilih</span>
                </div>
            </form>
        </x-card>

        <div class="grid grid-cols-1 gap-4">
            @foreach($factories as $factory)
                <x-card>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 pt-1">
                            <input type="checkbox" name="factory_ids[]" value="{{ $factory->uuid }}" 
                                   class="factory-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </div>
                        @if($factory->logo)
                            <img src="{{ Storage::url($factory->logo) }}" alt="{{ $factory->name }}" class="w-20 h-20 object-cover rounded">
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    <a href="{{ route('admin.factories.show', $factory) }}" class="hover:text-primary-600">
                                        {{ $factory->name }}
                                    </a>
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <x-badge variant="secondary" size="sm">{{ $factory->factoryType->name ?? 'Factory' }}</x-badge>
                                    <x-badge :variant="match($factory->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        'suspended' => 'danger',
                                        default => 'default'
                                    }">{{ ucfirst($factory->status) }}</x-badge>
                                    @if($factory->is_verified)
                                        <x-badge variant="success">Verified</x-badge>
                                    @endif
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                Owner: {{ $factory->user->name }} ({{ $factory->user->email }})
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                Category: {{ ucfirst($factory->category) }}
                            </p>
                            @if($factory->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">
                                    {{ Str::limit($factory->description, 150) }}
                                </p>
                            @endif
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>{{ $factory->locations->count() }} Lokasi</span>
                                <span>{{ $factory->products->count() }} Produk</span>
                                <span>{{ $factory->total_reviews }} Review</span>
                            </div>
                            @if($factory->rejection_reason)
                                <p class="text-sm text-red-600 dark:text-red-400 mt-2">
                                    <strong>Alasan:</strong> {{ $factory->rejection_reason }}
                                </p>
                            @endif
                        </div>
                        <div class="flex flex-col space-y-2">
                            <a href="{{ route('admin.factories.show', $factory) }}">
                                <x-button variant="secondary" size="sm">Detail</x-button>
                            </a>
                            @if($factory->status === 'pending')
                                <form action="{{ route('admin.factories.approve', $factory) }}" method="POST" class="inline">
                                    @csrf
                                    <x-button variant="success" size="sm" type="submit">Approve</x-button>
                                </form>
                                <button type="button" onclick="showRejectModal('{{ $factory->uuid }}', '{{ $factory->name }}')" class="w-full px-3 py-1.5 text-sm bg-red-600 text-white rounded hover:bg-red-700">
                                    Reject
                                </button>
                            @elseif($factory->status === 'suspended')
                                <form action="{{ route('admin.factories.activate', $factory) }}" method="POST" class="inline">
                                    @csrf
                                    <x-button variant="success" size="sm" type="submit">Activate</x-button>
                                </form>
                            @endif
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $factories->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>Tidak ada pabrik ditemukan.</p>
            </div>
        </x-card>
    @endif

    <!-- Reject Modal -->
    <x-modal id="rejectModal" title="Reject Factory">
        <form id="rejectForm" method="POST">
            @csrf
            <x-form-group label="Factory Name" name="factory_name">
                <p id="modalFactoryName" class="text-gray-700 dark:text-gray-300"></p>
            </x-form-group>
            <x-form-group label="Rejection Reason" name="rejection_reason" required>
                <x-textarea-input name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan..."></x-textarea-input>
            </x-form-group>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <x-button variant="danger" type="submit">Reject Factory</x-button>
            </div>
        </form>
    </x-modal>

    @push('scripts')
    <script>
        // Bulk action checkbox handling
        const checkboxes = document.querySelectorAll('.factory-checkbox');
        const bulkActionBtn = document.getElementById('bulkActionBtn');
        const selectedCount = document.getElementById('selectedCount');
        const bulkAction = document.getElementById('bulkAction');
        const rejectionReasonContainer = document.getElementById('rejectionReasonContainer');

        function updateBulkAction() {
            const checked = document.querySelectorAll('.factory-checkbox:checked');
            selectedCount.textContent = `${checked.length} item dipilih`;
            bulkActionBtn.disabled = checked.length === 0;

            if (bulkAction.value === 'reject' || bulkAction.value === 'suspend') {
                rejectionReasonContainer.classList.remove('hidden');
            } else {
                rejectionReasonContainer.classList.add('hidden');
            }
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkAction);
        });

        bulkAction.addEventListener('change', updateBulkAction);

        function showRejectModal(factoryId, factoryName) {
            document.getElementById('modalFactoryName').textContent = factoryName;
            document.getElementById('rejectForm').action = `/admin/factories/${factoryId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-with-sidebar>

