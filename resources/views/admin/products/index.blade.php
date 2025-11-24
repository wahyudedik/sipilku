<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Manajemen Produk
            </h2>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari produk..." 
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
                <a href="{{ route('admin.products.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($products->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($products as $product)
                <x-card>
                    <div class="flex items-start space-x-4">
                        <!-- Preview Image -->
                        <div class="flex-shrink-0">
                            @if($product->preview_image)
                                <img src="{{ Storage::url($product->preview_image) }}" 
                                     alt="{{ $product->title }}"
                                     class="w-24 h-24 object-cover rounded-lg">
                            @else
                                <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Product Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $product->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Oleh: <span class="font-medium">{{ $product->user->name }}</span>
                                    </p>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ $product->short_description ?? Str::limit($product->description, 100) }}
                                    </p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        <span class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                            Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                        </span>
                                        @if($product->category)
                                            <span class="text-sm text-gray-500">{{ $product->category->name }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex flex-col items-end space-y-2">
                                    <x-badge :variant="match($product->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($product->status) }}
                                    </x-badge>
                                    @if($product->status === 'rejected' && $product->rejection_reason)
                                        <p class="text-xs text-red-600 dark:text-red-400 max-w-xs text-right">
                                            {{ Str::limit($product->rejection_reason, 50) }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $product->sales_count }} penjualan</span>
                                    <span>{{ $product->download_count }} download</span>
                                    <span>Dibuat: {{ $product->created_at->format('d M Y') }}</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.products.show', $product) }}" 
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        Detail
                                    </a>
                                    @if($product->status === 'pending')
                                        <form action="{{ route('admin.products.approve', $product) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Apakah Anda yakin ingin menyetujui produk ini?')">
                                            @csrf
                                            <button type="submit" class="text-green-600 hover:text-green-800 dark:text-green-400">
                                                Setujui
                                            </button>
                                        </form>
                                        <button onclick="showRejectModal({{ $product->id }}, '{{ $product->title }}')" 
                                                class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Tolak
                                        </button>
                                    @endif
                                    <form action="{{ route('admin.products.destroy', $product) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus permanen produk ini?')">
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
            {{ $products->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada produk</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada produk yang ditemukan.</p>
            </div>
        </x-card>
    @endif

    <!-- Reject Modal -->
    <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white dark:bg-gray-800">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">Tolak Produk</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mb-4" id="rejectProductTitle"></p>
                <form id="rejectForm" method="POST">
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
        function showRejectModal(productId, productTitle) {
            document.getElementById('rejectProductTitle').textContent = 'Produk: ' + productTitle;
            document.getElementById('rejectForm').action = '{{ route("admin.products.reject", ":id") }}'.replace(':id', productId);
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

