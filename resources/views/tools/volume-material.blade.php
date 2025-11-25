<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Volume Material Calculator
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tools.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
                @auth
                    <a href="{{ route('tools.history', ['type' => 'volume_material']) }}">
                        <x-button variant="secondary" size="sm">Riwayat</x-button>
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Input Form -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Pilih Bentuk & Input Dimensi</h3>
                    </x-slot>
                    <form id="volumeForm">
                        <div class="space-y-6">
                            <!-- Shape Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bentuk Geometri</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="shape-option cursor-pointer p-4 border-2 border-gray-300 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors">
                                        <input type="radio" name="shape" value="rectangle" class="hidden" checked>
                                        <div class="text-center">
                                            <div class="text-2xl mb-2">▭</div>
                                            <div class="text-sm font-medium">Persegi Panjang</div>
                                        </div>
                                    </label>
                                    <label class="shape-option cursor-pointer p-4 border-2 border-gray-300 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors">
                                        <input type="radio" name="shape" value="circle" class="hidden">
                                        <div class="text-center">
                                            <div class="text-2xl mb-2">○</div>
                                            <div class="text-sm font-medium">Lingkaran</div>
                                        </div>
                                    </label>
                                    <label class="shape-option cursor-pointer p-4 border-2 border-gray-300 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors">
                                        <input type="radio" name="shape" value="triangle" class="hidden">
                                        <div class="text-center">
                                            <div class="text-2xl mb-2">△</div>
                                            <div class="text-sm font-medium">Segitiga</div>
                                        </div>
                                    </label>
                                    <label class="shape-option cursor-pointer p-4 border-2 border-gray-300 dark:border-gray-700 rounded-lg hover:border-primary-500 transition-colors">
                                        <input type="radio" name="shape" value="trapezoid" class="hidden">
                                        <div class="text-center">
                                            <div class="text-2xl mb-2">⏢</div>
                                            <div class="text-sm font-medium">Trapesium</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Dynamic Dimensions Input -->
                            <div id="dimensionsContainer">
                                <!-- Dimensions will be shown based on selected shape -->
                            </div>

                            <x-button type="button" variant="primary" size="lg" class="w-full" id="calculateBtn">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-5m-6 5h6m-6 0h-2a2 2 0 01-2-2V9a2 2 0 012-2h2m6 0h2a2 2 0 012 2v11a2 2 0 01-2 2h-2m-6-13V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                                </svg>
                                Hitung Volume
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Results -->
                <x-card id="resultsCard" class="hidden">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Hasil Perhitungan</h3>
                    </x-slot>
                    <div id="resultsContent"></div>
                    @auth
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <button type="button" id="saveBtn" class="w-full">
                                <x-button variant="primary" size="md" class="w-full">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                                    </svg>
                                    Simpan Perhitungan
                                </x-button>
                            </button>
                        </div>
                    @endauth
                </x-card>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('volumeForm');
            const dimensionsContainer = document.getElementById('dimensionsContainer');
            const calculateBtn = document.getElementById('calculateBtn');
            const resultsCard = document.getElementById('resultsCard');
            const resultsContent = document.getElementById('resultsContent');
            const saveBtn = document.getElementById('saveBtn');
            let currentResults = null;

            const shapeInputs = form.querySelectorAll('input[name="shape"]');
            shapeInputs.forEach(input => {
                input.addEventListener('change', updateDimensions);
            });

            // Initial load
            updateDimensions();

            function updateDimensions() {
                const selectedShape = form.querySelector('input[name="shape"]:checked').value;
                let html = '';

                switch(selectedShape) {
                    case 'rectangle':
                        html = `
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Panjang (m)</label>
                                    <input type="number" name="dimensions[length]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Lebar (m)</label>
                                    <input type="number" name="dimensions[width]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tinggi (m)</label>
                                    <input type="number" name="dimensions[height]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                            </div>
                        `;
                        break;
                    case 'circle':
                        html = `
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jari-jari (m)</label>
                                    <input type="number" name="dimensions[radius]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tinggi (m)</label>
                                    <input type="number" name="dimensions[height]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                            </div>
                        `;
                        break;
                    case 'triangle':
                        html = `
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Alas (m)</label>
                                    <input type="number" name="dimensions[base]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tinggi Segitiga (m)</label>
                                    <input type="number" name="dimensions[height]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Panjang (m)</label>
                                    <input type="number" name="dimensions[length]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                            </div>
                        `;
                        break;
                    case 'trapezoid':
                        html = `
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sisi Atas (m)</label>
                                    <input type="number" name="dimensions[top]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sisi Bawah (m)</label>
                                    <input type="number" name="dimensions[bottom]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tinggi (m)</label>
                                    <input type="number" name="dimensions[height]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Panjang (m)</label>
                                    <input type="number" name="dimensions[length]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                                </div>
                            </div>
                        `;
                        break;
                }

                dimensionsContainer.innerHTML = html;
            }

            // Update shape option styling
            shapeInputs.forEach(input => {
                input.addEventListener('change', function() {
                    document.querySelectorAll('.shape-option').forEach(opt => {
                        opt.classList.remove('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');
                    });
                    this.closest('.shape-option').classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');
                });
            });

            // Set initial selected
            document.querySelector('input[name="shape"]:checked').closest('.shape-option').classList.add('border-primary-500', 'bg-primary-50', 'dark:bg-primary-900');

            calculateBtn.addEventListener('click', function() {
                const formData = new FormData(form);
                const shape = formData.get('shape');
                const dimensions = {};

                formData.forEach((value, key) => {
                    if (key.startsWith('dimensions[')) {
                        const match = key.match(/dimensions\[(\w+)\]/);
                        if (match) {
                            dimensions[match[1]] = parseFloat(value);
                        }
                    }
                });

                fetch('{{ route("tools.volume-material.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ shape, dimensions })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentResults = data;
                        displayResults(data, shape);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghitung');
                });
            });

            function displayResults(data, shape) {
                const shapeNames = {
                    rectangle: 'Persegi Panjang',
                    circle: 'Lingkaran',
                    triangle: 'Segitiga',
                    trapezoid: 'Trapesium',
                };

                resultsContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="text-center p-6 bg-primary-50 dark:bg-primary-900 rounded-lg">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Bentuk: ${shapeNames[shape]}</p>
                            <p class="text-4xl font-bold text-primary-600 dark:text-primary-400">
                                ${data.volume.toLocaleString('id-ID', {minimumFractionDigits: 2, maximumFractionDigits: 2})}
                            </p>
                            <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">${data.unit}</p>
                        </div>
                    </div>
                `;
                resultsCard.classList.remove('hidden');
            }

            @auth
            saveBtn?.addEventListener('click', function() {
                if (!currentResults) {
                    alert('Silakan hitung terlebih dahulu');
                    return;
                }

                const title = prompt('Masukkan judul untuk perhitungan ini (opsional):') || 'Volume Material Calculator';
                const shape = form.querySelector('input[name="shape"]:checked').value;

                fetch('{{ route("tools.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'volume_material',
                        title: title,
                        inputs: { shape, dimensions: Object.fromEntries(new FormData(form).entries()) },
                        results: currentResults,
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Perhitungan berhasil disimpan!');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menyimpan');
                });
            });
            @endauth
        });
    </script>
    @endpush
</x-app-layout>

