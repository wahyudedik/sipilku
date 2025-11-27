@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Cari Toko Terdekat
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('stores.find-nearest') }}" id="searchForm" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
                            <x-form-group label="Radius (km)">
                                <x-text-input 
                                    type="number" 
                                    name="radius" 
                                    value="{{ request('radius', 10) }}" 
                                    min="1" 
                                    max="100" 
                                    step="1" 
                                    class="w-full" />
                            </x-form-group>
                        </div>
                        <div class="flex items-end">
                            <x-button variant="primary" size="md" type="submit" class="w-full">Cari</x-button>
                        </div>
                    </div>
                    <input type="hidden" name="latitude" id="search_latitude" value="{{ request('latitude') }}">
                    <input type="hidden" name="longitude" id="search_longitude" value="{{ request('longitude') }}">
                </form>
            </x-card>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Map -->
                <div class="lg:col-span-2">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Peta Lokasi</h3>
                        </x-slot>
                        <div id="map" class="w-full h-96 rounded-lg"></div>
                    </x-card>
                </div>

                <!-- Results -->
                <div>
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">
                                Toko Terdekat
                                @if($latitude && $longitude)
                                    <span class="text-sm font-normal text-gray-500">({{ $stores->count() }} ditemukan)</span>
                                @endif
                            </h3>
                        </x-slot>
                        <div class="space-y-4 max-h-[600px] overflow-y-auto">
                            @if($latitude && $longitude && $stores->count() > 0)
                                @foreach($stores as $store)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition cursor-pointer" 
                                         onclick="focusStore({{ $store->nearest_location->latitude }}, {{ $store->nearest_location->longitude }})">
                                        <div class="flex items-start space-x-3">
                                            @if($store->logo)
                                                <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-16 h-16 object-cover rounded">
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                                    <a href="{{ route('stores.show', $store) }}" class="hover:text-primary-600">
                                                        {{ $store->name }}
                                                    </a>
                                                </h4>
                                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                    {{ $store->nearest_location->full_address }}
                                                </p>
                                                <div class="flex items-center justify-between mt-2">
                                                    <span class="text-sm font-semibold text-primary-600 dark:text-primary-400">
                                                        {{ number_format($store->distance, 2) }} km
                                                    </span>
                                                    @if($store->rating > 0)
                                                        <span class="text-xs text-gray-500">
                                                            ⭐ {{ $store->rating }}/5
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($latitude && $longitude)
                                <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    Tidak ada toko ditemukan dalam radius {{ $radius }} km.
                                </p>
                            @else
                                <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                    Pilih lokasi di peta atau gunakan pencarian untuk menemukan toko terdekat.
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

        function initMap() {
            const defaultLocation = { lat: -6.2088, lng: 106.8456 };
            
            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 12,
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

            // Click on map
            map.addListener('click', function(event) {
                setSearchLocation(event.latLng.lat(), event.latLng.lng());
            });

            // Initialize with existing location or get current location
            @if($latitude && $longitude)
                setSearchLocation({{ $latitude }}, {{ $longitude }});
                loadStores({{ $latitude }}, {{ $longitude }}, {{ $radius }});
            @else
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        setSearchLocation(position.coords.latitude, position.coords.longitude);
                    });
                }
            @endif
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
                        url: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
                    },
                    title: 'Lokasi Pencarian'
                });
            }

            map.setCenter({ lat: lat, lng: lng });
        }

        function loadStores(lat, lng, radius) {
            // Clear existing markers
            markers.forEach(marker => marker.setMap(null));
            markers = [];

            @if($stores->count() > 0)
                const stores = @json($stores->map(function($store) {
                    return [
                        'name' => $store->name,
                        'uuid' => $store->uuid,
                        'latitude' => $store->nearest_location->latitude,
                        'longitude' => $store->nearest_location->longitude,
                        'address' => $store->nearest_location->full_address,
                        'distance' => $store->distance,
                    ];
                }));

                stores.forEach(store => {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(store.latitude), lng: parseFloat(store.longitude) },
                        map: map,
                        title: store.name,
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div class="p-2">
                                <h4 class="font-semibold">${store.name}</h4>
                                <p class="text-sm text-gray-600">${store.address}</p>
                                <p class="text-sm text-primary-600 font-semibold">${parseFloat(store.distance).toFixed(2)} km</p>
                                <a href="/stores/${store.uuid}" class="text-primary-600 hover:underline text-sm">Lihat Detail</a>
                            </div>
                        `
                    });

                    marker.addListener('click', function() {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                });

                // Fit bounds to show all stores
                if (markers.length > 0) {
                    const bounds = new google.maps.LatLngBounds();
                    markers.forEach(marker => bounds.extend(marker.getPosition()));
                    if (searchMarker) {
                        bounds.extend(searchMarker.getPosition());
                    }
                    map.fitBounds(bounds);
                }
            @endif
        }

        function focusStore(lat, lng) {
            map.setCenter({ lat: lat, lng: lng });
            map.setZoom(16);
        }

        // Auto-submit on radius change
        document.querySelector('input[name="radius"]').addEventListener('change', function() {
            const lat = document.getElementById('search_latitude').value;
            const lng = document.getElementById('search_longitude').value;
            if (lat && lng) {
                document.getElementById('searchForm').submit();
            }
        });
    </script>
    @endpush
</x-app-layout>

