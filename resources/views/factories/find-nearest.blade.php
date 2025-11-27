@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cari Pabrik Terdekat
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('factories.find-nearest') }}" id="searchForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                        <div class="md:col-span-2">
                            <x-form-group label="Cari Alamat atau Lokasi">
                                <x-text-input 
                                    type="text" 
                                    id="address_search" 
                                    name="address" 
                                    value="{{ request('address') }}" 
                                    placeholder="Masukkan alamat atau klik pada peta..." 
                                    class="w-full" />
                            </x-form-group>
                        </div>
                        <div>
                            <x-form-group label="Tipe Pabrik">
                                <select name="factory_type_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">Semua Tipe</option>
                                    @foreach($factoryTypes as $type)
                                        <option value="{{ $type->uuid }}" {{ $factoryTypeId === $type->uuid ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form-group>
                        </div>
                        <div>
                            <x-form-group label="Radius (km)">
                                <x-text-input 
                                    type="number" 
                                    name="radius" 
                                    value="{{ request('radius', 50) }}" 
                                    min="1" 
                                    max="200" 
                                    step="1" 
                                    class="w-full" />
                            </x-form-group>
                        </div>
                        <div class="flex items-end">
                            <x-button variant="primary" size="md" type="submit" class="w-full">Cari</x-button>
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="search_latitude" value="{{ $latitude }}">
                    <input type="hidden" name="longitude" id="search_longitude" value="{{ $longitude }}">
                </form>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Map -->
                <div class="lg:col-span-2">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Peta Lokasi Pabrik</h3>
                        </x-slot>
                        <div id="map" class="w-full h-[600px] rounded-lg"></div>
                    </x-card>
                </div>

                <!-- Results -->
                <div>
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">
                                Pabrik Terdekat
                                @if($latitude && $longitude)
                                    <span class="text-sm font-normal text-gray-500">({{ $factories->count() }} ditemukan)</span>
                                @endif
                            </h3>
                        </x-slot>
                        <div class="space-y-4 max-h-[600px] overflow-y-auto">
                            @if($latitude && $longitude && $factories->count() > 0)
                                @foreach($factories as $factory)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition cursor-pointer" 
                                         onclick="focusFactory({{ $factory->nearest_location->latitude }}, {{ $factory->nearest_location->longitude }})">
                                        <div class="flex items-start space-x-3">
                                            @if($factory->logo)
                                                <img src="{{ Storage::url($factory->logo) }}" alt="{{ $factory->name }}" class="w-16 h-16 object-cover rounded">
                                            @else
                                                <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                    </svg>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                    <a href="{{ route('factories.show', $factory) }}" class="hover:text-primary-600">
                                                        {{ $factory->name }}
                                                    </a>
                                                </h4>
                                                @if($factory->factoryType)
                                                    <x-badge variant="default" size="xs" class="mt-1">
                                                        {{ $factory->factoryType->name }}
                                                    </x-badge>
                                                @endif
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $factory->nearest_location->full_address }}
                                                </p>
                                                <div class="flex items-center justify-between mt-2">
                                                    <div>
                                                        <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                                            {{ number_format($factory->distance, 2) }} km
                                                        </span>
                                                        @if($factory->delivery_cost > 0)
                                                            <span class="text-xs text-gray-500 ml-2">
                                                                Delivery: Rp {{ number_format($factory->delivery_cost, 0, ',', '.') }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($factory->rating > 0)
                                                        <span class="text-xs text-gray-500">
                                                            ⭐ {{ $factory->rating }}/5
                                                        </span>
                                                    @endif
                                                </div>
                                                @if($factory->nearest_location->operating_hours)
                                                    <div class="mt-2 text-xs text-gray-500">
                                                        <strong>Jam Operasional:</strong>
                                                        @if(is_array($factory->nearest_location->operating_hours))
                                                            @foreach($factory->nearest_location->operating_hours as $day => $hours)
                                                                <div>{{ ucfirst($day) }}: {{ $hours }}</div>
                                                            @endforeach
                                                        @else
                                                            {{ $factory->nearest_location->operating_hours }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($latitude && $longitude)
                                <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    Tidak ada pabrik ditemukan dalam radius {{ $radius }} km.
                                </p>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    Pilih lokasi di peta atau gunakan pencarian untuk menemukan pabrik terdekat.
                                </p>
                            @endif
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
    <script>
        let map;
        let markers = [];
        let searchMarker;
        let geocoder;
        let autocomplete;

        const factories = @json($factories->map(function($factory) {
            return [
                'uuid' => $factory->uuid,
                'name' => $factory->name,
                'slug' => $factory->slug,
                'logo' => $factory->logo,
                'factory_type' => $factory->factoryType ? $factory->factoryType->name : null,
                'latitude' => $factory->nearest_location ? $factory->nearest_location->latitude : null,
                'longitude' => $factory->nearest_location ? $factory->nearest_location->longitude : null,
                'distance' => $factory->distance ?? null,
                'delivery_cost' => $factory->delivery_cost ?? null,
            ];
        })->filter(function($f) {
            return $f['latitude'] && $f['longitude'];
        })->values());

        function initMap() {
            const defaultLat = {{ $latitude ?? -6.2088 }};
            const defaultLng = {{ $longitude ?? 106.8456 }};
            const defaultLocation = { lat: defaultLat, lng: defaultLng };
            
            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: {{ $latitude && $longitude ? 11 : 10 }},
            });

            geocoder = new google.maps.Geocoder();

            // Initialize autocomplete
            const searchInput = document.getElementById('address_search');
            autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo('bounds', map);
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                map.setCenter(place.geometry.location);
                map.setZoom(15);
                setSearchLocation(place.geometry.location.lat(), place.geometry.location.lng());
            });

            // Map click handler
            map.addListener('click', function(event) {
                setSearchLocation(event.latLng.lat(), event.latLng.lng());
            });

            // Set search marker if coordinates exist
            if (defaultLat && defaultLng) {
                setSearchLocation(defaultLat, defaultLng);
                loadFactories();
            }

            // Get current location button
            if (navigator.geolocation) {
                const currentLocationBtn = document.createElement('button');
                currentLocationBtn.textContent = '📍 Lokasi Saya';
                currentLocationBtn.className = 'px-4 py-2 bg-white border border-gray-300 rounded shadow-md hover:bg-gray-50';
                currentLocationBtn.style.margin = '10px';
                map.controls[google.maps.ControlPosition.TOP_CENTER].push(currentLocationBtn);
                currentLocationBtn.addEventListener('click', function() {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        setSearchLocation(position.coords.latitude, position.coords.longitude);
                        map.setCenter({ lat: position.coords.latitude, lng: position.coords.longitude });
                        map.setZoom(15);
                    });
                });
            }
        }

        function setSearchLocation(lat, lng) {
            document.getElementById('search_latitude').value = lat;
            document.getElementById('search_longitude').value = lng;

            if (searchMarker) {
                searchMarker.setPosition({ lat: lat, lng: lng });
            } else {
                searchMarker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: map,
                    icon: {
                        url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                    },
                    title: 'Lokasi Pencarian',
                });
            }
        }

        function loadFactories() {
            // Clear existing markers
            markers.forEach(marker => marker.setMap(null));
            markers = [];

            // Add factory markers
            factories.forEach(function(factory) {
                if (factory.latitude && factory.longitude) {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(factory.latitude), lng: parseFloat(factory.longitude) },
                        map: map,
                        title: factory.name,
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div class="p-2">
                                <h3 class="font-semibold">${factory.name}</h3>
                                ${factory.factory_type ? `<p class="text-sm text-gray-600">${factory.factory_type}</p>` : ''}
                                ${factory.distance ? `<p class="text-sm">Jarak: ${factory.distance} km</p>` : ''}
                                ${factory.delivery_cost ? `<p class="text-sm">Delivery: Rp ${factory.delivery_cost.toLocaleString('id-ID')}</p>` : ''}
                                <a href="/factories/${factory.slug}" class="text-primary-600 hover:underline text-sm">Lihat Detail →</a>
                            </div>
                        `,
                    });

                    marker.addListener('click', function() {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });
        }

        function focusFactory(lat, lng) {
            map.setCenter({ lat: lat, lng: lng });
            map.setZoom(15);
        }
    </script>
    @endpush
</x-app-layout>

