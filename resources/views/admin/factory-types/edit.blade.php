<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Edit Tipe Pabrik
            </h2>
            <a href="{{ route('admin.factory-types.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Edit Tipe Pabrik</h3>
                </x-slot>

                <form action="{{ route('admin.factory-types.update', $factoryType) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <x-form-group>
                            <x-slot name="label">Nama Tipe Pabrik</x-slot>
                            <x-text-input type="text" name="name" value="{{ old('name', $factoryType->name) }}" placeholder="e.g., Pabrik Beton, Pabrik Bata" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Deskripsi</x-slot>
                            <x-textarea-input name="description" rows="4" placeholder="Deskripsi tipe pabrik...">{{ old('description', $factoryType->description) }}</x-textarea-input>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Gambar</x-slot>
                            @if($factoryType->image)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($factoryType->image) }}" alt="{{ $factoryType->name }}" class="w-32 h-32 object-cover rounded">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Gambar saat ini</p>
                                </div>
                            @endif
                            <input type="file" name="image" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-primary-50 file:text-primary-700
                                          hover:file:bg-primary-100
                                          dark:file:bg-primary-900 dark:file:text-primary-300">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah gambar</p>
                            <x-input-error :messages="$errors->get('image')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Icon</x-slot>
                            @if($factoryType->icon)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($factoryType->icon) }}" alt="{{ $factoryType->name }}" class="w-16 h-16 object-cover rounded">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Icon saat ini</p>
                                </div>
                            @endif
                            <input type="file" name="icon" accept="image/*,.svg" 
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400
                                          file:mr-4 file:py-2 file:px-4
                                          file:rounded-md file:border-0
                                          file:text-sm file:font-semibold
                                          file:bg-primary-50 file:text-primary-700
                                          hover:file:bg-primary-100
                                          dark:file:bg-primary-900 dark:file:text-primary-300">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan jika tidak ingin mengubah icon</p>
                            <x-input-error :messages="$errors->get('icon')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Default Units</x-slot>
                            <div id="unitsContainer" class="space-y-2">
                                @if($factoryType->default_units && count($factoryType->default_units) > 0)
                                    @foreach($factoryType->default_units as $index => $unit)
                                        <div class="flex items-center space-x-2">
                                            <x-text-input type="text" name="default_units[{{ $index }}]" value="{{ $unit }}" placeholder="e.g., m3, kg, pcs" />
                                            <button type="button" onclick="removeUnit(this)" class="text-red-600 hover:text-red-800">×</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex items-center space-x-2">
                                        <x-text-input type="text" name="default_units[0]" placeholder="e.g., m3, kg, pcs" />
                                        <button type="button" onclick="removeUnit(this)" class="text-red-600 hover:text-red-800">×</button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" onclick="addUnit()" class="mt-2 text-sm text-primary-600 hover:text-primary-800">
                                + Tambah Unit
                            </button>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Unit default untuk produk tipe pabrik ini (m3, kg, pcs, dll)</p>
                            <x-input-error :messages="$errors->get('default_units')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Specifications Template (JSON)</x-slot>
                            <x-textarea-input name="specifications_template" rows="6" placeholder='{"grade": "K-300", "slump": "12cm", "strength": "30 MPa"}'>{{ old('specifications_template', $factoryType->specifications_template ? json_encode($factoryType->specifications_template, JSON_PRETTY_PRINT) : '') }}</x-textarea-input>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Template spesifikasi dalam format JSON (opsional)</p>
                            <x-input-error :messages="$errors->get('specifications_template')" class="mt-2" />
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Urutan</x-slot>
                            <x-text-input type="number" name="sort_order" value="{{ old('sort_order', $factoryType->sort_order) }}" min="0" />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Urutan untuk sorting (angka lebih kecil muncul lebih dulu)</p>
                            <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                        </x-form-group>

                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', $factoryType->is_active) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                Aktif
                            </label>
                        </div>

                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('admin.factory-types.index') }}">
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
    <script>
        let unitCount = {{ $factoryType->default_units ? count($factoryType->default_units) : 1 }};
        function addUnit() {
            const container = document.getElementById('unitsContainer');
            const newUnit = document.createElement('div');
            newUnit.className = 'flex items-center space-x-2';
            newUnit.innerHTML = `
                <x-text-input type="text" name="default_units[${unitCount}]" placeholder="e.g., m3, kg, pcs" />
                <button type="button" onclick="removeUnit(this)" class="text-red-600 hover:text-red-800">×</button>
            `;
            container.appendChild(newUnit);
            unitCount++;
        }
        function removeUnit(btn) {
            btn.parentElement.remove();
        }
    </script>
    @endpush
</x-app-with-sidebar>

