@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Toko
            </h2>
            <a href="{{ route('admin.stores.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-start space-x-4">
                        @if($store->logo)
                            <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-20 h-20 object-cover rounded">
                        @endif
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $store->name }}</h1>
                            <div class="mt-2 flex items-center space-x-4">
                                <x-badge :variant="match($store->status) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    'suspended' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($store->status) }}
                                </x-badge>
                                @if($store->is_verified)
                                    <x-badge variant="success">Verified</x-badge>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                Owner: <span class="font-medium">{{ $store->user->name }}</span> ({{ $store->user->email }})
                            </p>
                        </div>
                    </div>
                </div>

                @if($store->banner)
                    <div class="mb-4">
                        <img src="{{ Storage::url($store->banner) }}" alt="{{ $store->name }}" class="w-full h-48 object-cover rounded-lg">
                    </div>
                @endif

                @if($store->description)
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($store->description)) !!}
                    </div>
                @endif

                <div class="grid grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Phone</p>
                        <p class="font-semibold">{{ $store->phone ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                        <p class="font-semibold">{{ $store->email ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Website</p>
                        <p class="font-semibold">{{ $store->website ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Business License</p>
                        <p class="font-semibold">{{ $store->business_license ?? '-' }}</p>
                    </div>
                </div>
            </x-card>

            @if($store->locations->count() > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Lokasi Toko</h3>
                    </x-slot>
                    <div class="space-y-4">
                        @foreach($store->locations as $location)
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <h4 class="font-semibold">{{ $location->name ?? 'Lokasi Utama' }}</h4>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $location->full_address }}</p>
                                @if($location->hasCoordinates())
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $location->latitude }}, {{ $location->longitude }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            @if($store->status === 'rejected' && $store->rejection_reason)
                <x-alert type="error">
                    <strong>Alasan Penolakan:</strong> {{ $store->rejection_reason }}
                </x-alert>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            @if($store->status === 'pending')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Aksi</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <form action="{{ route('admin.stores.approve', $store) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui toko ini?')">
                            @csrf
                            <x-button variant="success" size="md" type="submit" class="w-full">Approve</x-button>
                        </form>
                        <button type="button" onclick="showRejectModal()" class="w-full px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                            Reject
                        </button>
                    </div>
                </x-card>
            @elseif($store->status === 'suspended')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Aksi</h3>
                    </x-slot>
                    <form action="{{ route('admin.stores.activate', $store) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin mengaktifkan toko ini?')">
                        @csrf
                        <x-button variant="success" size="md" type="submit" class="w-full">Activate</x-button>
                    </form>
                </x-card>
            @endif

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Statistik</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Produk</span>
                        <span class="font-semibold">{{ $store->products->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Lokasi</span>
                        <span class="font-semibold">{{ $store->locations->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Review</span>
                        <span class="font-semibold">{{ $store->total_reviews }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Rating</span>
                        <span class="font-semibold">{{ $store->rating }}/5</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Orders</span>
                        <span class="font-semibold">{{ $store->total_orders }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    <!-- Reject Modal -->
    <x-modal id="rejectModal" title="Reject Store">
        <form action="{{ route('admin.stores.reject', $store) }}" method="POST">
            @csrf
            <x-form-group label="Rejection Reason" name="rejection_reason" required>
                <x-textarea-input name="rejection_reason" rows="4" required placeholder="Masukkan alasan penolakan..."></x-textarea-input>
            </x-form-group>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="document.getElementById('rejectModal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                    Cancel
                </button>
                <x-button variant="danger" type="submit">Reject Store</x-button>
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

