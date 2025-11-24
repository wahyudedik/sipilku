<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Tambah Jasa Baru
        </h2>
    </x-slot>

    <form action="{{ route('seller.services.store') }}" method="POST" enctype="multipart/form-data" id="serviceForm">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Informasi Jasa</h3>
                    </x-slot>

                    <x-form-group label="Judul Jasa" name="title" required>
                        <x-text-input name="title" value="{{ old('title') }}" placeholder="Contoh: Jasa Desain Struktur Gedung" />
                    </x-form-group>

                    <x-form-group label="Slug (URL)" name="slug" help="Akan di-generate otomatis dari judul jika dikosongkan">
                        <x-text-input name="slug" value="{{ old('slug') }}" placeholder="jasa-desain-struktur-gedung" />
                    </x-form-group>

                    <x-form-group label="Kategori" name="category_id">
                        <x-select-input name="category_id" :options="$categories->pluck('name', 'id')->toArray()" placeholder="Pilih kategori" value="{{ old('category_id') }}" />
                    </x-form-group>

                    <x-form-group label="Deskripsi Singkat" name="short_description" help="Maksimal 500 karakter">
                        <x-textarea-input name="short_description" rows="3">{{ old('short_description') }}</x-textarea-input>
                    </x-form-group>

                    <x-form-group label="Deskripsi Lengkap" name="description" required help="Minimal 50 karakter">
                        <x-textarea-input name="description" rows="8">{{ old('description') }}</x-textarea-input>
                    </x-form-group>
                </x-card>

                <!-- Package Pricing -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">Paket Harga</h3>
                            <button type="button" id="addPackageBtn" class="text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                + Tambah Paket
                            </button>
                        </div>
                    </x-slot>

                    <div id="packagesContainer" class="space-y-4">
                        <!-- Package will be added here dynamically -->
                    </div>
                </x-card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Gambar Preview</h3>
                    </x-slot>

                    <x-form-group label="Gambar Preview" name="preview_image" required help="Format: JPG, PNG, GIF, WEBP. Maksimal 2MB">
                        <input type="file" 
                               name="preview_image" 
                               accept="image/*"
                               id="preview_image"
                               class="block w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-primary-50 file:text-primary-700
                                      hover:file:bg-primary-100
                                      dark:file:bg-primary-900 dark:file:text-primary-200">
                        <div id="preview_image_preview" class="mt-4 hidden">
                            <img id="preview_image_display" src="" alt="Preview" class="w-full h-48 object-cover rounded-lg">
                        </div>
                    </x-form-group>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Galeri Gambar</h3>
                    </x-slot>

                    <x-form-group label="Galeri (Maksimal 10)" name="gallery_images" help="Format: JPG, PNG, GIF, WEBP. Maksimal 2MB per gambar">
                        <input type="file" 
                               name="gallery_images[]" 
                               accept="image/*"
                               multiple
                               id="gallery_images"
                               class="block w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-primary-50 file:text-primary-700
                                      hover:file:bg-primary-100
                                      dark:file:bg-primary-900 dark:file:text-primary-200">
                        <div id="gallery_preview" class="mt-4 grid grid-cols-2 gap-2"></div>
                    </x-form-group>
                </x-card>

                <div class="flex space-x-3">
                    <a href="{{ route('seller.services.index') }}" class="flex-1">
                        <x-button variant="secondary" size="md" type="button" class="w-full">
                            Batal
                        </x-button>
                    </a>
                    <x-button variant="primary" size="md" type="submit" class="flex-1">
                        Simpan Jasa
                    </x-button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
        let packageCount = 0;

        function addPackage() {
            const container = document.getElementById('packagesContainer');
            const packageDiv = document.createElement('div');
            packageDiv.className = 'border border-gray-200 dark:border-gray-700 rounded-lg p-4';
            packageDiv.id = `package-${packageCount}`;
            packageDiv.innerHTML = `
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-medium">Paket ${packageCount + 1}</h4>
                                        <button type="button" data-remove-package="${packageCount}" class="remove-package-btn text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Paket *</label>
                        <input type="text" name="package_prices[${packageCount}][name]" required
                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                               placeholder="Contoh: Paket Basic">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga *</label>
                        <input type="number" name="package_prices[${packageCount}][price]" required step="0.01" min="0"
                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                               placeholder="0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                        <textarea name="package_prices[${packageCount}][description]" rows="2"
                                  class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                                  placeholder="Fitur yang termasuk dalam paket ini..."></textarea>
                    </div>
                </div>
            `;
            container.appendChild(packageDiv);
            packageCount++;
        }

        function removePackage(id) {
            const packageDiv = document.getElementById(`package-${id}`);
            if (packageDiv) {
                packageDiv.remove();
            }
        }

        // Add event listener for add package button
        document.addEventListener('DOMContentLoaded', function() {
            // Add first package on load
            addPackage();
            
            // Add event listener for add package button
            const addPackageBtn = document.getElementById('addPackageBtn');
            if (addPackageBtn) {
                addPackageBtn.addEventListener('click', addPackage);
            }
            
            // Use event delegation for remove buttons (since they're added dynamically)
            document.getElementById('packagesContainer').addEventListener('click', function(e) {
                if (e.target.closest('.remove-package-btn')) {
                    const btn = e.target.closest('.remove-package-btn');
                    const id = btn.getAttribute('data-remove-package');
                    removePackage(id);
                }
            });
        });

        // Preview image handler
        document.getElementById('preview_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview_image_display').src = e.target.result;
                    document.getElementById('preview_image_preview').classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });

        // Gallery images handler
        document.getElementById('gallery_images').addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const preview = document.getElementById('gallery_preview');
            preview.innerHTML = '';
            
            files.slice(0, 10).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg">
                    `;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
    @endpush
</x-app-with-sidebar>

