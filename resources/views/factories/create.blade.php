<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Daftarkan Pabrik Anda
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Pendaftaran Pabrik</h3>
                </x-slot>

                <form action="{{ route('factories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Factory Type & Category -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Tipe & Kategori Pabrik</h4>
                            
                            <div class="space-y-4">
                                <x-form-group label="Tipe Pabrik *" name="factory_type_id" required>
                                    <x-select-input name="factory_type_id" required>
                                        <option value="">Pilih Tipe Pabrik</option>
                                        @foreach($factoryTypes as $type)
                                            <option value="{{ $type->uuid }}" {{ old('factory_type_id') === $type->uuid ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('factory_type_id')" class="mt-2" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Pilih tipe pabrik Anda (Beton, Bata, Genting, Baja, Precast, Keramik, Kayu, dll)
                                    </p>
                                </x-form-group>

                                <x-form-group label="Kategori *" name="category" required>
                                    <x-select-input name="category" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="industri" {{ old('category') === 'industri' ? 'selected' : '' }}>Industri</option>
                                        <option value="umkm" {{ old('category') === 'umkm' ? 'selected' : '' }}>UMKM</option>
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="UMKM (Opsional)" name="umkm_id">
                                    <x-select-input name="umkm_id">
                                        <option value="">Tidak Terkait UMKM</option>
                                        @foreach($umkms as $umkm)
                                            <option value="{{ $umkm->uuid }}" {{ old('umkm_id') === $umkm->uuid ? 'selected' : '' }}>
                                                {{ $umkm->name }}
                                            </option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('umkm_id')" class="mt-2" />
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        Pilih jika pabrik Anda terkait dengan UMKM tertentu
                                    </p>
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Factory Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Pabrik</h4>
                            
                            <div class="space-y-4">
                                <x-form-group label="Nama Pabrik *" name="name" required>
                                    <x-text-input type="text" name="name" value="{{ old('name') }}" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Deskripsi Pabrik" name="description">
                                    <x-textarea-input name="description" rows="4">{{ old('description') }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Nomor Telepon *" name="phone" required>
                                    <x-text-input type="text" name="phone" value="{{ old('phone') }}" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Email" name="email">
                                    <x-text-input type="email" name="email" value="{{ old('email') }}" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Website" name="website">
                                    <x-text-input type="url" name="website" value="{{ old('website') }}" placeholder="https://..." />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Nomor Izin Operasional / SIUP" name="business_license">
                                    <x-text-input type="text" name="business_license" value="{{ old('business_license') }}" />
                                    <x-input-error :messages="$errors->get('business_license')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Delivery Settings -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Pengaturan Pengiriman</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form-group label="Harga Delivery per KM (Rp)" name="delivery_price_per_km">
                                    <x-text-input type="number" name="delivery_price_per_km" step="0.01" min="0" value="{{ old('delivery_price_per_km') }}" />
                                    <x-input-error :messages="$errors->get('delivery_price_per_km')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Maksimal Jarak Delivery (KM)" name="max_delivery_distance">
                                    <x-text-input type="number" name="max_delivery_distance" min="1" value="{{ old('max_delivery_distance') }}" />
                                    <x-input-error :messages="$errors->get('max_delivery_distance')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Logo & Banner -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Logo & Banner</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form-group label="Logo Pabrik" name="logo">
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

                                <x-form-group label="Banner Pabrik" name="banner">
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
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Dokumen (Izin Operasional, NPWP, dll)</h4>
                            
                            <x-form-group label="Upload Dokumen" name="documents">
                                <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-primary-50 file:text-primary-700
                                              hover:file:bg-primary-100
                                              dark:file:bg-primary-900 dark:file:text-primary-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 5MB per file (PDF, JPG, PNG)</p>
                                <x-input-error :messages="$errors->get('documents.*')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Certifications -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Sertifikat (ISO, Sertifikat Kualitas, dll)</h4>
                            
                            <x-form-group label="Upload Sertifikat" name="certifications">
                                <input type="file" name="certifications[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-primary-50 file:text-primary-700
                                              hover:file:bg-primary-100
                                              dark:file:bg-primary-900 dark:file:text-primary-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Maksimal 5MB per file (PDF, JPG, PNG)</p>
                                <x-input-error :messages="$errors->get('certifications.*')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Location -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Lokasi Pabrik</h4>
                            
                            <div class="space-y-4">
                                <x-form-group label="Nama Lokasi" name="location[name]">
                                    <x-text-input type="text" name="location[name]" value="{{ old('location.name') }}" placeholder="Kantor Pusat / Pabrik Utama" />
                                    <x-input-error :messages="$errors->get('location.name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Alamat *" name="location[address]" required>
                                    <x-textarea-input name="location[address]" rows="3" required>{{ old('location.address') }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('location.address')" class="mt-2" />
                                </x-form-group>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group label="Kota *" name="location[city]" required>
                                        <x-text-input type="text" name="location[city]" value="{{ old('location.city') }}" required />
                                        <x-input-error :messages="$errors->get('location.city')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group label="Provinsi *" name="location[province]" required>
                                        <x-text-input type="text" name="location[province]" value="{{ old('location.province') }}" required />
                                        <x-input-error :messages="$errors->get('location.province')" class="mt-2" />
                                    </x-form-group>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group label="Kode Pos" name="location[postal_code]">
                                        <x-text-input type="text" name="location[postal_code]" value="{{ old('location.postal_code') }}" />
                                        <x-input-error :messages="$errors->get('location.postal_code')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group label="Negara" name="location[country]">
                                        <x-text-input type="text" name="location[country]" value="{{ old('location.country', 'Indonesia') }}" />
                                        <x-input-error :messages="$errors->get('location.country')" class="mt-2" />
                                    </x-form-group>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group label="Latitude" name="location[latitude]">
                                        <x-text-input type="number" name="location[latitude]" step="0.00000001" value="{{ old('location.latitude') }}" placeholder="-6.2088" />
                                        <x-input-error :messages="$errors->get('location.latitude')" class="mt-2" />
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Koordinat GPS (opsional, untuk fitur pencarian terdekat)</p>
                                    </x-form-group>

                                    <x-form-group label="Longitude" name="location[longitude]">
                                        <x-text-input type="number" name="location[longitude]" step="0.00000001" value="{{ old('location.longitude') }}" placeholder="106.8456" />
                                        <x-input-error :messages="$errors->get('location.longitude')" class="mt-2" />
                                    </x-form-group>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group label="Telepon Lokasi" name="location[phone]">
                                        <x-text-input type="text" name="location[phone]" value="{{ old('location.phone') }}" />
                                        <x-input-error :messages="$errors->get('location.phone')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group label="Email Lokasi" name="location[email]">
                                        <x-text-input type="email" name="location[email]" value="{{ old('location.email') }}" />
                                        <x-input-error :messages="$errors->get('location.email')" class="mt-2" />
                                    </x-form-group>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('dashboard') }}">
                                <x-button variant="secondary" type="button">Cancel</x-button>
                            </a>
                            <x-button variant="primary" type="submit">Daftarkan Pabrik</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>

