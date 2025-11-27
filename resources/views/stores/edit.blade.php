@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Toko
            </h2>
            <a href="{{ route('stores.my-store') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Edit Toko</h3>
                </x-slot>

                <form action="{{ route('stores.update', $store) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Store Information -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Informasi Toko</h4>
                            
                            <div class="space-y-4">
                                <x-form-group>
                                    <x-slot name="label">Nama Toko *</x-slot>
                                    <x-text-input type="text" name="name" value="{{ old('name', $store->name) }}" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Deskripsi Toko</x-slot>
                                    <x-textarea-input name="description" rows="4">{{ old('description', $store->description) }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Nomor Telepon *</x-slot>
                                    <x-text-input type="text" name="phone" value="{{ old('phone', $store->phone) }}" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Email</x-slot>
                                    <x-text-input type="email" name="email" value="{{ old('email', $store->email) }}" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Website</x-slot>
                                    <x-text-input type="url" name="website" value="{{ old('website', $store->website) }}" />
                                    <x-input-error :messages="$errors->get('website')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Nomor SIUP / Izin Usaha</x-slot>
                                    <x-text-input type="text" name="business_license" value="{{ old('business_license', $store->business_license) }}" />
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
                                    @if($store->logo)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($store->logo) }}" alt="Current logo" class="w-32 h-32 object-cover rounded-lg">
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

                                <x-form-group>
                                    <x-slot name="label">Banner Toko</x-slot>
                                    @if($store->banner)
                                        <div class="mb-2">
                                            <img src="{{ Storage::url($store->banner) }}" alt="Current banner" class="w-full h-32 object-cover rounded-lg">
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
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Dokumen (SIUP, NPWP, dll)</h4>
                            
                            @if($store->documents && count($store->documents) > 0)
                                <div class="mb-4 space-y-2">
                                    <p class="text-sm font-semibold">Dokumen saat ini:</p>
                                    @foreach($store->documents as $index => $doc)
                                        <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-800 rounded">
                                            <a href="{{ Storage::url($doc) }}" target="_blank" class="text-primary-600 hover:underline">
                                                Dokumen {{ $index + 1 }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                            
                            <x-form-group>
                                <x-slot name="label">Upload Dokumen Baru</x-slot>
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

                        <!-- Status -->
                        <div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', $store->is_active) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Aktifkan toko
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('stores.my-store') }}">
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

