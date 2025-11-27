@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('factories.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Pabrik</a>
            <span>/</span>
            @if($factory->factoryType)
                <a href="{{ route('factories.index', ['factory_type' => $factory->factoryType->slug]) }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                    {{ $factory->factoryType->name }}
                </a>
                <span>/</span>
            @endif
            <span class="text-gray-900 dark:text-gray-100">{{ $factory->name }}</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Factory Header -->
            <x-card class="mb-6">
                <div class="flex items-start space-x-6">
                    @if($factory->logo)
                        <img src="{{ Storage::url($factory->logo) }}" alt="{{ $factory->name }}" class="w-32 h-32 object-cover rounded-lg">
                    @else
                        <div class="w-32 h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    @endif
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100">
                                {{ $factory->name }}
                            </h1>
                            <x-badge variant="info">{{ ucfirst($factory->category) }}</x-badge>
                            @if($factory->factoryType)
                                <x-badge variant="default">{{ $factory->factoryType->name }}</x-badge>
                            @endif
                        </div>
                        @if($factory->description)
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ $factory->description }}</p>
                        @endif
                        <div class="flex items-center space-x-6 text-sm">
                            @if($factory->rating > 0)
                                <div class="flex items-center">
                                    <span class="text-yellow-400 text-lg">★</span>
                                    <span class="ml-1 font-medium">{{ $factory->rating }}/5</span>
                                    <span class="text-gray-500 ml-1">({{ $factory->total_reviews }} reviews)</span>
                                </div>
                            @endif
                            <span>{{ $factory->products->count() }} produk</span>
                            @if($factory->primaryLocation->first())
                                <span>{{ $factory->primaryLocation->first()->city }}, {{ $factory->primaryLocation->first()->province }}</span>
                            @endif
                        </div>
                        @if($factory->phone || $factory->email || $factory->website)
                            <div class="mt-4 flex flex-wrap gap-4 text-sm">
                                @if($factory->phone)
                                    <span class="text-gray-600 dark:text-gray-400">📞 {{ $factory->phone }}</span>
                                @endif
                                @if($factory->email)
                                    <span class="text-gray-600 dark:text-gray-400">✉️ {{ $factory->email }}</span>
                                @endif
                                @if($factory->website)
                                    <a href="{{ $factory->website }}" target="_blank" class="text-primary-600 hover:underline">
                                        🌐 Website
                                    </a>
                                @endif
                            </div>
                        @endif
                        @if($factory->delivery_price_per_km)
                            <div class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>🚚 Delivery: Rp {{ number_format($factory->delivery_price_per_km, 0, ',', '.') }}/km</span>
                                @if($factory->max_delivery_distance)
                                    <span class="ml-2">(Max: {{ $factory->max_delivery_distance }} km)</span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>

            @if($factory->banner)
                <x-card class="mb-6">
                    <img src="{{ Storage::url($factory->banner) }}" alt="{{ $factory->name }}" class="w-full h-64 object-cover rounded-lg">
                </x-card>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Products -->
                    <x-card>
                        <x-slot name="header">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Produk Pabrik</h3>
                                <form method="GET" action="{{ route('factories.show', $factory) }}" class="flex items-center space-x-2">
                                    <x-text-input name="product_search" value="{{ request('product_search') }}" placeholder="Cari produk..." size="sm" />
                                    <x-select-input name="product_sort" size="sm" onchange="this.form.submit()">
                                        <option value="latest" {{ request('product_sort') === 'latest' ? 'selected' : '' }}>Terbaru</option>
                                        <option value="price_low" {{ request('product_sort') === 'price_low' ? 'selected' : '' }}>Harga Terendah</option>
                                        <option value="price_high" {{ request('product_sort') === 'price_high' ? 'selected' : '' }}>Harga Tertinggi</option>
                                        <option value="name" {{ request('product_sort') === 'name' ? 'selected' : '' }}>Nama A-Z</option>
                                    </x-select-input>
                                </form>
                            </div>
                        </x-slot>
                        @if($products->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($products as $product)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-2">{{ $product->name }}</h4>
                                        @if($product->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 line-clamp-2">{{ $product->description }}</p>
                                        @endif
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                                    Rp {{ number_format($product->final_price, 0, ',', '.') }}
                                                </p>
                                                <p class="text-xs text-gray-500">per {{ $product->unit }}</p>
                                            </div>
                                            @if($product->quality_grade)
                                                <x-badge variant="info">{{ $product->quality_grade['grade'] ?? 'Standard' }}</x-badge>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="mt-4">
                                {{ $products->links() }}
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-8">Belum ada produk</p>
                        @endif
                    </x-card>

                    <!-- Certifications -->
                    @if($factory->certifications && count($factory->certifications) > 0)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Sertifikat & Kualitas</h3>
                            </x-slot>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                @foreach($factory->certifications as $cert)
                                    <a href="{{ Storage::url($cert) }}" target="_blank" class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 hover:bg-gray-50 dark:hover:bg-gray-800 transition text-center">
                                        <svg class="w-12 h-12 text-primary-600 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Sertifikat</p>
                                    </a>
                                @endforeach
                            </div>
                        </x-card>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Locations -->
                    @if($factory->locations->count() > 0)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Lokasi Pabrik</h3>
                            </x-slot>
                            <div class="space-y-3">
                                @foreach($factory->locations as $location)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                        @if($location->name)
                                            <p class="font-semibold mb-1">{{ $location->name }}</p>
                                        @endif
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $location->full_address }}</p>
                                        @if($location->phone)
                                            <p class="text-xs text-gray-500 mt-1">📞 {{ $location->phone }}</p>
                                        @endif
                                        @if($location->operating_hours)
                                            <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-700">
                                                <p class="text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1">Jam Operasional:</p>
                                                @if(is_array($location->operating_hours))
                                                    <div class="text-xs text-gray-600 dark:text-gray-400 space-y-1">
                                                        @foreach($location->operating_hours as $day => $hours)
                                                            <div class="flex justify-between">
                                                                <span>{{ ucfirst($day) }}:</span>
                                                                <span>{{ $hours }}</span>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $location->operating_hours }}</p>
                                                @endif
                                            </div>
                                        @endif
                                        @if($location->hasCoordinates())
                                            <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" 
                                               target="_blank" 
                                               class="text-xs text-primary-600 hover:underline mt-2 inline-block">
                                                📍 Buka di Google Maps
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </x-card>
                    @endif

                    <!-- Delivery Cost Calculator -->
                    @if($factory->delivery_price_per_km)
                        <x-card>
                            <x-slot name="header">
                                <h3 class="text-lg font-medium">Kalkulator Biaya Delivery</h3>
                            </x-slot>
                            <form id="deliveryCalculatorForm" class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Alamat Tujuan
                                    </label>
                                    <input type="text" 
                                           id="delivery_address" 
                                           placeholder="Masukkan alamat atau koordinat" 
                                           class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <input type="hidden" id="delivery_latitude">
                                    <input type="hidden" id="delivery_longitude">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Tipe Produk (Opsional)
                                    </label>
                                    <select id="product_type" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="">Pilih Tipe Produk</option>
                                        <option value="beton">Beton</option>
                                        <option value="bata">Bata</option>
                                        <option value="genting">Genting</option>
                                        <option value="baja">Baja</option>
                                        <option value="precast">Precast</option>
                                        <option value="keramik">Keramik/Granit</option>
                                        <option value="kayu">Kayu</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Unit (Opsional)
                                    </label>
                                    <select id="product_unit" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="">Pilih Unit</option>
                                        <option value="m3">M³</option>
                                        <option value="m2">M²</option>
                                        <option value="kg">Kg</option>
                                        <option value="ton">Ton</option>
                                        <option value="pcs">Pcs</option>
                                        <option value="mobil">Mobil</option>
                                    </select>
                                </div>
                                <x-button variant="primary" size="md" type="button" onclick="calculateDeliveryCost()" class="w-full">
                                    Hitung Biaya Delivery
                                </x-button>
                                <div id="delivery_result" class="hidden mt-4 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                                    <div class="space-y-2">
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Jarak:</span>
                                            <span class="text-sm font-semibold" id="result_distance">-</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">Biaya Delivery:</span>
                                            <span class="text-sm font-semibold text-primary-600" id="result_cost">-</span>
                                        </div>
                                        <div id="result_cannot_deliver" class="hidden text-xs text-red-600 dark:text-red-400 mt-2">
                                            ⚠️ Jarak melebihi maksimal delivery ({{ $factory->max_delivery_distance }} km)
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </x-card>
                    @endif

                    <!-- Reviews Summary -->
                    <x-card>
                        <x-slot name="header">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Rating & Review</h3>
                                @if($factory->rating > 0)
                                    <div class="text-right">
                                        <div class="text-3xl font-bold">{{ number_format($factory->rating, 1) }}</div>
                                        <div class="flex items-center justify-end">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-5 h-5 {{ $i <= round($factory->rating) ? 'text-yellow-500' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <p class="text-sm text-gray-500">{{ $factory->total_reviews }} review</p>
                                    </div>
                                @endif
                            </div>
                        </x-slot>

                        @auth
                            @php
                                $userReview = \App\Models\FactoryReview::where('factory_id', $factory->uuid)
                                    ->where('user_id', Auth::id())
                                    ->first();
                            @endphp
                            @if(!$userReview)
                                <div class="mb-6 pb-6 border-b border-gray-200 dark:border-gray-700">
                                    <a href="{{ route('factory-reviews.create', $factory) }}" class="inline-block">
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

                        @if($factory->rating > 0)
                            <!-- Category Ratings -->
                            @if(isset($categoryRatings) && (array_sum($categoryRatings) > 0))
                                <div class="mb-6 space-y-3">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">Rating per Kategori</h4>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Kualitas Produk</span>
                                                <span class="text-xs font-semibold">{{ number_format($categoryRatings['product_quality'], 1) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= round($categoryRatings['product_quality']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Kualitas Delivery</span>
                                                <span class="text-xs font-semibold">{{ number_format($categoryRatings['delivery_quality'], 1) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= round($categoryRatings['delivery_quality']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Kualitas Pelayanan</span>
                                                <span class="text-xs font-semibold">{{ number_format($categoryRatings['service_quality'], 1) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= round($categoryRatings['service_quality']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs text-gray-600 dark:text-gray-400">Harga</span>
                                                <span class="text-xs font-semibold">{{ number_format($categoryRatings['price'], 1) }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-3 h-3 {{ $i <= round($categoryRatings['price']) ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if($ratingBreakdown)
                                <div class="space-y-2">
                                    @for($i = 5; $i >= 1; $i--)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm w-12">{{ $i }} ⭐</span>
                                            <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                                <div class="bg-yellow-400 h-2 rounded-full" style="width: {{ $factory->total_reviews > 0 ? ($ratingBreakdown[$i] / $factory->total_reviews * 100) : 0 }}%"></div>
                                            </div>
                                            <span class="text-xs text-gray-500 w-8">{{ $ratingBreakdown[$i] }}</span>
                                        </div>
                                    @endfor
                                </div>
                            @endif

                            <!-- Quality Certification Badges -->
                            @if($factory->certifications && count($factory->certifications) > 0)
                                <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Sertifikasi & Kualitas</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($factory->certifications as $cert)
                                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-200 text-xs rounded-full font-medium">
                                                ✓ {{ $cert }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400 py-4">
                                Belum ada rating untuk pabrik ini.
                            </p>
                        @endif
                    </x-card>
                </div>
            </div>

            <!-- Reviews -->
            @if($reviews->count() > 0)
                <x-card class="mt-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Review & Rating</h3>
                    </x-slot>
                    <div class="space-y-4">
                        @foreach($reviews as $review)
                            @php
                                $isMarkedHelpful = Auth::check() ? $review->isMarkedHelpfulBy(Auth::id()) : false;
                            @endphp
                            <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-0">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1">
                                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $review->user->name }}</p>
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
                                
                                @if($review->ratings_breakdown)
                                    <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                                        @if(isset($review->ratings_breakdown['product_quality']))
                                            <div class="text-gray-600 dark:text-gray-400">
                                                Kualitas Produk: <span class="font-semibold">{{ $review->ratings_breakdown['product_quality'] }}/5</span>
                                            </div>
                                        @endif
                                        @if(isset($review->ratings_breakdown['delivery_quality']))
                                            <div class="text-gray-600 dark:text-gray-400">
                                                Delivery: <span class="font-semibold">{{ $review->ratings_breakdown['delivery_quality'] }}/5</span>
                                            </div>
                                        @endif
                                        @if(isset($review->ratings_breakdown['service_quality']))
                                            <div class="text-gray-600 dark:text-gray-400">
                                                Pelayanan: <span class="font-semibold">{{ $review->ratings_breakdown['service_quality'] }}/5</span>
                                            </div>
                                        @endif
                                        @if(isset($review->ratings_breakdown['price']))
                                            <div class="text-gray-600 dark:text-gray-400">
                                                Harga: <span class="font-semibold">{{ $review->ratings_breakdown['price'] }}/5</span>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($review->comment)
                                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-2">{{ $review->comment }}</p>
                                @endif

                                <div class="flex items-center space-x-4 mt-3">
                                    @auth
                                        @if($review->user_id !== Auth::id())
                                            <form action="{{ route('factory-reviews.mark-helpful', [$factory, $review]) }}" method="POST" class="inline">
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
                    <div class="mt-4">
                        {{ $reviews->links() }}
                    </div>
                </x-card>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places" async defer></script>
    <script>
        let deliveryAutocomplete;
        
        // Initialize delivery address autocomplete
        document.addEventListener('DOMContentLoaded', function() {
            const deliveryAddressInput = document.getElementById('delivery_address');
            if (deliveryAddressInput && typeof google !== 'undefined' && google.maps) {
                deliveryAutocomplete = new google.maps.places.Autocomplete(deliveryAddressInput);
                deliveryAutocomplete.addListener('place_changed', function() {
                    const place = deliveryAutocomplete.getPlace();
                    if (place.geometry) {
                        document.getElementById('delivery_latitude').value = place.geometry.location.lat();
                        document.getElementById('delivery_longitude').value = place.geometry.location.lng();
                    }
                });
            }
        });

        function calculateDeliveryCost() {
            const latitude = document.getElementById('delivery_latitude').value;
            const longitude = document.getElementById('delivery_longitude').value;
            const productType = document.getElementById('product_type').value;
            const unit = document.getElementById('product_unit').value;
            const factoryId = '{{ $factory->uuid }}';

            if (!latitude || !longitude) {
                alert('Silakan pilih alamat tujuan terlebih dahulu.');
                return;
            }

            // Show loading
            const resultDiv = document.getElementById('delivery_result');
            resultDiv.classList.remove('hidden');
            resultDiv.innerHTML = '<p class="text-sm">Menghitung...</p>';

            // Make API call
            fetch('{{ route("factories.calculate-delivery-cost") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    factory_id: factoryId,
                    latitude: parseFloat(latitude),
                    longitude: parseFloat(longitude),
                    product_type: productType || null,
                    unit: unit || null,
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('result_distance').textContent = data.distance + ' km';
                    document.getElementById('result_cost').textContent = 'Rp ' + data.delivery_cost.toLocaleString('id-ID');
                    
                    const cannotDeliverDiv = document.getElementById('result_cannot_deliver');
                    if (!data.can_deliver) {
                        cannotDeliverDiv.classList.remove('hidden');
                    } else {
                        cannotDeliverDiv.classList.add('hidden');
                    }
                } else {
                    alert('Error: ' + (data.message || 'Gagal menghitung biaya delivery'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menghitung biaya delivery.');
            });
        }
    </script>
    @endpush
</x-app-layout>

