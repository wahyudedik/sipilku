<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('services.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Jasa</a>
            <span>/</span>
            @if($service->category)
                <a href="{{ route('services.index', ['category' => $service->category->slug]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                    {{ $service->category->name }}
                </a>
                <span>/</span>
            @endif
            <span class="text-gray-900 dark:text-gray-100">{{ $service->title }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column - Images & Gallery -->
                <div class="lg:col-span-1">
                    <!-- Main Preview Image -->
                    <div class="mb-4">
                        <img id="mainImage" 
                             src="{{ $service->preview_image ? Storage::url($service->preview_image) : asset('images/placeholder.png') }}" 
                             alt="{{ $service->title }}"
                             class="w-full h-96 object-cover rounded-lg cursor-pointer"
                             onclick="openImageModal(this.src)">
                    </div>

                    <!-- Gallery Thumbnails -->
                    @if($service->gallery_images && count($service->gallery_images) > 0)
                        <div class="grid grid-cols-4 gap-2">
                            <div class="relative">
                                <img src="{{ Storage::url($service->preview_image) }}" 
                                     alt="Preview"
                                     class="w-full h-20 object-cover rounded cursor-pointer border-2 border-primary-500"
                                     onclick="changeMainImage('{{ Storage::url($service->preview_image) }}')">
                            </div>
                            @foreach($service->gallery_images as $image)
                                <div class="relative">
                                    <img src="{{ Storage::url($image) }}" 
                                         alt="Gallery"
                                         class="w-full h-20 object-cover rounded cursor-pointer hover:opacity-75 transition"
                                         onclick="changeMainImage('{{ Storage::url($image) }}')">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Portfolio -->
                    @if($service->portfolio && count($service->portfolio) > 0)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Portfolio</h3>
                            <div class="grid grid-cols-2 gap-2">
                                @foreach($service->portfolio as $item)
                                    @if(isset($item['image']))
                                        <div class="relative group">
                                            <img src="{{ Storage::url($item['image']) }}" 
                                                 alt="{{ $item['title'] ?? 'Portfolio' }}"
                                                 class="w-full h-32 object-cover rounded cursor-pointer"
                                                 onclick="openPortfolioModal('{{ Storage::url($item['image']) }}', '{{ $item['title'] ?? '' }}', '{{ $item['description'] ?? '' }}')">
                                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition flex items-center justify-center">
                                                <span class="text-white opacity-0 group-hover:opacity-100 text-sm font-medium">
                                                    {{ $item['title'] ?? 'Lihat Detail' }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Service Info -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-6">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $service->title }}
                        </h1>

                        <div class="flex items-center space-x-4 mb-4">
                            @if($service->category)
                                <x-badge variant="default" size="md">
                                    {{ $service->category->name }}
                                </x-badge>
                            @endif
                            @if($service->rating > 0)
                                <div class="flex items-center space-x-1">
                                    <span class="text-yellow-500 text-lg">⭐</span>
                                    <span class="font-medium">{{ number_format($service->rating, 1) }}</span>
                                    <span class="text-gray-500">({{ $service->review_count }} review)</span>
                                </div>
                            @endif
                            <span class="text-gray-500">{{ $service->completed_orders }} pesanan selesai</span>
                        </div>

                        <!-- Package Pricing -->
                        @if($service->package_prices && count($service->package_prices) > 0)
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Paket Harga</h3>
                                <div class="space-y-3">
                                    @foreach($service->package_prices as $package)
                                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:border-primary-500 transition">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-1">
                                                        {{ $package['name'] }}
                                                    </h4>
                                                    @if(isset($package['description']))
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                            {{ $package['description'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <div class="text-right">
                                                    <span class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                                        Rp {{ number_format($package['price'], 0, ',', '.') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="mb-6">
                                <p class="text-gray-500 dark:text-gray-400">Hubungi seller untuk mendapatkan penawaran harga.</p>
                            </div>
                        @endif

                        <!-- Short Description -->
                        @if($service->short_description)
                            <p class="text-lg text-gray-700 dark:text-gray-300 mb-6">
                                {{ $service->short_description }}
                            </p>
                        @endif

                        <!-- Seller Info -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Disediakan oleh</p>
                            <p class="font-medium text-gray-900 dark:text-gray-100">{{ $service->user->name }}</p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-4">
                            @auth
                                @if(auth()->id() !== $service->user_id)
                                    <a href="{{ route('quote-requests.create', $service) }}" class="flex-1">
                                        <x-button variant="primary" size="lg" class="w-full">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                            </svg>
                                            Request Quote
                                        </x-button>
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="flex-1">
                                    <x-button variant="primary" size="lg" class="w-full">
                                        Login untuk Request Quote
                                    </x-button>
                                </a>
                            @endauth
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
                            {!! nl2br(e($service->description)) !!}
                        </div>
                    </x-card>

                    <!-- Reviews -->
                    <x-card class="mt-6">
                        <x-slot name="header">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium">Review & Rating</h3>
                                @if($service->rating > 0)
                                    <div class="text-right">
                                        <div class="text-3xl font-bold">{{ number_format($service->rating, 1) }}</div>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= round($service->rating) ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $service->review_count }} review</p>
                                    </div>
                                @endif
                            </div>
                        </x-slot>

                        @if($service->reviews->where('is_approved', true)->count() > 0)
                            <div class="space-y-4">
                                @foreach($service->reviews->where('is_approved', true) as $review)
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
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada review untuk jasa ini.</p>
                        @endif
                    </x-card>

                    <!-- Related Services -->
                    @if($relatedServices->count() > 0)
                        <x-card class="mt-6">
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Jasa Terkait</h3>
                            </x-slot>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                @foreach($relatedServices as $related)
                                    <a href="{{ route('services.show', $related) }}" class="block">
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
                                                @if($related->package_prices && count($related->package_prices) > 0)
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Mulai dari:</p>
                                                    <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                        Rp {{ number_format(min(array_column($related->package_prices, 'price')), 0, ',', '.') }}
                                                    </p>
                                                @endif
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

    <!-- Portfolio Modal -->
    <div id="portfolioModal" class="hidden fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4" onclick="closePortfolioModal()">
        <div class="max-w-4xl w-full bg-white dark:bg-gray-800 rounded-lg p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 id="portfolioTitle" class="text-xl font-bold text-gray-900 dark:text-gray-100"></h3>
                    <p id="portfolioDescription" class="text-gray-600 dark:text-gray-400 mt-2"></p>
                </div>
                <button onclick="closePortfolioModal()" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <img id="portfolioImage" src="" alt="Portfolio" class="w-full h-auto rounded-lg">
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

        function openPortfolioModal(imageSrc, title, description) {
            document.getElementById('portfolioImage').src = imageSrc;
            document.getElementById('portfolioTitle').textContent = title || 'Portfolio';
            document.getElementById('portfolioDescription').textContent = description || '';
            document.getElementById('portfolioModal').classList.remove('hidden');
        }

        function closePortfolioModal() {
            document.getElementById('portfolioModal').classList.add('hidden');
        }

        // Close modals on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
                closePortfolioModal();
            }
        });
    </script>
    @endpush
</x-app-layout>

