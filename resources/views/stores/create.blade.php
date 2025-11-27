<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Daftarkan Toko Anda
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Pendaftaran Toko</h3>
                </x-slot>

                <form action="{{ route('stores.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Store Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Toko</h4>
                            
                            <div class="space-y-4">
                                <x-form-group>
                                    <x-slot name="label">Nama Toko *</x-slot>
                                    <x-text-input type="text" name="name" value="{{ old('name') }}" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Deskripsi Toko</x-slot>
                                    <x-textarea-input name="description" rows="4">{{ old('description') }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Nomor Telepon *</x-slot>
                                    <x-text-input type="text" name="phone" value="{{ old('phone') }}" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Email</x-slot>
                                    <x-text-input type="email" name="email" value="{{ old('email') }}" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Website</x-slot>
                                    <x-text-input type="url" name="website" value="{{ old('website') }}" placeholder="https://..." />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Nomor SIUP / Izin Usaha</x-slot>
                                    <x-text-input type="text" name="business_license" value="{{ old('business_license') }}" />
                                    <x-input-error :messages="$errors->get('business_license')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Logo & Banner -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Logo & Banner</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form-group>
                                    <x-slot name="label">Logo Toko</x-slot>
                                    <input type="file" name="logo" accept="image/*" 
                                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-primary-50 file:text-primary-700
                                                  hover:file:bg-primary-100
                                                  dark:file:bg-primary-900 dark:file:text-primary-300">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 2MB (JPG, PNG, GIF, WebP)</p>
                                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Banner Toko</x-slot>
                                    <input type="file" name="banner" accept="image/*" 
                                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-primary-50 file:text-primary-700
                                                  hover:file:bg-primary-100
                                                  dark:file:bg-primary-900 dark:file:text-primary-300">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 5MB (JPG, PNG, GIF, WebP)</p>
                                    <x-input-error :messages="$errors->get('banner')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Dokumen (SIUP, NPWP, dll)</h4>
                            
                            <x-form-group>
                                <x-slot name="label">Upload Dokumen</x-slot>
                                <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" 
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-primary-50 file:text-primary-700
                                              hover:file:bg-primary-100
                                              dark:file:bg-primary-900 dark:file:text-primary-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 5MB per file (PDF, JPG, PNG)</p>
                                <x-input-error :messages="$errors->get('documents')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Location -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Lokasi Toko</h4>
                            
                            <div class="space-y-4">
                                <x-form-group>
                                    <x-slot name="label">Nama Lokasi *</x-slot>
                                    <x-text-input type="text" name="location[name]" value="{{ old('location.name') }}" placeholder="Kantor Pusat / Cabang Utama" required />
                                    <x-input-error :messages="$errors->get('location.name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Alamat Lengkap *</x-slot>
                                    <x-textarea-input name="location[address]" rows="3" required>{{ old('location.address') }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('location.address')" class="mt-2" />
                                </x-form-group>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group>
                                        <x-slot name="label">Kota *</x-slot>
                                        <x-text-input type="text" name="location[city]" value="{{ old('location.city') }}" required />
                                        <x-input-error :messages="$errors->get('location.city')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group>
                                        <x-slot name="label">Provinsi *</x-slot>
                                        <x-text-input type="text" name="location[province]" value="{{ old('location.province') }}" required />
                                        <x-input-error :messages="$errors->get('location.province')" class="mt-2" />
                                    </x-form-group>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <x-form-group>
                                        <x-slot name="label">Kode Pos</x-slot>
                                        <x-text-input type="text" name="location[postal_code]" value="{{ old('location.postal_code') }}" />
                                        <x-input-error :messages="$errors->get('location.postal_code')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group>
                                        <x-slot name="label">Latitude</x-slot>
                                        <x-text-input type="number" id="location_latitude" name="location[latitude]" value="{{ old('location.latitude') }}" step="any" />
                                        <x-input-error :messages="$errors->get('location.latitude')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group>
                                        <x-slot name="label">Longitude</x-slot>
                                        <x-text-input type="number" id="location_longitude" name="location[longitude]" value="{{ old('location.longitude') }}" step="any" />
                                        <x-input-error :messages="$errors->get('location.longitude')" class="mt-2" />
                                    </x-form-group>
                                </div>

                                <!-- Google Maps Picker -->
                                <div class="mt-4">
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
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                Klik pada peta atau gunakan pencarian untuk memilih lokasi. Koordinat akan otomatis terisi.
                                            </p>
                                        </div>
                                    </x-form-group>
                                </div>
                            </div>
                        </div>

                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Catatan:</strong> Setelah pendaftaran, toko Anda akan ditinjau oleh admin. Anda akan mendapat notifikasi setelah toko disetujui atau ditolak.
                            </p>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('dashboard') }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Daftarkan Toko</x-button>
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
            // Default to Jakarta, Indonesia
            const defaultLocation = { lat: -6.2088, lng: 106.8456 };
            
            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: 12,
            });

            geocoder = new google.maps.Geocoder();

            // Initialize autocomplete for address search
            const searchInput = document.getElementById('location_search');
            autocomplete = new google.maps.places.Autocomplete(searchInput);
            autocomplete.bindTo('bounds', map);
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry) {
                    return;
                }
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                setMarker(place.geometry.location.lat(), place.geometry.location.lng());
                updateAddressFields(place);
            });

            // Click on map to set location
            map.addListener('click', function(event) {
                setMarker(event.latLng.lat(), event.latLng.lng());
            });

            // Initialize marker if coordinates exist
            const lat = parseFloat(document.getElementById('location_latitude').value);
            const lng = parseFloat(document.getElementById('location_longitude').value);
            if (lat && lng) {
                setMarker(lat, lng);
                map.setCenter({ lat: lat, lng: lng });
                map.setZoom(15);
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
                'street_number': '',
                'route': '',
                'locality': '',
                'administrative_area_level_1': '',
                'postal_code': '',
            };

            addressComponents.forEach(component => {
                const types = component.types;
                if (types.includes('street_number')) {
                    addressFields.street_number = component.long_name;
                }
                if (types.includes('route')) {
                    addressFields.route = component.long_name;
                }
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

            // Update form fields if they exist
            const addressInput = document.querySelector('textarea[name="location[address]"]');
            if (addressInput && !addressInput.value) {
                addressInput.value = `${addressFields.street_number} ${addressFields.route}`.trim() || place.formatted_address;
            }

            const cityInput = document.querySelector('input[name="location[city]"]');
            if (cityInput && !cityInput.value) {
                cityInput.value = addressFields.locality || '';
            }

            const provinceInput = document.querySelector('input[name="location[province]"]');
            if (provinceInput && !provinceInput.value) {
                provinceInput.value = addressFields.administrative_area_level_1 || '';
            }

            const postalCodeInput = document.querySelector('input[name="location[postal_code]"]');
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
                        alert('Lokasi tidak ditemukan. Silakan coba dengan alamat yang lebih spesifik.');
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
                }, function() {
                    alert('Tidak dapat mengakses lokasi Anda. Pastikan izin lokasi sudah diberikan.');
                });
            } else {
                alert('Browser Anda tidak mendukung geolocation.');
            }
        }
    </script>
    @endpush
</x-app-layout>

