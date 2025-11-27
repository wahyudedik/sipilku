<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tambah Produk Baru
            </h2>
            <a href="{{ route('stores.products.index', $store) }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Tambah Produk</h3>
                </x-slot>

                <form action="{{ route('stores.products.store', $store) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Basic Information -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Informasi Dasar</h4>
                            <div class="space-y-4">
                                <x-form-group>
                                    <x-slot name="label">Nama Produk *</x-slot>
                                    <x-text-input type="text" name="name" value="{{ old('name') }}" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </x-form-group>

                                <x-form-group>
                                    <x-slot name="label">Deskripsi</x-slot>
                                    <x-textarea-input name="description" rows="4">{{ old('description') }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </x-form-group>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group>
                                        <x-slot name="label">SKU</x-slot>
                                        <x-text-input type="text" name="sku" value="{{ old('sku') }}" />
                                        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group>
                                        <x-slot name="label">Brand</x-slot>
                                        <x-text-input type="text" name="brand" value="{{ old('brand') }}" />
                                        <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                    </x-form-group>
                                </div>

                                <x-form-group>
                                    <x-slot name="label">Kategori</x-slot>
                                    <select name="store_category_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                        <option value="">Pilih Kategori</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->uuid }}" {{ old('store_category_id') === $category->uuid ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('store_category_id')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Harga & Stok</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <x-form-group>
                                        <x-slot name="label">Harga Normal *</x-slot>
                                        <x-text-input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required />
                                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group>
                                        <x-slot name="label">Harga Diskon</x-slot>
                                        <x-text-input type="number" name="discount_price" value="{{ old('discount_price') }}" step="0.01" min="0" />
                                        <x-input-error :messages="$errors->get('discount_price')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group>
                                        <x-slot name="label">Unit *</x-slot>
                                        <select name="unit" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900" required>
                                            <option value="pcs" {{ old('unit', 'pcs') === 'pcs' ? 'selected' : '' }}>Pcs</option>
                                            <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>Kg</option>
                                            <option value="m" {{ old('unit') === 'm' ? 'selected' : '' }}>Meter</option>
                                            <option value="m2" {{ old('unit') === 'm2' ? 'selected' : '' }}>M²</option>
                                            <option value="m3" {{ old('unit') === 'm3' ? 'selected' : '' }}>M³</option>
                                            <option value="pack" {{ old('unit') === 'pack' ? 'selected' : '' }}>Pack</option>
                                            <option value="box" {{ old('unit') === 'box' ? 'selected' : '' }}>Box</option>
                                        </select>
                                        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                                    </x-form-group>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group>
                                        <x-slot name="label">Stok</x-slot>
                                        <x-text-input type="number" name="stock" value="{{ old('stock', 0) }}" min="0" />
                                        <p class="text-xs text-gray-500 mt-1">Kosongkan untuk stok tidak terbatas</p>
                                        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group>
                                        <x-slot name="label">Minimal Pemesanan</x-slot>
                                        <x-text-input type="number" name="min_order" value="{{ old('min_order', 1) }}" min="1" />
                                        <x-input-error :messages="$errors->get('min_order')" class="mt-2" />
                                    </x-form-group>
                                </div>
                            </div>
                        </div>

                        <!-- Images -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Gambar Produk</h4>
                            <x-form-group>
                                <x-slot name="label">Upload Gambar (Maks 10)</x-slot>
                                <input type="file" name="images[]" multiple accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                <x-input-error :messages="$errors->get('images')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Specifications -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Spesifikasi (Opsional)</h4>
                            <div id="specifications-container" class="space-y-2">
                                <div class="flex gap-2">
                                    <x-text-input type="text" name="spec_key[]" placeholder="Nama (contoh: Dimensi)" class="flex-1" />
                                    <x-text-input type="text" name="spec_value[]" placeholder="Nilai (contoh: 10x20x30 cm)" class="flex-1" />
                                    <button type="button" onclick="removeSpec(this)" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                </div>
                            </div>
                            <button type="button" onclick="addSpec()" class="mt-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                + Tambah Spesifikasi
                            </button>
                        </div>

                        <!-- Status -->
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_active" id="is_active" value="1" 
                                       {{ old('is_active', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Aktifkan produk
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                                       {{ old('is_featured') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Tampilkan sebagai produk unggulan
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('stores.products.index', $store) }}">
                                <x-button variant="secondary" size="md" type="button">Batal</x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit">Simpan Produk</x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        function addSpec() {
            const container = document.getElementById('specifications-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" name="spec_key[]" placeholder="Nama" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                <input type="text" name="spec_value[]" placeholder="Nilai" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                <button type="button" onclick="removeSpec(this)" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
            `;
            container.appendChild(div);
        }

        function removeSpec(button) {
            button.parentElement.remove();
        }
    </script>
    @endpush
</x-app-layout>

