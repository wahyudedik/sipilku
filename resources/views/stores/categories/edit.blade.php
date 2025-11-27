@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Edit Kategori: {{ $category->name }}
            </h2>
            <a href="{{ route('stores.categories.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Edit Kategori</h3>
                </x-slot>

                <form action="{{ route('stores.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <x-form-group>
                            <x-slot name="label">Nama Kategori *</x-slot>
                            <x-text-input type="text" name="name" value="{{ old('name', $category->name) }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Deskripsi</x-slot>
                            <x-textarea-input name="description" rows="4">{{ old('description', $category->description) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Gambar Kategori</x-slot>
                            @if($category->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-32 h-32 object-cover rounded-lg">
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                            <p class="text-xs text-gray-500 mt-1">Kosongkan jika tidak ingin mengubah gambar</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Urutan Tampil</x-slot>
                            <x-text-input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" />
                            <p class="text-xs text-gray-500 mt-1">Angka lebih kecil akan ditampilkan lebih dulu</p>
                            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                        </x-form-group>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Aktifkan kategori
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('stores.categories.index') }}">
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

