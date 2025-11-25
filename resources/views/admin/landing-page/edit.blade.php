<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Edit Landing Page
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.landing-page.index') }}" target="_blank">
                    <x-button variant="secondary" size="sm">Preview</x-button>
                </a>
                <a href="{{ route('admin.dashboard') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('admin.landing-page.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Hero Section -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Hero Section</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <x-form-group>
                            <x-slot name="label">Judul</x-slot>
                            <x-text-input type="text" name="hero_title" value="{{ old('hero_title', $settings['hero_title'] ?? '') }}" />
                        </x-form-group>
                        <x-form-group>
                            <x-slot name="label">Subtitle</x-slot>
                            <x-textarea-input name="hero_subtitle" rows="3">{{ old('hero_subtitle', $settings['hero_subtitle'] ?? '') }}</x-textarea-input>
                        </x-form-group>
                        <x-form-group>
                            <x-slot name="label">Gambar Hero</x-slot>
                            @if($settings['hero_image'] ?? null)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($settings['hero_image']) }}" alt="Hero" class="w-64 h-32 object-cover rounded-lg">
                                </div>
                            @endif
                            <input type="file" name="hero_image" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </x-form-group>
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-group>
                                <x-slot name="label">Teks Tombol</x-slot>
                                <x-text-input type="text" name="hero_button_text" value="{{ old('hero_button_text', $settings['hero_button_text'] ?? '') }}" />
                            </x-form-group>
                            <x-form-group>
                                <x-slot name="label">Link Tombol</x-slot>
                                <x-text-input type="text" name="hero_button_link" value="{{ old('hero_button_link', $settings['hero_button_link'] ?? '') }}" />
                            </x-form-group>
                        </div>
                    </div>
                </x-card>

                <!-- Features Section -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Features Section</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <x-form-group>
                            <x-slot name="label">Judul</x-slot>
                            <x-text-input type="text" name="features_title" value="{{ old('features_title', $settings['features_title'] ?? '') }}" />
                        </x-form-group>
                        <x-form-group>
                            <x-slot name="label">Subtitle</x-slot>
                            <x-textarea-input name="features_subtitle" rows="2">{{ old('features_subtitle', $settings['features_subtitle'] ?? '') }}</x-textarea-input>
                        </x-form-group>
                        <div id="featuresContainer" class="space-y-4">
                            @foreach(old('features', $settings['features'] ?? []) as $index => $feature)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg feature-item">
                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                        <x-form-group>
                                            <x-slot name="label">Judul Feature</x-slot>
                                            <x-text-input type="text" name="features[{{ $index }}][title]" value="{{ $feature['title'] ?? '' }}" required />
                                        </x-form-group>
                                        <x-form-group>
                                            <x-slot name="label">Deskripsi</x-slot>
                                            <x-text-input type="text" name="features[{{ $index }}][description]" value="{{ $feature['description'] ?? '' }}" />
                                        </x-form-group>
                                        <x-form-group>
                                            <x-slot name="label">Icon (Heroicon name)</x-slot>
                                            <x-text-input type="text" name="features[{{ $index }}][icon]" value="{{ $feature['icon'] ?? '' }}" placeholder="check-circle" />
                                        </x-form-group>
                                    </div>
                                    <button type="button" onclick="this.closest('.feature-item').remove()" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                                        Hapus Feature
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" onclick="addFeature()" class="text-primary-600 hover:text-primary-800 text-sm">
                            + Tambah Feature
                        </button>
                    </div>
                </x-card>

                <!-- About Section -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">About Section</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <x-form-group>
                            <x-slot name="label">Judul</x-slot>
                            <x-text-input type="text" name="about_title" value="{{ old('about_title', $settings['about_title'] ?? '') }}" />
                        </x-form-group>
                        <x-form-group>
                            <x-slot name="label">Konten</x-slot>
                            <x-textarea-input name="about_content" rows="4">{{ old('about_content', $settings['about_content'] ?? '') }}</x-textarea-input>
                        </x-form-group>
                        <x-form-group>
                            <x-slot name="label">Gambar About</x-slot>
                            @if($settings['about_image'] ?? null)
                                <div class="mb-2">
                                    <img src="{{ Storage::url($settings['about_image']) }}" alt="About" class="w-64 h-32 object-cover rounded-lg">
                                </div>
                            @endif
                            <input type="file" name="about_image" accept="image/*" 
                                   class="block w-full text-sm text-gray-500 dark:text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                        </x-form-group>
                    </div>
                </x-card>

                <!-- CTA Section -->
                <x-card class="mb-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Call to Action Section</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <x-form-group>
                            <x-slot name="label">Judul</x-slot>
                            <x-text-input type="text" name="cta_title" value="{{ old('cta_title', $settings['cta_title'] ?? '') }}" />
                        </x-form-group>
                        <x-form-group>
                            <x-slot name="label">Subtitle</x-slot>
                            <x-textarea-input name="cta_subtitle" rows="2">{{ old('cta_subtitle', $settings['cta_subtitle'] ?? '') }}</x-textarea-input>
                        </x-form-group>
                        <div class="grid grid-cols-2 gap-4">
                            <x-form-group>
                                <x-slot name="label">Teks Tombol</x-slot>
                                <x-text-input type="text" name="cta_button_text" value="{{ old('cta_button_text', $settings['cta_button_text'] ?? '') }}" />
                            </x-form-group>
                            <x-form-group>
                                <x-slot name="label">Link Tombol</x-slot>
                                <x-text-input type="text" name="cta_button_link" value="{{ old('cta_button_link', $settings['cta_button_link'] ?? '') }}" />
                            </x-form-group>
                        </div>
                    </div>
                </x-card>

                <div class="flex justify-end">
                    <x-button variant="primary" size="lg" type="submit">Simpan Perubahan</x-button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        let featureIndex = {{ count($settings['features'] ?? []) }};
        
        function addFeature() {
            const container = document.getElementById('featuresContainer');
            const featureDiv = document.createElement('div');
            featureDiv.className = 'p-4 border border-gray-200 dark:border-gray-700 rounded-lg feature-item';
            featureDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Judul Feature</label>
                        <input type="text" name="features[${featureIndex}][title]" required
                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                        <input type="text" name="features[${featureIndex}][description]"
                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Icon</label>
                        <input type="text" name="features[${featureIndex}][icon]" placeholder="check-circle"
                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
                <button type="button" onclick="this.closest('.feature-item').remove()" class="mt-2 text-red-600 hover:text-red-800 text-sm">
                    Hapus Feature
                </button>
            `;
            container.appendChild(featureDiv);
            featureIndex++;
        }
    </script>
    @endpush
</x-app-with-sidebar>

