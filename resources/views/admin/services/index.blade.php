@php
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Manajemen Jasa
            </h2>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.services.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari jasa..." 
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
                        'draft' => 'Draft'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('admin.services.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($services->count() > 0)
        <!-- Bulk Actions -->
        <x-card class="mb-6">
            <form id="bulkActionForm" action="{{ route('admin.services.bulk-action') }}" method="POST">
                @csrf
                <div class="flex flex-wrap items-center gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <select name="action" id="bulkAction" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                            <option value="">Pilih Aksi</option>
                            <option value="approve">Setujui</option>
                            <option value="reject">Tolak</option>
                            <option value="delete">Hapus</option>
                        </select>
                    </div>
                    <div id="rejectionReasonContainer" class="hidden flex-1 min-w-[200px]">
                        <input type="text" name="rejection_reason" placeholder="Alasan penolakan..." 
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
            @foreach($services as $service)
                <x-card>
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0 pt-1">
                            <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" 
                                   class="service-checkbox rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </div>
                        <!-- Preview Image -->
                        <div class="flex-shrink-0">
                            @if($service->preview_image)
                                <img src="{{ Storage::url($service->preview_image) }}" 
                                     alt="{{ $service->title }}"
                                     class="w-24 h-24 object-cover rounded-lg">
                            @else
                                <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Service Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $service->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Oleh: <span class="font-medium">{{ $service->user->name }}</span>
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ $service->short_description ?? Str::limit($service->description, 100) }}
                                    </p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        @if($service->package_prices && count($service->package_prices) > 0)
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                Mulai dari: <span class="font-bold text-primary-600 dark:text-primary-400">
                                                    Rp {{ number_format(min(array_column($service->package_prices, 'price')), 0, ',', '.') }}
                                                </span>
                                            </span>
                                        @endif
                                        @if($service->category)
                                            <span class="text-sm text-gray-500">{{ $service->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex flex-col items-end space-y-2">
                                    <x-badge :variant="match($service->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($service->status) }}
                                    </x-badge>
                                    @if($service->status === 'rejected' && $service->rejection_reason)
                                        <p class="text-xs text-red-600 dark:text-red-400 max-w-xs text-right">
                                            {{ Str::limit($service->rejection_reason, 50) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $service->completed_orders }} pesanan selesai</span>
                                    @if($service->rating > 0)
                                        <span>{{ number_format($service->rating, 1) }} ⭐</span>
                                    @endif
                                    <span>Dibuat: {{ $service->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.services.show', $service) }}" 
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        Detail
                                    </a>
                                    @if($service->status === 'pending')
                                        <form action="{{ route('admin.services.approve', $service) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui jasa ini?')">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400">
                                                Setujui
                                            </button>
                                        </form>
                                        <button onclick="showRejectModal({{ $service->id }}, '{{ $service->title }}')" 
                                                class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Tolak
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.services.destroy', $service) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen jasa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $services->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada jasa</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada jasa yang ditemukan.</p>
            </div>
        </x-card>
    @endif

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tolak Jasa</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="rejectServiceTitle"></p>
                <form id="rejectForm" method="POST">
                    @csrf
                    <x-form-group label="Alasan Penolakan" name="rejection_reason" required>
                        <x-textarea-input name="rejection_reason" rows="4" placeholder="Masukkan alasan penolakan jasa..."></x-textarea-input>
                    </x-form-group>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <x-button variant="danger" size="md" type="submit">Tolak Jasa</x-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Bulk Actions
        const checkboxes = document.querySelectorAll('.service-checkbox');
        const bulkAction = document.getElementById('bulkAction');
        const bulkActionBtn = document.getElementById('bulkActionBtn');
        const selectedCount = document.getElementById('selectedCount');
        const rejectionReasonContainer = document.getElementById('rejectionReasonContainer');

        function updateBulkAction() {
            const checked = document.querySelectorAll('.service-checkbox:checked');
            const count = checked.length;
            selectedCount.textContent = count + ' item dipilih';
            bulkActionBtn.disabled = count === 0 || !bulkAction.value;

            if (bulkAction.value === 'reject') {
                rejectionReasonContainer.classList.remove('hidden');
                if (count > 0) {
                    document.querySelector('input[name="rejection_reason"]').required = true;
                }
            } else {
                rejectionReasonContainer.classList.add('hidden');
                document.querySelector('input[name="rejection_reason"]').required = false;
            }
        }

        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkAction);
        });

        bulkAction.addEventListener('change', updateBulkAction);

        document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
            const checked = document.querySelectorAll('.service-checkbox:checked');
            if (checked.length === 0) {
                e.preventDefault();
                alert('Pilih minimal 1 jasa');
                return false;
            }

            checked.forEach(checkbox => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'service_ids[]';
                input.value = checkbox.value;
                this.appendChild(input);
            });

            const action = bulkAction.value;
            let confirmMsg = '';
            if (action === 'approve') {
                confirmMsg = `Setujui ${checked.length} jasa?`;
            } else if (action === 'reject') {
                confirmMsg = `Tolak ${checked.length} jasa?`;
            } else if (action === 'delete') {
                confirmMsg = `Hapus permanen ${checked.length} jasa? Tindakan ini tidak dapat dibatalkan!`;
            }

            if (!confirm(confirmMsg)) {
                e.preventDefault();
                return false;
            }
        });

        function showRejectModal(serviceId, serviceTitle) {
            document.getElementById('rejectServiceTitle').textContent = 'Jasa: ' + serviceTitle;
            document.getElementById('rejectForm').action = '{{ route("admin.services.reject", ":id") }}'.replace(':id', serviceId);
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejectForm').reset();
        }

        // Close modal when clicking outside
        document.getElementById('rejectModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRejectModal();
            }
        });
    </script>
    @endpush
</x-app-with-sidebar>

