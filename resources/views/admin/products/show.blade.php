<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Produk
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.products.index') }}">
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
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $product->title }}</h1>
                        <div class="mt-2 flex items-center space-x-4">
                            <x-badge :variant="match($product->status) {
                                'approved' => 'success',
                                'pending' => 'warning',
                                'rejected' => 'danger',
                                default => 'default'
                            }">
                                {{ ucfirst($product->status) }}
                            </x-badge>
                            @if($product->category)
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name }}</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            Oleh: <span class="font-medium">{{ $product->user->name }}</span> ({{ $product->user->email }})
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                            Rp {{ number_format($product->final_price, 0, ',', '.') }}
                        </div>
                        @if($product->discount_price)
                            <div class="text-sm text-gray-500 line-through">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>
                        @endif
                    </div>
                </div>

                @if($product->preview_image)
                    <div class="mb-4">
                        <img src="{{ Storage::url($product->preview_image) }}" 
                             alt="{{ $product->title }}"
                             class="w-full h-64 object-cover rounded-lg">
                    </div>
                @endif

                @if($product->short_description)
                    <p class="text-lg text-gray-700 dark:text-gray-300 mb-4">{{ $product->short_description }}</p>
                @endif

                <div class="prose dark:prose-invert max-w-none">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </x-card>

            @if($product->gallery_images && count($product->gallery_images) > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Galeri</h3>
                    </x-slot>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        @foreach($product->gallery_images as $image)
                            <img src="{{ Storage::url($image) }}" alt="Gallery" class="w-full h-32 object-cover rounded-lg">
                        @endforeach
                    </div>
                </x-card>
            @endif

            @if($product->status === 'rejected' && $product->rejection_reason)
                <x-alert type="error">
                    <strong>Alasan Penolakan:</strong> {{ $product->rejection_reason }}
                </x-alert>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            @if($product->status === 'pending')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Aksi</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <form action="{{ route('admin.products.approve', $product) }}" 
                              method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui produk ini?')">
                            @csrf
                            <x-button variant="success" size="md" type="submit" class="w-full">
                                ✓ Setujui Produk
                            </x-button>
                        </form>
                        <button onclick="showRejectModal()" 
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md">
                            ✗ Tolak Produk
                        </button>
                    </div>
                </x-card>
            @endif

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Informasi File</h3>
                </x-slot>
                @if($product->file_path)
                    <div class="space-y-2">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Nama File</p>
                            <p class="font-medium">{{ $product->file_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Ukuran</p>
                            <p class="font-medium">{{ number_format($product->file_size / 1024 / 1024, 2) }} MB</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Tipe</p>
                            <p class="font-medium">{{ $product->file_type }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-sm text-gray-500">File belum diupload</p>
                @endif
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Statistik</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Penjualan</span>
                        <span class="font-medium">{{ $product->sales_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Download</span>
                        <span class="font-medium">{{ $product->download_count }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Rating</span>
                        <span class="font-medium">{{ number_format($product->rating, 1) }} ⭐</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Review</span>
                        <span class="font-medium">{{ $product->review_count }}</span>
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
                        <p class="font-medium">{{ $product->created_at->format('d M Y H:i') }}</p>
                    </div>
                    @if($product->approved_at)
                        <div>
                            <p class="text-gray-500 dark:text-gray-400">Disetujui</p>
                            <p class="font-medium">{{ $product->approved_at->format('d M Y H:i') }}</p>
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
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tolak Produk</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Produk: {{ $product->title }}</p>
                <form action="{{ route('admin.products.reject', $product) }}" method="POST">
                    @csrf
                    <x-form-group label="Alasan Penolakan" name="rejection_reason" required>
                        <x-textarea-input name="rejection_reason" rows="4" placeholder="Masukkan alasan penolakan produk..."></x-textarea-input>
                    </x-form-group>
                    <div class="flex justify-end space-x-3 mt-4">
                        <button type="button" onclick="closeRejectModal()" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                            Batal
                        </button>
                        <x-button variant="danger" size="md" type="submit">Tolak Produk</x-button>
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

