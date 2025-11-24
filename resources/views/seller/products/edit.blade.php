<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Edit Produk: {{ $product->title }}
        </h2>
    </x-slot>

    <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Informasi Produk</h3>
                    </x-slot>

                    <x-form-group label="Judul Produk" name="title" required>
                        <x-text-input name="title" value="{{ old('title', $product->title) }}" placeholder="Contoh: Template RAB Proyek Gedung" />
                    </x-form-group>

                    <x-form-group label="Slug (URL)" name="slug" help="Akan di-generate otomatis dari judul jika dikosongkan">
                        <x-text-input name="slug" value="{{ old('slug', $product->slug) }}" placeholder="template-rab-proyek-gedung" />
                    </x-form-group>

                    <x-form-group label="Kategori" name="category_id">
                        <x-select-input name="category_id" :options="$categories->pluck('name', 'id')->toArray()" placeholder="Pilih kategori" value="{{ old('category_id', $product->category_id) }}" />
                    </x-form-group>

                    <x-form-group label="Deskripsi Singkat" name="short_description" help="Maksimal 500 karakter">
                        <x-textarea-input name="short_description" rows="3">{{ old('short_description', $product->short_description) }}</x-textarea-input>
                    </x-form-group>

                    <x-form-group label="Deskripsi Lengkap" name="description" required help="Minimal 50 karakter">
                        <x-textarea-input name="description" rows="8">{{ old('description', $product->description) }}</x-textarea-input>
                    </x-form-group>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Harga</h3>
                    </x-slot>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-group label="Harga Normal" name="price" required>
                            <x-text-input name="price" type="number" step="0.01" value="{{ old('price', $product->price) }}" placeholder="0" />
                        </x-form-group>

                        <x-form-group label="Harga Diskon (Opsional)" name="discount_price" help="Harus lebih kecil dari harga normal">
                            <x-text-input name="discount_price" type="number" step="0.01" value="{{ old('discount_price', $product->discount_price) }}" placeholder="0" />
                        </x-form-group>
                    </div>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">File Produk</h3>
                    </x-slot>

                    @if($product->file_path)
                        <div class="mb-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400">File saat ini:</p>
                            <p class="font-medium">{{ $product->file_name }}</p>
                            <p class="text-xs text-gray-500">{{ number_format($product->file_size / 1024 / 1024, 2) }} MB</p>
                        </div>
                    @endif

                    <x-form-group label="Ubah File Produk (Opsional)" name="file" help="Format: ZIP, RAR, PDF, DOC, XLS, DWG, SKP, RVT. Maksimal 100MB">
                        <input type="file" 
                               name="file" 
                               accept=".zip,.rar,.7z,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.dwg,.skp,.rvt"
                               class="block w-full text-sm text-gray-500 dark:text-gray-400
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-primary-50 file:text-primary-700
                                      hover:file:bg-primary-100
                                      dark:file:bg-primary-900 dark:file:text-primary-200">
                    </x-form-group>
                </x-card>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Gambar Preview</h3>
                    </x-slot>

                    @if($product->preview_image)
                        <div class="mb-4">
                            <img src="{{ Storage::url($product->preview_image) }}" alt="Current preview" class="w-full h-48 object-cover rounded-lg">
                        </div>
                    @endif

                    <x-form-group label="Ubah Gambar Preview (Opsional)" name="preview_image" help="Format: JPG, PNG, GIF, WEBP. Maksimal 2MB">
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

                    @if($product->gallery_images && count($product->gallery_images) > 0)
                        <div class="mb-4 grid grid-cols-2 gap-2">
                            @foreach($product->gallery_images as $image)
                                <div class="relative">
                                    <img src="{{ Storage::url($image) }}" alt="Gallery" class="w-full h-24 object-cover rounded-lg">
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <x-form-group label="Tambah Galeri (Maksimal 5)" name="gallery_images" help="Format: JPG, PNG, GIF, WEBP. Maksimal 2MB per gambar">
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

                @if($product->status === 'rejected' && $product->rejection_reason)
                    <x-alert type="error">
                        <strong>Produk ditolak:</strong> {{ $product->rejection_reason }}
                    </x-alert>
                @endif

                <div class="flex space-x-3">
                    <a href="{{ route('seller.products.index') }}" class="flex-1">
                        <x-button variant="secondary" size="md" type="button" class="w-full">
                            Batal
                        </x-button>
                    </a>
                    <x-button variant="primary" size="md" type="submit" class="flex-1">
                        Update Produk
                    </x-button>
                </div>
            </div>
        </div>
    </form>

    @push('scripts')
    <script>
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
            
            files.slice(0, 5).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const div = document.createElement('div');
                    div.className = 'relative';
                    div.innerHTML = `
                        <img src="${e.target.result}" alt="Preview" class="w-full h-24 object-cover rounded-lg">
                        <button type="button" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1" onclick="this.parentElement.remove()">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    `;
                    preview.appendChild(div);
                }
                reader.readAsDataURL(file);
            });
        });
    </script>
    @endpush
</x-app-with-sidebar>

