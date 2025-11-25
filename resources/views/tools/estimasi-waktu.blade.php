<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Estimasi Waktu Proyek
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tools.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
                @auth
                    <a href="{{ route('tools.history', ['type' => 'estimasi_waktu']) }}">
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
                        <h3 class="text-lg font-medium">Input Aktivitas Proyek</h3>
                    </x-slot>
                    <form id="estimasiForm">
                        <div id="activitiesContainer" class="space-y-4">
                            <!-- Activities will be added here dynamically -->
                        </div>
                        <div class="mt-4">
                            <x-button type="button" variant="secondary" size="md" id="addActivityBtn">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                </svg>
                                Tambah Aktivitas
                            </x-button>
                            <x-button type="button" variant="primary" size="md" id="calculateBtn" class="ml-2">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hitung Estimasi
                            </x-button>
                        </div>
                    </form>
                </x-card>

                <!-- Results -->
                <x-card id="resultsCard" class="hidden">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Hasil Estimasi</h3>
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
            const activitiesContainer = document.getElementById('activitiesContainer');
            const addActivityBtn = document.getElementById('addActivityBtn');
            const calculateBtn = document.getElementById('calculateBtn');
            const resultsCard = document.getElementById('resultsCard');
            const resultsContent = document.getElementById('resultsContent');
            const saveBtn = document.getElementById('saveBtn');
            let activityCount = 0;
            let currentResults = null;

            addActivity();

            function addActivity() {
                const activityIndex = activityCount++;
                const activityDiv = document.createElement('div');
                activityDiv.className = 'p-4 border border-gray-200 dark:border-gray-700 rounded-lg';
                activityDiv.innerHTML = `
                    <div class="space-y-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Aktivitas</label>
                            <input type="text" name="activities[${activityIndex}][name]" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Contoh: Pekerjaan Galian" required>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Durasi (hari)</label>
                                <input type="number" name="activities[${activityIndex}][duration]" step="0.1" min="0" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="0" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Pekerja</label>
                                <input type="number" name="activities[${activityIndex}][workers]" min="1" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="1" value="1" required>
                            </div>
                        </div>
                        <button type="button" class="remove-activity-btn w-full px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-md text-sm" ${activityCount === 1 ? 'disabled' : ''}>
                            Hapus Aktivitas
                        </button>
                    </div>
                `;
                activitiesContainer.appendChild(activityDiv);

                activityDiv.querySelector('.remove-activity-btn').addEventListener('click', function() {
                    if (activitiesContainer.children.length > 1) {
                        activityDiv.remove();
                        updateRemoveButtons();
                    }
                });
            }

            function updateRemoveButtons() {
                const removeBtns = document.querySelectorAll('.remove-activity-btn');
                removeBtns.forEach(btn => {
                    btn.disabled = activitiesContainer.children.length === 1;
                });
            }

            addActivityBtn.addEventListener('click', addActivity);

            calculateBtn.addEventListener('click', function() {
                const form = document.getElementById('estimasiForm');
                const formData = new FormData(form);
                const activities = [];

                const activityInputs = form.querySelectorAll('[name^="activities["]');
                const activityGroups = {};
                activityInputs.forEach(input => {
                    const match = input.name.match(/activities\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!activityGroups[index]) activityGroups[index] = {};
                        activityGroups[index][field] = input.value;
                    }
                });

                Object.values(activityGroups).forEach(activity => {
                    if (activity.name && activity.duration && activity.workers) {
                        activities.push({
                            name: activity.name,
                            duration: parseFloat(activity.duration),
                            workers: parseInt(activity.workers),
                            dependencies: [],
                        });
                    }
                });

                if (activities.length === 0) {
                    alert('Minimal 1 aktivitas harus diisi');
                    return;
                }

                fetch('{{ route("tools.estimasi-waktu.calculate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ activities })
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
                        <div class="bg-primary-50 dark:bg-primary-900 p-6 rounded-lg text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Estimasi Durasi Proyek</p>
                            <p class="text-4xl font-bold text-primary-600 dark:text-primary-400 mb-2">
                                ${data.estimated_days.toLocaleString('id-ID', {minimumFractionDigits: 1})}
                            </p>
                            <p class="text-lg text-gray-600 dark:text-gray-400">Hari</p>
                        </div>

                        <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">Detail Estimasi</h4>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Durasi:</span>
                                    <span class="font-medium">${data.total_duration.toLocaleString('id-ID', {minimumFractionDigits: 1})} hari</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Total Work Days:</span>
                                    <span class="font-medium">${data.total_work_days.toLocaleString('id-ID', {minimumFractionDigits: 1})} hari</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Estimasi (hari):</span>
                                    <span class="font-bold text-primary-600 dark:text-primary-400">${data.estimated_days.toLocaleString('id-ID', {minimumFractionDigits: 1})}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Estimasi (minggu):</span>
                                    <span class="font-bold text-primary-600 dark:text-primary-400">${data.estimated_weeks.toLocaleString('id-ID', {minimumFractionDigits: 1})}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">Estimasi (bulan):</span>
                                    <span class="font-bold text-primary-600 dark:text-primary-400">${data.estimated_months.toLocaleString('id-ID', {minimumFractionDigits: 1})}</span>
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

                const title = prompt('Masukkan judul untuk perhitungan ini (opsional):') || 'Estimasi Waktu Proyek';
                const form = document.getElementById('estimasiForm');
                const formData = new FormData(form);
                const activities = [];

                const activityInputs = form.querySelectorAll('[name^="activities["]');
                const activityGroups = {};
                activityInputs.forEach(input => {
                    const match = input.name.match(/activities\[(\d+)\]\[(\w+)\]/);
                    if (match) {
                        const index = match[1];
                        const field = match[2];
                        if (!activityGroups[index]) activityGroups[index] = {};
                        activityGroups[index][field] = input.value;
                    }
                });

                Object.values(activityGroups).forEach(activity => {
                    if (activity.name && activity.duration && activity.workers) {
                        activities.push({
                            name: activity.name,
                            duration: parseFloat(activity.duration),
                            workers: parseInt(activity.workers),
                        });
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
                        type: 'estimasi_waktu',
                        title: title,
                        inputs: { activities },
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

