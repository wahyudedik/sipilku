<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Toko Bangunan
            </h2>
            <a href="{{ route('stores.find-nearest') }}">
                <x-button variant="primary" size="sm">Cari Toko Terdekat</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('stores.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-text-input 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari toko..." 
                                class="w-full" />
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <select name="city" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Kota</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                                        {{ $city }}
                                    </option>
                                @endforeach
                            </select>
                            <select name="province" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Provinsi</option>
                                @foreach($provinces as $province)
                                    <option value="{{ $province }}" {{ request('province') === $province ? 'selected' : '' }}>
                                        {{ $province }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rating Minimum</label>
                            <select name="min_rating" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Rating</option>
                                <option value="5" {{ request('min_rating') == '5' ? 'selected' : '' }}>5 Bintang</option>
                                <option value="4" {{ request('min_rating') == '4' ? 'selected' : '' }}>4+ Bintang</option>
                                <option value="3" {{ request('min_rating') == '3' ? 'selected' : '' }}>3+ Bintang</option>
                                <option value="2" {{ request('min_rating') == '2' ? 'selected' : '' }}>2+ Bintang</option>
                                <option value="1" {{ request('min_rating') == '1' ? 'selected' : '' }}>1+ Bintang</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutkan</label>
                            <select name="sort" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                                <option value="rating" {{ request('sort') === 'rating' ? 'selected' : '' }}>Rating Tertinggi</option>
                                <option value="reviews" {{ request('sort') === 'reviews' ? 'selected' : '' }}>Review Terbanyak</option>
                                <option value="products" {{ request('sort') === 'products' ? 'selected' : '' }}>Produk Terbanyak</option>
                                <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Nama A-Z</option>
                            </select>
                        </div>
                        <div class="flex items-end gap-2">
                            <x-button variant="primary" size="md" type="submit" class="flex-1">Cari</x-button>
                            <a href="{{ route('stores.index') }}">
                                <x-button variant="secondary" size="md" type="button">Reset</x-button>
                            </a>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Location-based Recommendations (if location provided) -->
            @if(request()->has('latitude') && request()->has('longitude'))
                @php
                    $recommendationService = new \App\Services\StoreRecommendationService();
                    $locationRecommendations = $recommendationService->getRecommendations(
                        request('latitude'),
                        request('longitude'),
                        6,
                        50
                    );
                @endphp
                @if($locationRecommendations->count() > 0)
                    <x-card class="mb-6 bg-gradient-to-r from-primary-50 to-blue-50 dark:from-primary-900/20 dark:to-blue-900/20 border-primary-200 dark:border-primary-800">
                        <x-slot name="header">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="text-lg font-medium text-primary-900 dark:text-primary-100">Rekomendasi Toko Terdekat</h3>
                            </div>
                        </x-slot>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($locationRecommendations as $recommendation)
                                <div class="border border-primary-200 dark:border-primary-800 rounded-lg p-4 hover:shadow-md transition bg-white dark:bg-gray-800">
                                    <div class="flex items-start space-x-3">
                                        @if($recommendation['store']->logo)
                                            <img src="{{ Storage::url($recommendation['store']->logo) }}" 
                                                 alt="{{ $recommendation['store']->name }}" 
                                                 class="w-12 h-12 object-cover rounded">
                                        @endif
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-sm text-gray-900 dark:text-gray-100">
                                                <a href="{{ route('stores.show', $recommendation['store']) }}" class="hover:text-primary-600">
                                                    {{ $recommendation['store']->name }}
                                                </a>
                                            </h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                {{ number_format($recommendation['distance'], 1) }} km
                                            </p>
                                            @if($recommendation['store']->rating > 0)
                                                <div class="flex items-center mt-1">
                                                    <span class="text-yellow-400 text-xs">★</span>
                                                    <span class="text-xs text-gray-600 dark:text-gray-400 ml-1">
                                                        {{ $recommendation['store']->rating }}/5
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            @endif

            @if($stores->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($stores as $store)
                        <x-card class="hover:shadow-lg transition-shadow">
                            <div class="flex items-start space-x-4">
                                @if($store->logo)
                                    <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                        <a href="{{ route('stores.show', $store) }}" class="hover:text-primary-600">
                                            {{ $store->name }}
                                        </a>
                                    </h3>
                                    @if($store->primaryLocation->first())
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $store->primaryLocation->first()->city }}, {{ $store->primaryLocation->first()->province }}
                                        </p>
                                    @endif
                                    @if($store->rating > 0)
                                        <div class="flex items-center mt-2">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $store->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">
                                                {{ number_format($store->rating, 1) }}/5 ({{ $store->total_reviews }} reviews)
                                            </span>
                                        </div>
                                    @endif
                                    <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                        {{ $store->products_count ?? $store->products->count() }} produk
                                    </div>
                                </div>
                            </div>
                            @if($store->description)
                                <p class="mt-4 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit($store->description, 100) }}
                                </p>
                            @endif
                        </x-card>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $stores->links() }}
                </div>
            @else
                <x-card>
                    <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                        <p>Tidak ada toko ditemukan.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

