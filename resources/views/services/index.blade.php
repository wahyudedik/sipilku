<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Marketplace Jasa Profesional
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search and Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('services.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="md:col-span-2">
                            <x-text-input 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari jasa..." 
                                class="w-full" />
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <x-select-input 
                                name="category" 
                                :options="['' => 'Semua Kategori'] + $categories->pluck('name', 'slug')->toArray()"
                                value="{{ request('category') }}" />
                        </div>

                        <!-- Sort -->
                        <div>
                            <x-select-input 
                                name="sort" 
                                :options="[
                                    'latest' => 'Terbaru',
                                    'price_low' => 'Harga: Rendah ke Tinggi',
                                    'price_high' => 'Harga: Tinggi ke Rendah',
                                    'rating' => 'Rating Tertinggi',
                                    'orders' => 'Paling Banyak Pesanan'
                                ]"
                                value="{{ request('sort', 'latest') }}" />
                        </div>
                    </div>

                    <!-- Price Range -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="min_price" value="Harga Minimum" />
                            <x-text-input 
                                name="min_price" 
                                type="number" 
                                value="{{ request('min_price') }}" 
                                placeholder="0" 
                                class="w-full" />
                        </div>
                        <div>
                            <x-input-label for="max_price" value="Harga Maksimum" />
                            <x-text-input 
                                name="max_price" 
                                type="number" 
                                value="{{ request('max_price') }}" 
                                placeholder="0" 
                                class="w-full" />
                        </div>
                        <div class="flex items-end gap-2">
                            <x-button variant="primary" size="md" type="submit" class="flex-1">
                                Filter
                            </x-button>
                            <a href="{{ route('services.index') }}">
                                <x-button variant="secondary" size="md" type="button">
                                    Reset
                                </x-button>
                            </a>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Services Grid -->
            @if($services->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($services as $service)
                        <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location='{{ route('services.show', $service) }}'">
                            <!-- Service Image -->
                            <div class="relative">
                                @if($service->preview_image)
                                    <img src="{{ Storage::url($service->preview_image) }}" 
                                         alt="{{ $service->title }}"
                                         class="w-full h-48 object-cover rounded-t-lg">
                                @else
                                    <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-t-lg flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            <!-- Service Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-lg text-gray-900 dark:text-gray-100 mb-2 line-clamp-2">
                                    {{ $service->title }}
                                </h3>
                                
                                @if($service->short_description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3 line-clamp-2">
                                        {{ $service->short_description }}
                                    </p>
                                @endif

                                <div class="flex items-center justify-between mb-3">
                                    <div>
                                        @if($service->package_prices && count($service->package_prices) > 0)
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Mulai dari:</span>
                                            <span class="text-xl font-bold text-primary-600 dark:text-primary-400">
                                                Rp {{ number_format(min(array_column($service->package_prices, 'price')), 0, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-sm text-gray-500 dark:text-gray-400">Hubungi untuk harga</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    <div class="flex items-center space-x-2">
                                        @if($service->rating > 0)
                                            <span class="text-yellow-500">⭐ {{ number_format($service->rating, 1) }}</span>
                                            <span>({{ $service->review_count }})</span>
                                        @else
                                            <span>Belum ada rating</span>
                                        @endif
                                    </div>
                                    <span>{{ $service->completed_orders }} pesanan</span>
                                </div>

                                @if($service->category)
                                    <div class="mt-2">
                                        <x-badge variant="default" size="sm">
                                            {{ $service->category->name }}
                                        </x-badge>
                                    </div>
                                @endif

                                @if($service->package_prices && count($service->package_prices) > 1)
                                    <div class="mt-2">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ count($service->package_prices) }} paket tersedia
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $services->links() }}
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada jasa</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Tidak ada jasa yang ditemukan dengan filter yang dipilih.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

