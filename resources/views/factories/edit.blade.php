@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Pabrik
            </h2>
            <a href="{{ route('factories.my-factory') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Edit Pabrik</h3>
                </x-slot>

                <form action="{{ route('factories.update', $factory) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Factory Type & Category -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Tipe & Kategori Pabrik</h4>
                            
                            <div class="space-y-4">
                                <x-form-group label="Tipe Pabrik *" name="factory_type_id" required>
                                    <x-select-input name="factory_type_id" required>
                                        <option value="">Pilih Tipe Pabrik</option>
                                        @foreach($factoryTypes as $type)
                                            <option value="{{ $type->uuid }}" {{ old('factory_type_id', $factory->factory_type_id) === $type->uuid ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('factory_type_id')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Kategori *" name="category" required>
                                    <x-select-input name="category" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="industri" {{ old('category', $factory->category) === 'industri' ? 'selected' : '' }}>Industri</option>
                                        <option value="umkm" {{ old('category', $factory->category) === 'umkm' ? 'selected' : '' }}>UMKM</option>
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="UMKM (Opsional)" name="umkm_id">
                                    <x-select-input name="umkm_id">
                                        <option value="">Tidak Terkait UMKM</option>
                                        @foreach($umkms as $umkm)
                                            <option value="{{ $umkm->uuid }}" {{ old('umkm_id', $factory->umkm_id) === $umkm->uuid ? 'selected' : '' }}>
                                                {{ $umkm->name }}
                                            </option>
                                        @endforeach
                                    </x-select-input>
                                    <x-input-error :messages="$errors->get('umkm_id')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Factory Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Pabrik</h4>
                            
                            <div class="space-y-4">
                                <x-form-group label="Nama Pabrik *" name="name" required>
                                    <x-text-input type="text" name="name" value="{{ old('name', $factory->name) }}" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Deskripsi Pabrik" name="description">
                                    <x-textarea-input name="description" rows="4">{{ old('description', $factory->description) }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Nomor Telepon *" name="phone" required>
                                    <x-text-input type="text" name="phone" value="{{ old('phone', $factory->phone) }}" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Email" name="email">
                                    <x-text-input type="email" name="email" value="{{ old('email', $factory->email) }}" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Website" name="website">
                                    <x-text-input type="url" name="website" value="{{ old('website', $factory->website) }}" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Nomor Izin Operasional / SIUP" name="business_license">
                                    <x-text-input type="text" name="business_license" value="{{ old('business_license', $factory->business_license) }}" />
                                    <x-input-error :messages="$errors->get('business_license')" class="mt-2" />
                                </x-form-group>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group label="Harga Delivery per KM (Rp)" name="delivery_price_per_km">
                                        <x-text-input type="number" name="delivery_price_per_km" step="0.01" min="0" value="{{ old('delivery_price_per_km', $factory->delivery_price_per_km) }}" />
                                        <x-input-error :messages="$errors->get('delivery_price_per_km')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group label="Maksimal Jarak Delivery (KM)" name="max_delivery_distance">
                                        <x-text-input type="number" name="max_delivery_distance" min="1" value="{{ old('max_delivery_distance', $factory->max_delivery_distance) }}" />
                                        <x-input-error :messages="$errors->get('max_delivery_distance')" class="mt-2" />
                                    </x-form-group>
                                </div>
                            </div>
                        </div>

                        <!-- Logo & Banner -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Logo & Banner</h4>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form-group label="Logo Pabrik" name="logo">
                                    @if($factory->logo)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($factory->logo) }}" alt="Current logo" class="w-32 h-32 object-cover rounded-lg">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Logo saat ini</p>
                                        </div>
                                    @endif
                                    <input type="file" name="logo" accept="image/*" 
                                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-primary-50 file:text-primary-700
                                                  hover:file:bg-primary-100
                                                  dark:file:bg-primary-900 dark:file:text-primary-300">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah</p>
                                    <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Banner Pabrik" name="banner">
                                    @if($factory->banner)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($factory->banner) }}" alt="Current banner" class="w-full h-32 object-cover rounded-lg">
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Banner saat ini</p>
                                        </div>
                                    @endif
                                    <input type="file" name="banner" accept="image/*" 
                                           class="block w-full text-sm text-gray-500 dark:text-gray-400
                                                  file:mr-4 file:py-2 file:px-4
                                                  file:rounded-md file:border-0
                                                  file:text-sm file:font-semibold
                                                  file:bg-primary-50 file:text-primary-700
                                                  hover:file:bg-primary-100
                                                  dark:file:bg-primary-900 dark:file:text-primary-300">
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah</p>
                                    <x-input-error :messages="$errors->get('banner')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Documents -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Dokumen (Izin Operasional, NPWP, dll)</h4>
                            
                            @if($factory->documents && count($factory->documents) > 0)
                                <div class="mb-4 space-y-2">
                                    <p class="text-sm font-semibold">Dokumen saat ini:</p>
                                    @foreach($factory->documents as $index => $doc)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                            <a href="{{ Storage::url($doc) }}" target="_blank" class="text-primary-600 hover:underline">
                                                Dokumen {{ $index + 1 }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <x-form-group label="Upload Dokumen Baru" name="documents">
                                <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png" 
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-primary-50 file:text-primary-700
                                              hover:file:bg-primary-100
                                              dark:file:bg-primary-900 dark:file:text-primary-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload dokumen baru akan mengganti dokumen lama</p>
                                <x-input-error :messages="$errors->get('documents')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Certifications -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Sertifikat (ISO, Sertifikat Kualitas, dll)</h4>
                            
                            @if($factory->certifications && count($factory->certifications) > 0)
                                <div class="mb-4 space-y-2">
                                    <p class="text-sm font-semibold">Sertifikat saat ini:</p>
                                    @foreach($factory->certifications as $index => $cert)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                            <a href="{{ Storage::url($cert) }}" target="_blank" class="text-primary-600 hover:underline">
                                                Sertifikat {{ $index + 1 }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <x-form-group label="Upload Sertifikat Baru" name="certifications">
                                <input type="file" name="certifications[]" multiple accept=".pdf,.jpg,.jpeg,.png" 
                                       class="block w-full text-sm text-gray-500 dark:text-gray-400
                                              file:mr-4 file:py-2 file:px-4
                                              file:rounded-md file:border-0
                                              file:text-sm file:font-semibold
                                              file:bg-primary-50 file:text-primary-700
                                              hover:file:bg-primary-100
                                              dark:file:bg-primary-900 dark:file:text-primary-300">
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Upload sertifikat baru akan mengganti sertifikat lama</p>
                                <x-input-error :messages="$errors->get('certifications')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Status -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $factory->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Aktifkan pabrik
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('factories.my-factory') }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Simpan Perubahan</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>

