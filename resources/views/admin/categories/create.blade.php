<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Tambah Kategori
            </h2>
            <a href="{{ route('admin.categories.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Tambah Kategori</h3>
                </x-slot>

                <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <x-form-group>
                            <x-slot name="label">Nama Kategori</x-slot>
                            <x-text-input type="text" name="name" value="{{ old('name') }}" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Deskripsi</x-slot>
                            <x-textarea-input name="description" rows="4">{{ old('description') }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Gambar</x-slot>
                            <input type="file" name="image" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-primary-50 file:text-primary-700
                                          hover:file:bg-primary-100
                                          dark:file:bg-primary-900 dark:file:text-primary-300">
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Tipe</x-slot>
                            <select name="type" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                                <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Produk</option>
                                <option value="service" {{ old('type') === 'service' ? 'selected' : '' }}>Jasa</option>
                                <option value="both" {{ old('type') === 'both' ? 'selected' : '' }}>Keduanya</option>
                            </select>
                            <x-input-error :messages="$errors->get('type')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Urutan</x-slot>
                            <x-text-input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Urutan untuk sorting (angka lebih kecil muncul lebih dulu)</p>
                            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                        </x-form-group>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Aktif
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.categories.index') }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Simpan</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

