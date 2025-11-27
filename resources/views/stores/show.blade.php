@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('stores.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Toko</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-gray-100">{{ $store->name }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Store Header -->
            <x-card class="mb-6">
                <div class="flex items-start space-x-6">
                    @if($store->logo)
                        <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-32 h-32 object-cover rounded-lg">
                    @else
                        <div class="w-32 h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1">
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ $store->name }}
                        </h1>
                        @if($store->description)
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $store->description }}</p>
                        @endif
                        <div class="flex items-center space-x-6 text-sm">
                            @if($store->rating > 0)
                                <div class="flex items-center">
                                    <span class="text-yellow-400 text-lg">★</span>
                                    <span class="ml-1 font-medium">{{ $store->rating }}/5</span>
                                    <span class="text-gray-500 ml-1">({{ $store->total_reviews }} reviews)</span>
                                </div>
                            @endif
                            <span>{{ $store->products->count() }} produk</span>
                            @if($store->primaryLocation->first())
                                <span>{{ $store->primaryLocation->first()->city }}, {{ $store->primaryLocation->first()->province }}</span>
                            @endif
                        </div>
                        @if($store->phone || $store->email || $store->website)
                            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                @if($store->phone)
                                    <span class="text-gray-600 dark:text-gray-400">📞 {{ $store->phone }}</span>
                                @endif
                                @if($store->email)
                                    <span class="text-gray-600 dark:text-gray-400">✉️ {{ $store->email }}</span>
                                @endif
                                @if($store->website)
                                    <a href="{{ $store->website }}" target="_blank" class="text-primary-600 hover:underline">
                                        🌐 Website
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>

            @if($store->banner)
                <x-card class="mb-6">
                    <img src="{{ Storage::url($store->banner) }}" alt="{{ $store->name }}" class="w-full h-64 object-cover rounded-lg">
                </x-card>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Store Gallery -->
                    @if($store->banner || ($store->logo))
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Galeri Toko</h3>
                            </x-slot>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="gallery">
                                @if($store->banner)
                                    <div class="col-span-2 md:col-span-2">
                                        <img src="{{ Storage::url($store->banner) }}" 
                                             alt="{{ $store->name }}" 
                                             class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition"
                                             onclick="openGallery(0)">
                                    </div>
                                @endif
                                @if($store->logo)
                                    <div>
                                        <img src="{{ Storage::url($store->logo) }}" 
                                             alt="{{ $store->name }} Logo" 
                                             class="w-full h-48 object-cover rounded-lg cursor-pointer hover:opacity-90 transition"
                                             onclick="openGallery({{ $store->banner ? 1 : 0 }})">
                                    </div>
                                @endif
                            </div>
                        </x-card>
                    @endif

                    <!-- Products Catalog -->
                    <x-card>
                        <x-slot name="header">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Katalog Produk ({{ $products->total() }})</h3>
                            </div>
                        </x-slot>

                        <!-- Product Filters -->
                        <form method="GET" action="{{ route('stores.show', $store) }}" class="mb-4 space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <x-text-input 
                                        name="product_search" 
                                        value="{{ request('product_search') }}" 
                                        placeholder="Cari produk..." 
                                        class="w-full" />
                                </div>
                                <div>
                                    <select name="category" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="">Semua Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->uuid }}" {{ request('category') === $category->uuid ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <select name="product_sort" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="latest" {{ request('product_sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                                        <option value="price_low" {{ request('product_sort') === 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                        <option value="price_high" {{ request('product_sort') === 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                        <option value="name" {{ request('product_sort') === 'name' ? 'selected' : '' }}>Nama A-Z</option>
                                    </select>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <x-button variant="primary" size="sm" type="submit">Filter</x-button>
                                <a href="{{ route('stores.show', $store) }}">
                                    <x-button variant="secondary" size="sm" type="button">Reset</x-button>
                                </a>
                            </div>
                        </form>

                        @if($products->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($products as $product)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                                        @if($product->images && count($product->images) > 0)
                                            <img src="{{ Storage::url($product->images[0]) }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="w-full h-32 object-cover rounded-lg mb-3">
                                        @endif
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                            {{ $product->name }}
                                        </h4>
                                        @if($product->category)
                                            <x-badge variant="info" size="xs" class="mb-2">{{ $product->category->name }}</x-badge>
                                        @endif
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">
                                            {{ Str::limit($product->description, 80) }}
                                        </p>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                @if($product->hasDiscount())
                                                    <span class="font-bold text-primary-600 dark:text-primary-400">
                                                        Rp {{ number_format($product->discount_price, 0, ',', '.') }}
                                                    </span>
                                                    <span class="text-sm text-gray-500 line-through ml-2">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    <span class="font-bold text-primary-600 dark:text-primary-400">
                                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                                    </span>
                                                @endif
                                                <span class="text-xs text-gray-500">/ {{ $product->unit }}</span>
                                            </div>
                                            @if($product->stock !== null)
                                                <span class="text-xs {{ $product->stock > 10 ? 'text-green-600' : ($product->stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                                                    Stok: {{ $product->stock }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Pagination -->
                            <div class="mt-6">
                                {{ $products->links() }}
                            </div>
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                Tidak ada produk ditemukan.
                            </p>
                        @endif
                    </x-card>

                    <!-- Reviews & Ratings -->
                    <x-card>
                        <x-slot name="header">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Review & Rating</h3>
                                @if($store->rating > 0)
                                    <div class="text-right">
                                        <div class="text-3xl font-bold">{{ number_format($store->rating, 1) }}</div>
                                        <div class="flex items-center justify-end">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= round($store->rating) ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $store->total_reviews }} review</p>
                                    </div>
                                @endif
                            </div>
                        </x-slot>

                        @auth
                            @php
                                $userReview = \App\Models\StoreReview::where('store_id', $store->uuid)
                                    ->where('user_id', Auth::id())
                                    ->first();
                            @endphp
                            @if(!$userReview)
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('store-reviews.create', $store) }}" class="inline-block">
                                        <x-button variant="primary" size="sm">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                            </svg>
                                            Beri Review
                                        </x-button>
                                    </a>
                                </div>
                            @endif
                        @endauth

                        @if($reviews->count() > 0)
                            <!-- Rating Breakdown -->
                            <div class="mb-6 space-y-2">
                                @for($i = 5; $i >= 1; $i--)
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm w-12">{{ $i }} Bintang</span>
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                            <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $store->total_reviews > 0 ? ($ratingBreakdown[$i] / $store->total_reviews * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-500 w-12 text-right">{{ $ratingBreakdown[$i] }}</span>
                                    </div>
                                @endfor
                            </div>

                            <!-- Reviews List -->
                            <div class="space-y-4">
                                @foreach($reviews as $review)
                                    @php
                                        $isMarkedHelpful = Auth::check() ? $review->isMarkedHelpfulBy(Auth::id()) : false;
                                    @endphp
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0">
                                        <div class="flex items-start justify-between mb-2">
                                            <div class="flex-1">
                                                <p class="font-semibold text-gray-900 dark:text-gray-100">{{ $review->user->name }}</p>
                                                <div class="flex items-center mt-1 space-x-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                        </svg>
                                                    @endfor
                                                    @if($review->is_verified_purchase)
                                                        <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 text-xs rounded">Verified Purchase</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <span class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</span>
                                        </div>
                                        @if($review->comment)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ $review->comment }}</p>
                                        @endif
                                        <div class="flex items-center space-x-4 mt-3">
                                            @auth
                                                @if($review->user_id !== Auth::id())
                                                    <form action="{{ route('store-reviews.mark-helpful', [$store, $review]) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="text-sm text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400 flex items-center space-x-1 {{ $isMarkedHelpful ? 'text-primary-600 dark:text-primary-400' : '' }}">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                            </svg>
                                                            <span>Membantu ({{ $review->helpful_count }})</span>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endauth
                                            @guest
                                                <span class="text-sm text-gray-600 dark:text-gray-400 flex items-center space-x-1">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" />
                                                    </svg>
                                                    <span>Membantu ({{ $review->helpful_count }})</span>
                                                </span>
                                            @endguest
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Reviews Pagination -->
                            <div class="mt-6">
                                {{ $reviews->links() }}
                            </div>
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                Belum ada review untuk toko ini.
                            </p>
                        @endif
                    </x-card>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Contact Information -->
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Kontak</h3>
                        </x-slot>
                        <div class="space-y-3 text-sm">
                            @if($store->phone)
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Telepon</p>
                                        <a href="tel:{{ $store->phone }}" class="font-semibold text-primary-600 hover:underline">
                                            {{ $store->phone }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if($store->email)
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Email</p>
                                        <a href="mailto:{{ $store->email }}" class="font-semibold text-primary-600 hover:underline">
                                            {{ $store->email }}
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if($store->website)
                                <div class="flex items-start space-x-2">
                                    <svg class="w-5 h-5 text-gray-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                                    </svg>
                                    <div>
                                        <p class="text-gray-600 dark:text-gray-400">Website</p>
                                        <a href="{{ $store->website }}" target="_blank" class="font-semibold text-primary-600 hover:underline">
                                            Kunjungi Website
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </x-card>

                    <!-- Location & Operating Hours -->
                    @if($store->locations->count() > 0)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Lokasi & Jam Operasional</h3>
                            </x-slot>
                            <div class="space-y-4">
                                @foreach($store->locations as $location)
                                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0 last:pb-0">
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $location->name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $location->full_address }}</p>
                                        
                                        @if($location->phone)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                                📞 <a href="tel:{{ $location->phone }}" class="text-primary-600 hover:underline">{{ $location->phone }}</a>
                                            </p>
                                        @endif

                                        @if($location->operating_hours && count($location->operating_hours) > 0)
                                            <div class="mt-3">
                                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jam Operasional:</p>
                                                <div class="space-y-1 text-xs">
                                                    @foreach($location->operating_hours as $day => $hours)
                                                        <div class="flex justify-between">
                                                            <span class="text-gray-600 dark:text-gray-400">{{ ucfirst($day) }}:</span>
                                                            <span class="font-medium">{{ $hours['open'] ?? '09:00' }} - {{ $hours['close'] ?? '17:00' }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        @if($location->hasCoordinates())
                                            <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" 
                                               target="_blank" 
                                               class="text-primary-600 hover:underline text-xs mt-2 inline-block">
                                                📍 Buka di Google Maps
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif

                    <!-- Store Map -->
                    @if($store->locations->where('is_active', true)->filter(fn($loc) => $loc->hasCoordinates())->count() > 0)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Peta Lokasi</h3>
                            </x-slot>
                            <div id="store_map" class="w-full h-64 rounded-lg"></div>
                        </x-card>
                    @endif

                    <!-- Recommended Stores -->
                    @if($recommendations && $recommendations->count() > 0)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Toko Terdekat Lainnya</h3>
                            </x-slot>
                            <div class="space-y-3">
                                @foreach($recommendations as $recommendation)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:shadow-md transition">
                                        <div class="flex items-start space-x-3">
                                            @if($recommendation['store']->logo)
                                                <img src="{{ Storage::url($recommendation['store']->logo) }}" 
                                                     alt="{{ $recommendation['store']->name }}" 
                                                     class="w-16 h-16 object-cover rounded">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                    <a href="{{ route('stores.show', $recommendation['store']) }}" class="hover:text-primary-600">
                                                        {{ $recommendation['store']->name }}
                                                    </a>
                                                </h4>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                    {{ $recommendation['nearest_location']->full_address }}
                                                </p>
                                                <div class="flex items-center justify-between mt-2">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                                            {{ number_format($recommendation['distance'], 1) }} km
                                                        </span>
                                                        @if($recommendation['store']->rating > 0)
                                                            <div class="flex items-center">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    <svg class="w-3 h-3 {{ $i <= $recommendation['store']->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                    </svg>
                                                                @endfor
                                                                <span class="text-xs text-gray-500 ml-1">{{ $recommendation['store']->rating }}/5</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <span class="text-xs text-gray-500">
                                                        Skor: {{ number_format($recommendation['recommendation_score'], 1) }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif

                    <!-- Store Info -->
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Informasi Toko</h3>
                        </x-slot>
                        <div class="space-y-3 text-sm">
                            @if($store->business_license)
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">SIUP:</span>
                                    <span class="font-semibold">{{ $store->business_license }}</span>
                                </div>
                            @endif
                            <div>
                                <span class="text-gray-600 dark:text-gray-400">Total Orders:</span>
                                <span class="font-semibold">{{ $store->total_orders }}</span>
                            </div>
                            @if($store->is_verified)
                                <div>
                                    <x-badge variant="success">Verified Store</x-badge>
                                </div>
                            @endif
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    @if($store->locations->where('is_active', true)->filter(fn($loc) => $loc->hasCoordinates())->count() > 0)
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initStoreMap" async defer></script>
        <script>
            function initStoreMap() {
                const locations = @json($store->locations->where('is_active', true)->filter(fn($loc) => $loc->hasCoordinates())->map(function($loc) {
                    return [
                        'name' => $loc->name,
                        'address' => $loc->full_address,
                        'latitude' => $loc->latitude,
                        'longitude' => $loc->longitude,
                    ];
                }));

                if (locations.length === 0) return;

                let centerLat = parseFloat(locations[0].latitude);
                let centerLng = parseFloat(locations[0].longitude);

                if (locations.length > 1) {
                    const sumLat = locations.reduce((sum, loc) => sum + parseFloat(loc.latitude), 0);
                    const sumLng = locations.reduce((sum, loc) => sum + parseFloat(loc.longitude), 0);
                    centerLat = sumLat / locations.length;
                    centerLng = sumLng / locations.length;
                }

                const map = new google.maps.Map(document.getElementById('store_map'), {
                    center: { lat: centerLat, lng: centerLng },
                    zoom: locations.length === 1 ? 15 : 12,
                });

                const bounds = new google.maps.LatLngBounds();

                locations.forEach(location => {
                    const marker = new google.maps.Marker({
                        position: { 
                            lat: parseFloat(location.latitude), 
                            lng: parseFloat(location.longitude) 
                        },
                        map: map,
                        title: location.name,
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div class="p-2">
                                <h4 class="font-semibold">${location.name}</h4>
                                <p class="text-sm text-gray-600">${location.address}</p>
                                <a href="https://www.google.com/maps?q=${location.latitude},${location.longitude}" 
                                   target="_blank" 
                                   class="text-primary-600 hover:underline text-xs mt-1 inline-block">
                                    Buka di Google Maps
                                </a>
                            </div>
                        `
                    });

                    marker.addListener('click', function() {
                        infoWindow.open(map, marker);
                    });

                    bounds.extend(marker.getPosition());
                });

                if (locations.length > 1) {
                    map.fitBounds(bounds);
                }
            }
        </script>
    @endif

    <!-- Gallery Viewer -->
    <script>
        const galleryImages = [
            @if($store->banner) '{{ Storage::url($store->banner) }}', @endif
            @if($store->logo) '{{ Storage::url($store->logo) }}', @endif
        ];

        function openGallery(index) {
            // Simple lightbox implementation
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black bg-opacity-90 z-50 flex items-center justify-center';
            overlay.onclick = function() {
                document.body.removeChild(overlay);
            };

            const img = document.createElement('img');
            img.src = galleryImages[index];
            img.className = 'max-w-full max-h-full object-contain';
            img.onclick = function(e) {
                e.stopPropagation();
            };

            const closeBtn = document.createElement('button');
            closeBtn.innerHTML = '✕';
            closeBtn.className = 'absolute top-4 right-4 text-white text-3xl hover:text-gray-300';
            closeBtn.onclick = function() {
                document.body.removeChild(overlay);
            };

            overlay.appendChild(img);
            overlay.appendChild(closeBtn);
            document.body.appendChild(overlay);
        }
    </script>
    @endpush
</x-app-layout>

