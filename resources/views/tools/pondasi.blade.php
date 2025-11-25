<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pondasi Calculator
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tools.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
                @auth
                    <a href="{{ route('tools.history', ['type' => 'pondasi']) }}">
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
                        <h3 class="text-lg font-medium">Input Data Pondasi</h3>
                    </x-slot>
                    <form id="pondasiForm" class="space-y-6">
                        <!-- Type Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jenis Pondasi</label>
                            <div class="space-y-2">
                                <label class="flex items-center p-3 border-2 border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                                    <input type="radio" name="type" value="footing" class="mr-3" checked>
                                    <div>
                                        <div class="font-medium">Footing (Pondasi Telapak)</div>
                                        <div class="text-xs text-gray-500">Untuk kolom tunggal</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                                    <input type="radio" name="type" value="strip" class="mr-3">
                                    <div>
                                        <div class="font-medium">Strip Foundation (Pondasi Menerus)</div>
                                        <div class="text-xs text-gray-500">Untuk dinding</div>
                                    </div>
                                </label>
                                <label class="flex items-center p-3 border-2 border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer hover:border-primary-500 transition-colors">
                                    <input type="radio" name="type" value="raft" class="mr-3">
                                    <div>
                                        <div class="font-medium">Raft Foundation (Pondasi Raft)</div>
                                        <div class="text-xs text-gray-500">Pondasi pelat</div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <!-- Dynamic Dimensions -->
                        <div id="dimensionsContainer">
                            <!-- Dimensions will be shown based on selected type -->
                        </div>

                        <!-- Material Prices -->
                        <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                            <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Harga Material</h4>
                            <div class="space-y-4">
                                <x-form-group>
                                    <x-slot name="label">Harga Beton per m³ (Rp)</x-slot>
                                    <x-text-input type="number" name="concrete_price" step="0.01" min="0" placeholder="0" required />
                                </x-form-group>
                                <x-form-group>
                                    <x-slot name="label">Harga Besi per kg (Rp)</x-slot>
                                    <x-text-input type="number" name="rebar_price" step="0.01" min="0" placeholder="0" required />
                                </x-form-group>
                            </div>
                        </div>

                        <x-button type="button" variant="primary" size="lg" class="w-full" id="calculateBtn">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-5m-6 5h6m-6 0h-2a2 2 0 01-2-2V9a2 2 0 012-2h2m6 0h2a2 2 0 012 2v11a2 2 0 01-2 2h-2m-6-13V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                            </svg>
                            Hitung Pondasi
                        </x-button>
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
            const form = document.getElementById('pondasiForm');
            const dimensionsContainer = document.getElementById('dimensionsContainer');
            const calculateBtn = document.getElementById('calculateBtn');
            const resultsCard = document.getElementById('resultsCard');
            const resultsContent = document.getElementById('resultsContent');
            const saveBtn = document.getElementById('saveBtn');
            let currentResults = null;

            const typeInputs = form.querySelectorAll('input[name="type"]');
            typeInputs.forEach(input => {
                input.addEventListener('change', updateDimensions);
            });

            updateDimensions();

            function updateDimensions() {
                const selectedType = form.querySelector('input[name="type"]:checked').value;
                let html = '';

                switch(selectedType) {
                    case 'footing':
                        html = `
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Dimensi Footing</h4>
                                <div class="space-y-4">
                                    <x-form-group>
                                        <x-slot name="label">Panjang (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[length]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                    <x-form-group>
                                        <x-slot name="label">Lebar (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[width]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                    <x-form-group>
                                        <x-slot name="label">Tinggi (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[height]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                    <x-form-group>
                                        <x-slot name="label">Jumlah Footing</x-slot>
                                        <x-text-input type="number" name="dimensions[count]" min="1" value="1" required />
                                    </x-form-group>
                                </div>
                            </div>
                        `;
                        break;
                    case 'strip':
                        html = `
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Dimensi Strip</h4>
                                <div class="space-y-4">
                                    <x-form-group>
                                        <x-slot name="label">Panjang (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[length]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                    <x-form-group>
                                        <x-slot name="label">Lebar (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[width]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                    <x-form-group>
                                        <x-slot name="label">Tinggi (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[height]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                </div>
                            </div>
                        `;
                        break;
                    case 'raft':
                        html = `
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">Dimensi Raft</h4>
                                <div class="space-y-4">
                                    <x-form-group>
                                        <x-slot name="label">Panjang (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[length]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                    <x-form-group>
                                        <x-slot name="label">Lebar (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[width]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                    <x-form-group>
                                        <x-slot name="label">Ketebalan (m)</x-slot>
                                        <x-text-input type="number" name="dimensions[thickness]" step="0.01" min="0" placeholder="0" required />
                                    </x-form-group>
                                </div>
                            </div>
                        `;
                        break;
                }

                dimensionsContainer.innerHTML = html;
            }

            calculateBtn.addEventListener('click', function() {
                const formData = new FormData(form);
                const type = formData.get('type');
                const dimensions = {};

                formData.forEach((value, key) => {
                    if (key.startsWith('dimensions[')) {
                        const match = key.match(/dimensions\[(\w+)\]/);
                        if (match) {
                            dimensions[match[1]] = parseFloat(value) || parseInt(value);
                        }
                    }
                });

                const data = {
                    type,
                    dimensions,
                    concrete_price: parseFloat(formData.get('concrete_price')),
                    rebar_price: parseFloat(formData.get('rebar_price')),
                };

                fetch('{{ route("tools.pondasi.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        currentResults = data;
                        displayResults(data, type);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghitung');
                });
            });

            function displayResults(data, type) {
                const typeNames = {
                    footing: 'Footing (Pondasi Telapak)',
                    strip: 'Strip Foundation (Pondasi Menerus)',
                    raft: 'Raft Foundation (Pondasi Raft)',
                };

                resultsContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Jenis: ${typeNames[type]}</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Volume Beton:</span>
                                    <span class="font-medium">${data.volume.toLocaleString('id-ID', {minimumFractionDigits: 2})} m³</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Berat Besi:</span>
                                    <span class="font-medium">${data.rebar_weight.toLocaleString('id-ID', {minimumFractionDigits: 2})} kg</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Rincian Biaya</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Biaya Beton:</span>
                                    <span class="font-medium">Rp ${data.concrete_cost.toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Biaya Besi:</span>
                                    <span class="font-medium">Rp ${data.rebar_cost.toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Total Biaya:</span>
                                    <span class="font-bold text-primary-600 dark:text-primary-400 text-lg">Rp ${data.total_cost.toLocaleString('id-ID')}</span>
                                </div>
                            </div>
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

                const title = prompt('Masukkan judul untuk perhitungan ini (opsional):') || 'Pondasi Calculator';
                const formData = new FormData(form);
                const type = formData.get('type');
                const dimensions = {};

                formData.forEach((value, key) => {
                    if (key.startsWith('dimensions[')) {
                        const match = key.match(/dimensions\[(\w+)\]/);
                        if (match) {
                            dimensions[match[1]] = parseFloat(value) || parseInt(value);
                        }
                    }
                });

                fetch('{{ route("tools.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'pondasi',
                        title: title,
                        inputs: {
                            type,
                            dimensions,
                            concrete_price: parseFloat(formData.get('concrete_price')),
                            rebar_price: parseFloat(formData.get('rebar_price')),
                        },
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

