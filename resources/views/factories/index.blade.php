@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Daftar Pabrik
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('factories.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-form-group label="Cari" name="search">
                                <x-text-input name="search" value="{{ request('search') }}" placeholder="Nama pabrik..." />
                            </x-form-group>
                        </div>
                        <div>
                            <x-form-group label="Tipe Pabrik" name="factory_type">
                                <x-select-input name="factory_type">
                                    <option value="">Semua Tipe</option>
                                    @foreach($factoryTypes as $type)
                                        <option value="{{ $type->slug }}" {{ request('factory_type') === $type->slug ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </x-select-input>
                            </x-form-group>
                        </div>
                        <div>
                            <x-form-group label="Kategori" name="category">
                                <x-select-input name="category">
                                    <option value="">Semua Kategori</option>
                                    <option value="industri" {{ request('category') === 'industri' ? 'selected' : '' }}>Industri</option>
                                    <option value="umkm" {{ request('category') === 'umkm' ? 'selected' : '' }}>UMKM</option>
                                </x-select-input>
                            </x-form-group>
                        </div>
                        <div>
                            <x-form-group label="Kota" name="city">
                                <x-select-input name="city">
                                    <option value="">Semua Kota</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>
                                            {{ $city }}
                                        </option>
                                    @endforeach
                                </x-select-input>
                            </x-form-group>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <x-button variant="primary" type="submit">Filter</x-button>
                    </div>
                </form>
            </x-card>

            <!-- Factories Grid -->
            @if($factories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($factories as $factory)
                        <x-card class="hover:shadow-lg transition cursor-pointer" onclick="window.location.href='{{ route('factories.show', $factory) }}'">
                            <div class="flex items-start space-x-4">
                                @if($factory->logo)
                                    <img src="{{ Storage::url($factory->logo) }}" alt="{{ $factory->name }}" class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                        {{ $factory->name }}
                                    </h3>
                                    <div class="flex items-center space-x-2 mb-2">
                                        @if($factory->factoryType)
                                            <x-badge variant="default" size="xs">{{ $factory->factoryType->name }}</x-badge>
                                        @endif
                                        <x-badge variant="info" size="xs">{{ ucfirst($factory->category) }}</x-badge>
                                    </div>
                                    @if($factory->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">
                                            {{ $factory->description }}
                                        </p>
                                    @endif
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if($factory->rating > 0)
                                            <div class="flex items-center">
                                                <span class="text-yellow-400">★</span>
                                                <span class="ml-1">{{ $factory->rating }}/5</span>
                                            </div>
                                        @endif
                                        <span>{{ $factory->products_count }} produk</span>
                                        @if($factory->primaryLocation->first())
                                            <span>{{ $factory->primaryLocation->first()->city }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $factories->links() }}
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada pabrik yang ditemukan.</p>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

