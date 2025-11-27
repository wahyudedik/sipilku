<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Tambah Produk Baru
            </h2>
            <a href="{{ route('factories.products.index', $factory) }}">
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

                <form action="{{ route('factories.products.store', $factory) }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        <!-- Factory Type Info -->
                        @if($factoryType && $typeConfig)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                                <p class="text-sm text-blue-800 dark:text-blue-200">
                                    <strong>Tipe Pabrik:</strong> {{ $typeConfig['name'] }}
                                    @if(isset($typeConfig['pricing_notes']))
                                        <br><strong>Catatan Harga:</strong> {{ $typeConfig['pricing_notes'] }}
                                    @endif
                                </p>
                            </div>
                        @endif

                        <!-- Basic Information -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Informasi Dasar</h4>
                            <div class="space-y-4">
                                @if(count($productCategories) > 0)
                                    <x-form-group label="Kategori Produk" name="product_category">
                                        <x-select-input name="product_category" id="product_category" onchange="updateProductNameSuggestions()">
                                            <option value="">Pilih Kategori</option>
                                            @foreach($productCategories as $key => $label)
                                                <option value="{{ $key }}" {{ old('product_category') === $key ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </x-select-input>
                                        <x-input-error :messages="$errors->get('product_category')" class="mt-2" />
                                    </x-form-group>
                                @endif

                                <x-form-group label="Nama Produk *" name="name" required>
                                    <x-text-input type="text" name="name" id="product_name" value="{{ old('name') }}" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                    <div id="name-suggestions" class="mt-2 space-y-1"></div>
                                </x-form-group>

                                <x-form-group label="Deskripsi" name="description">
                                    <x-textarea-input name="description" rows="4">{{ old('description') }}</x-textarea-input>
                                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                                </x-form-group>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <x-form-group label="SKU" name="sku">
                                        <x-text-input type="text" name="sku" value="{{ old('sku') }}" />
                                        <x-input-error :messages="$errors->get('sku')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group label="Kode Produk Pabrik" name="code">
                                        <x-text-input type="text" name="code" value="{{ old('code') }}" />
                                        <x-input-error :messages="$errors->get('code')" class="mt-2" />
                                    </x-form-group>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing & Units -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Harga & Unit</h4>
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <x-form-group label="Harga Normal *" name="price" required>
                                        <x-text-input type="number" name="price" value="{{ old('price') }}" step="0.01" min="0" required />
                                        <x-input-error :messages="$errors->get('price')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group label="Harga Diskon" name="discount_price">
                                        <x-text-input type="number" name="discount_price" value="{{ old('discount_price') }}" step="0.01" min="0" />
                                        <x-input-error :messages="$errors->get('discount_price')" class="mt-2" />
                                    </x-form-group>

                                    <x-form-group label="Unit Utama *" name="unit" required>
                                        <x-select-input name="unit" required>
                                            @if(count($defaultUnits) > 0)
                                                @foreach($defaultUnits as $unit)
                                                    <option value="{{ $unit }}" {{ old('unit', $defaultUnits[0]) === $unit ? 'selected' : '' }}>
                                                        {{ strtoupper($unit) }}
                                                    </option>
                                                @endforeach
                                            @endif
                                            <!-- Additional common units -->
                                            <option value="pcs" {{ old('unit') === 'pcs' ? 'selected' : '' }}>Pcs</option>
                                            <option value="kg" {{ old('unit') === 'kg' ? 'selected' : '' }}>Kg</option>
                                            <option value="ton" {{ old('unit') === 'ton' ? 'selected' : '' }}>Ton</option>
                                            <option value="m" {{ old('unit') === 'm' ? 'selected' : '' }}>Meter</option>
                                            <option value="m2" {{ old('unit') === 'm2' ? 'selected' : '' }}>M²</option>
                                            <option value="m3" {{ old('unit') === 'm3' ? 'selected' : '' }}>M³</option>
                                            <option value="pack" {{ old('unit') === 'pack' ? 'selected' : '' }}>Pack</option>
                                            <option value="box" {{ old('unit') === 'box' ? 'selected' : '' }}>Box</option>
                                            <option value="unit" {{ old('unit') === 'unit' ? 'selected' : '' }}>Unit</option>
                                            <option value="mobil" {{ old('unit') === 'mobil' ? 'selected' : '' }}>Mobil</option>
                                            <option value="kubik" {{ old('unit') === 'kubik' ? 'selected' : '' }}>Kubik</option>
                                        </x-select-input>
                                        <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                                        @if(count($defaultUnits) > 0)
                                            <p class="text-xs text-gray-500 mt-1">Unit yang disarankan untuk {{ $typeConfig['name'] ?? 'tipe pabrik ini' }}</p>
                                        @endif
                                    </x-form-group>
                                </div>

                                <x-form-group label="Unit Alternatif (Opsional)" name="available_units">
                                    <div class="space-y-2" id="available-units-container">
                                        <div class="flex gap-2">
                                            <x-text-input type="text" name="available_units[]" placeholder="m3, m2, kg, dll" class="flex-1" />
                                            <button type="button" onclick="removeUnit(this)" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                        </div>
                                    </div>
                                    <button type="button" onclick="addUnit()" class="mt-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                        + Tambah Unit
                                    </button>
                                    <p class="text-xs text-gray-500 mt-1">Unit alternatif yang tersedia untuk produk ini</p>
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Stock Management -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Stok & Pemesanan</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <x-form-group label="Stok" name="stock">
                                    <x-text-input type="number" name="stock" value="{{ old('stock') }}" min="0" />
                                    <p class="text-xs text-gray-500 mt-1">Kosongkan untuk stok tidak terbatas</p>
                                    <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                                </x-form-group>

                                <x-form-group label="Minimal Pemesanan" name="min_order">
                                    <x-text-input type="number" name="min_order" value="{{ old('min_order', 1) }}" min="1" />
                                    <x-input-error :messages="$errors->get('min_order')" class="mt-2" />
                                </x-form-group>
                            </div>
                        </div>

                        <!-- Quality Grade -->
                        @if($factoryType && isset($typeConfig['quality_grades']) && count($typeConfig['quality_grades']) > 0)
                            <div>
                                <h4 class="text-md font-medium mb-4">Grade Kualitas</h4>
                                <div class="space-y-4">
                                    <x-form-group label="Grade" name="quality_grade[grade]">
                                        <x-select-input name="quality_grade[grade]">
                                            <option value="">Pilih Grade</option>
                                            @foreach($typeConfig['quality_grades'] as $value => $label)
                                                <option value="{{ $value }}" {{ old('quality_grade.grade') === $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </x-select-input>
                                    </x-form-group>

                                    <x-form-group label="Nilai Grade" name="quality_grade[value]">
                                        <x-text-input type="text" name="quality_grade[value]" value="{{ old('quality_grade.value') }}" placeholder="Contoh: fc' = 21.7 MPa" />
                                    </x-form-group>

                                    <x-form-group label="Deskripsi Grade" name="quality_grade[description]">
                                        <x-textarea-input name="quality_grade[description]" rows="2">{{ old('quality_grade.description') }}</x-textarea-input>
                                    </x-form-group>
                                </div>
                            </div>
                        @endif

                        <!-- Images -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Galeri Gambar Produk</h4>
                            <x-form-group label="Upload Gambar (Maks 10)" name="images">
                                <input type="file" name="images[]" multiple accept="image/*" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                <p class="text-xs text-gray-500 mt-1">Maksimal 5MB per gambar</p>
                                <x-input-error :messages="$errors->get('images')" class="mt-2" />
                            </x-form-group>
                        </div>

                        <!-- Specifications -->
                        <div>
                            <h4 class="text-md font-medium mb-4">Spesifikasi Teknis</h4>
                            @if(count($specificationsTemplate) > 0)
                                <p class="text-xs text-gray-500 mb-2">Template spesifikasi untuk {{ $typeConfig['name'] ?? 'tipe pabrik ini' }}:</p>
                            @endif
                            <div id="specifications-container" class="space-y-2">
                                @if(count($specificationsTemplate) > 0)
                                    @foreach($specificationsTemplate as $key => $value)
                                        <div class="flex gap-2">
                                            <x-text-input type="text" name="spec_key[]" value="{{ $key }}" class="flex-1" />
                                            <x-text-input type="text" name="spec_value[]" value="{{ old('spec_value.' . $loop->index) }}" placeholder="Masukkan nilai" class="flex-1" />
                                            <button type="button" onclick="removeSpec(this)" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex gap-2">
                                        <x-text-input type="text" name="spec_key[]" placeholder="Nama (contoh: Dimensi, Berat, dll)" class="flex-1" />
                                        <x-text-input type="text" name="spec_value[]" placeholder="Nilai (contoh: 10x20x30 cm)" class="flex-1" />
                                        <button type="button" onclick="removeSpec(this)" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addSpec()" class="mt-2 px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded hover:bg-gray-300 dark:hover:bg-gray-600">
                                + Tambah Spesifikasi
                            </button>
                        </div>

                        <!-- Status -->
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center">
                                <input type="checkbox" name="is_available" id="is_available" value="1" 
                                       {{ old('is_available', true) ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_available" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Produk Tersedia
                                </label>
                            </div>
                            <div class="flex items-center">
                                <input type="checkbox" name="is_featured" id="is_featured" value="1" 
                                       {{ old('is_featured') ? 'checked' : '' }}
                                       class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                                <label for="is_featured" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                    Featured Product
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('factories.products.index', $factory) }}">
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

        function addUnit() {
            const container = document.getElementById('available-units-container');
            const div = document.createElement('div');
            div.className = 'flex gap-2';
            div.innerHTML = `
                <input type="text" name="available_units[]" placeholder="m3, m2, kg, dll" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                <button type="button" onclick="removeUnit(this)" class="px-3 py-2 bg-red-600 text-white rounded hover:bg-red-700">Hapus</button>
            `;
            container.appendChild(div);
        }

        function removeUnit(button) {
            button.parentElement.remove();
        }

        function updateProductNameSuggestions() {
            const category = document.getElementById('product_category').value;
            const suggestionsDiv = document.getElementById('name-suggestions');
            
            // This would need to be implemented with AJAX or passed from server
            // For now, we'll just show a message
            if (category) {
                suggestionsDiv.innerHTML = '<p class="text-xs text-gray-500">Pilih kategori untuk melihat saran nama produk</p>';
            } else {
                suggestionsDiv.innerHTML = '';
            }
        }
    </script>
    @endpush
</x-app-layout>

