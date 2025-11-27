<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Lokasi Toko
            </h2>
            <a href="{{ route('stores.locations.index', $store) }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Edit Lokasi</h3>
                </x-slot>

                <form action="{{ route('stores.locations.update', [$store, $location]) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <x-form-group>
                            <x-slot name="label">Nama Lokasi *</x-slot>
                            <x-text-input type="text" name="name" value="{{ old('name', $location->name) }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Alamat Lengkap *</x-slot>
                            <x-textarea-input name="address" rows="3" required>{{ old('address', $location->address) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('address')" class="mt-2" />
                        </x-form-group>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-form-group>
                                <x-slot name="label">Kota *</x-slot>
                                <x-text-input type="text" name="city" value="{{ old('city', $location->city) }}" required />
                                <x-input-error :messages="$errors->get('city')" class="mt-2" />
                            </x-form-group>

                            <x-form-group>
                                <x-slot name="label">Provinsi *</x-slot>
                                <x-text-input type="text" name="province" value="{{ old('province', $location->province) }}" required />
                                <x-input-error :messages="$errors->get('province')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <x-form-group>
                                <x-slot name="label">Kode Pos</x-slot>
                                <x-text-input type="text" name="postal_code" value="{{ old('postal_code', $location->postal_code) }}" />
                                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                            </x-form-group>

                            <x-form-group>
                                <x-slot name="label">Latitude</x-slot>
                                <x-text-input type="number" id="location_latitude" name="latitude" value="{{ old('latitude', $location->latitude) }}" step="any" />
                                <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                            </x-form-group>

                            <x-form-group>
                                <x-slot name="label">Longitude</x-slot>
                                <x-text-input type="number" id="location_longitude" name="longitude" value="{{ old('longitude', $location->longitude) }}" step="any" />
                                <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Google Maps Picker -->
                        <div>
                            <x-form-group>
                                <x-slot name="label">Pilih Lokasi di Peta</x-slot>
                                <div class="space-y-2">
                                    <div class="flex gap-2">
                                        <x-text-input 
                                            type="text" 
                                            id="location_search" 
                                            placeholder="Cari alamat..." 
                                            class="flex-1" />
                                        <button type="button" onclick="searchLocation()" class="px-4 py-2 bg-primary-600 text-white rounded hover:bg-primary-700">
                                            Cari
                                        </button>
                                        <button type="button" onclick="getCurrentLocation()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                                            Lokasi Saya
                                        </button>
                                    </div>
                                    <div id="map" class="w-full h-96 rounded-lg border border-gray-300 dark:border-gray-700"></div>
                                </div>
                            </x-form-group>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-form-group>
                                <x-slot name="label">Phone</x-slot>
                                <x-text-input type="text" name="phone" value="{{ old('phone', $location->phone) }}" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </x-form-group>

                            <x-form-group>
                                <x-slot name="label">Email</x-slot>
                                <x-text-input type="email" name="email" value="{{ old('email', $location->email) }}" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_primary" id="is_primary" value="1" 
                                       {{ old('is_primary', $location->is_primary) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_primary" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Set sebagai lokasi utama
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $location->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Aktif
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('stores.locations.index', $store) }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Simpan</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
    <script>
        let map;
        let marker;
        let geocoder;
        let autocomplete;

        function initMap() {
            const lat = parseFloat(document.getElementById('location_latitude').value) || -6.2088;
            const lng = parseFloat(document.getElementById('location_longitude').value) || 106.8456;
            
            map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: lat, lng: lng },
                zoom: lat && lng ? 15 : 12,
            });

            geocoder = new google.maps.Geocoder();

            const searchInput = document.getElementById('location_search');
            autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo('bounds', map);
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                setMarker(place.geometry.location.lat(), place.geometry.location.lng());
                updateAddressFields(place);
            });

            map.addListener('click', function(event) {
                setMarker(event.latLng.lat(), event.latLng.lng());
            });

            if (lat && lng) {
                setMarker(lat, lng);
            }
        }

        function setMarker(lat, lng) {
            if (marker) {
                marker.setPosition({ lat: lat, lng: lng });
            } else {
                marker = new google.maps.Marker({
                    position: { lat: lat, lng: lng },
                    map: map,
                    draggable: true,
                });
                marker.addListener('dragend', function() {
                    const position = marker.getPosition();
                    updateCoordinates(position.lat(), position.lng());
                });
            }
            updateCoordinates(lat, lng);
            reverseGeocode(lat, lng);
        }

        function updateCoordinates(lat, lng) {
            document.getElementById('location_latitude').value = lat.toFixed(8);
            document.getElementById('location_longitude').value = lng.toFixed(8);
        }

        function reverseGeocode(lat, lng) {
            geocoder.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    updateAddressFields(results[0]);
                }
            });
        }

        function updateAddressFields(place) {
            const addressComponents = place.address_components || [];
            const addressFields = {
                'locality': '',
                'administrative_area_level_1': '',
                'postal_code': '',
            };

            addressComponents.forEach(component => {
                const types = component.types;
                if (types.includes('locality')) {
                    addressFields.locality = component.long_name;
                }
                if (types.includes('administrative_area_level_1')) {
                    addressFields.administrative_area_level_1 = component.short_name;
                }
                if (types.includes('postal_code')) {
                    addressFields.postal_code = component.long_name;
                }
            });

            const addressInput = document.querySelector('textarea[name="address"]');
            if (addressInput && !addressInput.value) {
                addressInput.value = place.formatted_address;
            }

            const cityInput = document.querySelector('input[name="city"]');
            if (cityInput && !cityInput.value) {
                cityInput.value = addressFields.locality || '';
            }

            const provinceInput = document.querySelector('input[name="province"]');
            if (provinceInput && !provinceInput.value) {
                provinceInput.value = addressFields.administrative_area_level_1 || '';
            }

            const postalCodeInput = document.querySelector('input[name="postal_code"]');
            if (postalCodeInput && !postalCodeInput.value) {
                postalCodeInput.value = addressFields.postal_code || '';
            }
        }

        function searchLocation() {
            const query = document.getElementById('location_search').value;
            if (query) {
                geocoder.geocode({ address: query }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        const location = results[0].geometry.location;
                        map.setCenter(location);
                        map.setZoom(15);
                        setMarker(location.lat(), location.lng());
                    } else {
                        alert('Lokasi tidak ditemukan.');
                    }
                });
            }
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    map.setCenter({ lat: lat, lng: lng });
                    map.setZoom(15);
                    setMarker(lat, lng);
                });
            }
        }
    </script>
    @endpush
</x-app-layout>

