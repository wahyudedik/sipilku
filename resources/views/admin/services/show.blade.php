<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Jasa
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.services.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $service->title }}</h1>
                        <div class="mt-2 flex items-center space-x-4">
                            <x-badge :variant="match($service->status) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'default'
                            }">
                                {{ ucfirst($service->status) }}
                            </x-badge>
                            @if($service->category)
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $service->category->name }}</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Oleh: <span class="font-medium">{{ $service->user->name }}</span> ({{ $service->user->email }})
                        </p>
                    </div>
                </div>

                @if($service->preview_image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($service->preview_image) }}" 
                             alt="{{ $service->title }}"
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif

                @if($service->short_description)
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">{{ $service->short_description }}</p>
                @endif

                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($service->description)) !!}
                </div>
            </x-card>

            <!-- Package Pricing -->
            @if($service->package_prices && count($service->package_prices) > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Paket Harga</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($service->package_prices as $package)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $package['name'] }}
                                </h4>
                                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                                    Rp {{ number_format($package['price'], 0, ',', '.') }}
                                </p>
                                @if(isset($package['description']))
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $package['description'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif

            @if($service->gallery_images && count($service->gallery_images) > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Galeri</h3>
                    </x-slot>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($service->gallery_images as $image)
                            <img src="{{ Storage::url($image) }}" alt="Gallery" class="w-full h-32 object-cover rounded-lg">
                        @endforeach
                    </div>
                </x-card>
            @endif

            @if($service->status === 'rejected' && $service->rejection_reason)
                <x-alert type="error">
                    <strong>Alasan Penolakan:</strong> {{ $service->rejection_reason }}
                </x-alert>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            @if($service->status === 'pending')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Aksi</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <form action="{{ route('admin.services.approve', $service) }}" 
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui jasa ini?')">
                            @csrf
                            <x-button variant="success" size="md" type="submit" class="w-full">
                                ✓ Setujui Jasa
                            </x-button>
                        </form>
                        <button onclick="showRejectModal()" 
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                            ✗ Tolak Jasa
                        </button>
                    </div>
                </x-card>
            @endif

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Statistik</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Pesanan Selesai</span>
                        <span class="font-medium">{{ $service->completed_orders }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Rating</span>
                        <span class="font-medium">{{ number_format($service->rating, 1) }} ⭐</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Review</span>
                        <span class="font-medium">{{ $service->review_count }}</span>
                    </div>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Tanggal</h3>
                </x-slot>
                <div class="space-y-2 text-sm">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400">Dibuat</p>
                        <p class="font-medium">{{ $service->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($service->approved_at)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Disetujui</p>
                            <p class="font-medium">{{ $service->approved_at->format('d M Y H:i') }}</p>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tolak Jasa</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Jasa: {{ $service->title }}</p>
                <form action="{{ route('admin.services.reject', $service) }}" method="POST">
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
        function showRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.querySelector('#rejectModal form').reset();
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

