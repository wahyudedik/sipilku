<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                RAB Calculator
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tools.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
                @auth
                    <a href="{{ route('tools.history', ['type' => 'rab']) }}">
                        <x-button variant="secondary" size="sm">Riwayat</x-button>
                    </a>
                @endauth
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Input Form -->
                <div class="lg:col-span-2">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Input Item Pekerjaan</h3>
                        </x-slot>
                        <form id="rabForm">
                            <div id="itemsContainer" class="space-y-4">
                                <!-- Items will be added here dynamically -->
                            </div>
                            <div class="mt-4">
                                <x-button type="button" variant="secondary" size="md" id="addItemBtn">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                    </svg>
                                    Tambah Item
                                </x-button>
                                <x-button type="button" variant="primary" size="md" id="calculateBtn" class="ml-2">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-5m-6 5h6m-6 0h-2a2 2 0 01-2-2V9a2 2 0 012-2h2m6 0h2a2 2 0 012 2v11a2 2 0 01-2 2h-2m-6-13V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                                    </svg>
                                    Hitung RAB
                                </x-button>
                            </div>
                        </form>
                    </x-card>
                </div>

                <!-- Results -->
                <div>
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
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const itemsContainer = document.getElementById('itemsContainer');
            const addItemBtn = document.getElementById('addItemBtn');
            const calculateBtn = document.getElementById('calculateBtn');
            const resultsCard = document.getElementById('resultsCard');
            const resultsContent = document.getElementById('resultsContent');
            const saveBtn = document.getElementById('saveBtn');
            let itemCount = 0;
            let currentResults = null;

            // Add initial item
            addItem();

            function addItem() {
                const itemIndex = itemCount++;
                const itemDiv = document.createElement('div');
                itemDiv.className = 'p-4 border border-gray-200 dark:border-gray-700 rounded-lg';
                itemDiv.innerHTML = `
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-span-12 md:col-span-4">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Item</label>
                            <input type="text" name="items[${itemIndex}][name]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Contoh: Pekerjaan Galian" required>
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Quantity</label>
                            <input type="number" name="items[${itemIndex}][quantity]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                        </div>
                        <div class="col-span-6 md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Satuan</label>
                            <select name="items[${itemIndex}][unit]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" required>
                                <option value="m³">m³</option>
                                <option value="m²">m²</option>
                                <option value="m">m</option>
                                <option value="kg">kg</option>
                                <option value="unit">unit</option>
                                <option value="ls">ls</option>
                            </select>
                        </div>
                        <div class="col-span-8 md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Harga Satuan (Rp)</label>
                            <input type="number" name="items[${itemIndex}][unit_price]" step="0.01" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                        </div>
                        <div class="col-span-4 md:col-span-1 flex items-end">
                            <button type="button" class="remove-item-btn w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm" ${itemCount === 1 ? 'disabled' : ''}>
                                Hapus
                            </button>
                        </div>
                    </div>
                `;
                itemsContainer.appendChild(itemDiv);

                // Add remove functionality
                itemDiv.querySelector('.remove-item-btn').addEventListener('click', function() {
                    if (itemsContainer.children.length > 1) {
                        itemDiv.remove();
                        updateRemoveButtons();
                    }
                });
            }

            function updateRemoveButtons() {
                const removeBtns = document.querySelectorAll('.remove-item-btn');
                removeBtns.forEach(btn => {
                    btn.disabled = itemsContainer.children.length === 1;
                });
            }

            addItemBtn.addEventListener('click', addItem);

            calculateBtn.addEventListener('click', function() {
                const form = document.getElementById('rabForm');
                const formData = new FormData(form);
                const items = [];

                // Collect items
                const itemInputs = form.querySelectorAll('[name^="items["]');
                const itemGroups = {};
                itemInputs.forEach(input => {
                    const match = input.name.match(/items\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!itemGroups[index]) itemGroups[index] = {};
                        itemGroups[index][field] = input.value;
                    }
                });

                // Convert to array
                Object.values(itemGroups).forEach(item => {
                    if (item.name && item.quantity && item.unit && item.unit_price) {
                        items.push({
                            name: item.name,
                            quantity: parseFloat(item.quantity),
                            unit: item.unit,
                            unit_price: parseFloat(item.unit_price),
                        });
                    }
                });

                if (items.length === 0) {
                    alert('Minimal 1 item harus diisi');
                    return;
                }

                // Calculate
                fetch('{{ route("tools.rab.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ items: items })
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
                let html = `
                    <div class="space-y-3">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Item</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Qty</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Harga Satuan</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                `;

                data.items.forEach(item => {
                    html += `
                        <tr>
                            <td class="px-4 py-2 text-sm text-gray-900 dark:text-gray-100">${item.name}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 text-right">${item.quantity} ${item.unit}</td>
                            <td class="px-4 py-2 text-sm text-gray-600 dark:text-gray-400 text-right">Rp ${item.unit_price.toLocaleString('id-ID')}</td>
                            <td class="px-4 py-2 text-sm font-semibold text-gray-900 dark:text-gray-100 text-right">Rp ${item.subtotal.toLocaleString('id-ID')}</td>
                        </tr>
                    `;
                });

                html += `
                                </tbody>
                                <tfoot class="bg-gray-50 dark:bg-gray-800">
                                    <tr>
                                        <td colspan="3" class="px-4 py-3 text-sm font-bold text-gray-900 dark:text-gray-100">TOTAL</td>
                                        <td class="px-4 py-3 text-lg font-bold text-primary-600 dark:text-primary-400 text-right">Rp ${data.total.toLocaleString('id-ID')}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                `;

                resultsContent.innerHTML = html;
                resultsCard.classList.remove('hidden');
            }

            @auth
            saveBtn?.addEventListener('click', function() {
                if (!currentResults) {
                    alert('Silakan hitung terlebih dahulu');
                    return;
                }

                const title = prompt('Masukkan judul untuk perhitungan ini (opsional):') || 'RAB Calculator';

                fetch('{{ route("tools.save") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        type: 'rab',
                        title: title,
                        inputs: { items: currentResults.items },
                        results: { total: currentResults.total },
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

