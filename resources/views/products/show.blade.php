<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('products.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Produk</a>
            <span>/</span>
            @if($product->category)
                <a href="{{ route('products.index', ['category' => $product->category->slug]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                    {{ $product->category->name }}
                </a>
                <span>/</span>
            @endif
            <span class="text-gray-900 dark:text-gray-100">{{ $product->title }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Images -->
                <div class="lg:col-span-1">
                    <!-- Main Preview Image -->
                    <div class="mb-4">
                        <img id="mainImage" 
                             src="{{ $product->preview_image ? Storage::url($product->preview_image) : asset('images/placeholder.png') }}" 
                             alt="{{ $product->title }}"
                             class="w-full h-96 object-cover rounded-lg cursor-pointer"
                             onclick="openImageModal(this.src)">
                    </div>

                    <!-- Gallery Thumbnails -->
                    @if($product->gallery_images && count($product->gallery_images) > 0)
                        <div class="grid grid-cols-4 gap-2">
                            <div class="relative">
                                <img src="{{ Storage::url($product->preview_image) }}" 
                                     alt="Preview"
                                     class="w-full h-20 object-cover rounded cursor-pointer border-2 border-primary-500"
                                     onclick="changeMainImage('{{ Storage::url($product->preview_image) }}')">
                            </div>
                            @foreach($product->gallery_images as $image)
                                <div class="relative">
                                    <img src="{{ Storage::url($image) }}" 
                                         alt="Gallery"
                                         class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition"
                                         onclick="changeMainImage('{{ Storage::url($image) }}')">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Right Column - Product Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $product->title }}
                        </h1>

                        <div class="flex items-center space-x-4 mb-4">
                            @if($product->category)
                                <x-badge variant="default" size="md">
                                    {{ $product->category->name }}
                                </x-badge>
                            @endif
                            @if($product->rating > 0)
                                <div class="flex items-center space-x-1">
                                    <span class="text-yellow-500 text-lg">⭐</span>
                                    <span class="font-medium">{{ number_format($product->rating, 1) }}</span>
                                    <span class="text-gray-500">({{ $product->review_count }} review)</span>
                                </div>
                            @endif
                            <span class="text-gray-500">{{ $product->sales_count }} penjualan</span>
                        </div>

                        <!-- Price -->
                        <div class="mb-6">
                            <div class="flex items-baseline space-x-3">
                                <span class="text-4xl font-bold text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                </span>
                                @if($product->discount_price)
                                    <span class="text-xl text-gray-500 line-through">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                    <x-badge variant="danger" size="md">
                                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% OFF
                                    </x-badge>
                                @endif
                            </div>
                        </div>

                        <!-- Short Description -->
                        @if($product->short_description)
                            <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                                {{ $product->short_description }}
                            </p>
                        @endif

                        <!-- Seller Info -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Dijual oleh</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $product->user->name }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-4">
                            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1">
                                @csrf
                                <x-button variant="primary" size="lg" type="submit" class="w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    Tambah ke Keranjang
                                </x-button>
                            </form>
                            <button class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Description -->
                    <x-card class="mt-6">
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Deskripsi</h3>
                        </x-slot>
                        <div class="prose dark:prose-invert max-w-none">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </x-card>

                    <!-- Reviews -->
                    <x-card class="mt-6">
                        <x-slot name="header">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium">Review & Rating</h3>
                                @if($product->rating > 0)
                                    <div class="text-right">
                                        <div class="text-3xl font-bold">{{ number_format($product->rating, 1) }}</div>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= round($product->rating) ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $product->review_count }} review</p>
                                    </div>
                                @endif
                            </div>
                        </x-slot>

                        @if($product->reviews->where('is_approved', true)->count() > 0)
                            <div class="space-y-4">
                                @foreach($product->reviews->where('is_approved', true) as $review)
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div>
                                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $review->user->name }}</p>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $review->created_at->format('d M Y') }}</p>
                                            </div>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                                        @endif
                                        @if($review->images && count($review->images) > 0)
                                            <div class="mt-2 flex space-x-2">
                                                @foreach($review->images as $image)
                                                    <img src="{{ Storage::url($image) }}" alt="Review image" class="w-16 h-16 object-cover rounded">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada review untuk produk ini.</p>
                        @endif
                    </x-card>

                    <!-- Related Products -->
                    @if($relatedProducts->count() > 0)
                        <x-card class="mt-6">
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Produk Terkait</h3>
                            </x-slot>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($relatedProducts as $related)
                                    <a href="{{ route('products.show', $related) }}" class="block">
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden hover:shadow-md transition">
                                            @if($related->preview_image)
                                                <img src="{{ Storage::url($related->preview_image) }}" 
                                                     alt="{{ $related->title }}"
                                                     class="w-full h-32 object-cover">
                                            @endif
                                            <div class="p-3">
                                                <h4 class="font-medium text-sm text-gray-900 dark:text-gray-100 line-clamp-2 mb-2">
                                                    {{ $related->title }}
                                                </h4>
                                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                    Rp {{ number_format($related->final_price, 0, ',', '.') }}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </x-card>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closeImageModal()">
        <div class="max-w-4xl w-full">
            <img id="modalImage" src="" alt="Preview" class="w-full h-auto rounded-lg">
            <button onclick="closeImageModal()" class="absolute top-4 right-4 text-white hover:text-gray-300">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    @push('scripts')
    <script>
        function changeMainImage(src) {
            document.getElementById('mainImage').src = src;
            // Update active thumbnail
            document.querySelectorAll('.grid img').forEach(img => {
                img.classList.remove('border-2', 'border-primary-500');
            });
            event.target.classList.add('border-2', 'border-primary-500');
        }

        function openImageModal(src) {
            document.getElementById('modalImage').src = src;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>

