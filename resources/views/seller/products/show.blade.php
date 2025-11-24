<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Produk
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('seller.products.edit', $product) }}">
                    <x-button variant="primary" size="sm">Edit</x-button>
                </a>
                <a href="{{ route('seller.products.index') }}">
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
</x-app-with-sidebar>

