<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Overhead & Profit Calculator
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tools.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
                @auth
                    <a href="{{ route('tools.history', ['type' => 'overhead_profit']) }}">
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
                        <h3 class="text-lg font-medium">Input Data</h3>
                    </x-slot>
                    <form id="overheadForm" class="space-y-6">
                        <x-form-group>
                            <x-slot name="label">Biaya Langsung (Rp)</x-slot>
                            <x-text-input type="number" name="direct_cost" step="0.01" min="0" placeholder="0" required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Total biaya material, tenaga kerja, dan peralatan langsung
                            </p>
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Persentase Overhead (%)</x-slot>
                            <x-text-input type="number" name="overhead_percentage" step="0.01" min="0" max="100" placeholder="0" required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Biaya tidak langsung (administrasi, sewa, dll)
                            </p>
                        </x-form-group>

                        <x-form-group>
                            <x-slot name="label">Persentase Profit (%)</x-slot>
                            <x-text-input type="number" name="profit_percentage" step="0.01" min="0" max="100" placeholder="0" required />
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                Keuntungan yang diinginkan dari proyek
                            </p>
                        </x-form-group>

                        <x-button type="button" variant="primary" size="lg" class="w-full" id="calculateBtn">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-5m-6 5h6m-6 0h-2a2 2 0 01-2-2V9a2 2 0 012-2h2m6 0h2a2 2 0 012 2v11a2 2 0 01-2 2h-2m-6-13V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                            </svg>
                            Hitung Overhead & Profit
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

            <!-- Info Card -->
            <x-card class="mt-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Penjelasan</h3>
                </x-slot>
                <div class="space-y-3 text-sm text-gray-600 dark:text-gray-400">
                    <div>
                        <strong class="text-gray-900 dark:text-gray-100">Biaya Langsung:</strong> Total biaya yang langsung terkait dengan pelaksanaan proyek, seperti material, tenaga kerja, dan peralatan.
                    </div>
                    <div>
                        <strong class="text-gray-900 dark:text-gray-100">Overhead:</strong> Biaya tidak langsung yang diperlukan untuk menjalankan proyek, seperti administrasi, sewa kantor, asuransi, dan biaya umum lainnya.
                    </div>
                    <div>
                        <strong class="text-gray-900 dark:text-gray-100">Profit:</strong> Keuntungan yang diinginkan dari proyek. Biasanya dihitung dari subtotal (biaya langsung + overhead).
                    </div>
                </div>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('overheadForm');
            const calculateBtn = document.getElementById('calculateBtn');
            const resultsCard = document.getElementById('resultsCard');
            const resultsContent = document.getElementById('resultsContent');
            const saveBtn = document.getElementById('saveBtn');
            let currentResults = null;

            calculateBtn.addEventListener('click', function() {
                const formData = new FormData(form);
                const data = {
                    direct_cost: parseFloat(formData.get('direct_cost')),
                    overhead_percentage: parseFloat(formData.get('overhead_percentage')),
                    profit_percentage: parseFloat(formData.get('profit_percentage')),
                };

                fetch('{{ route("tools.overhead-profit.calculate") }}', {
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
                        displayResults(data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghitung');
                });
            });

            function displayResults(data) {
                resultsContent.innerHTML = `
                    <div class="space-y-4">
                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Rincian Perhitungan</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Biaya Langsung:</span>
                                    <span class="font-medium">Rp ${data.direct_cost.toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Overhead:</span>
                                    <span class="font-medium">Rp ${data.overhead.toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Subtotal:</span>
                                    <span class="font-bold text-gray-900 dark:text-gray-100">Rp ${data.subtotal.toLocaleString('id-ID')}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Profit:</span>
                                    <span class="font-medium">Rp ${data.profit.toLocaleString('id-ID')}</span>
                                </div>
                            </div>
                        </div>

                        <div class="bg-primary-50 dark:bg-primary-900 p-6 rounded-lg text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Total Harga Proyek</p>
                            <p class="text-4xl font-bold text-primary-600 dark:text-primary-400">
                                Rp ${data.total.toLocaleString('id-ID')}
                            </p>
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

                const title = prompt('Masukkan judul untuk perhitungan ini (opsional):') || 'Overhead & Profit Calculator';
                const formData = new FormData(form);

                fetch('{{ route("tools.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'overhead_profit',
                        title: title,
                        inputs: {
                            direct_cost: parseFloat(formData.get('direct_cost')),
                            overhead_percentage: parseFloat(formData.get('overhead_percentage')),
                            profit_percentage: parseFloat(formData.get('profit_percentage')),
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

