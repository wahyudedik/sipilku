<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
            <a href="{{ route('products.index') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Home</a>
            <span>/</span>
            <a href="{{ route('services.show', $service) }}" class="hover:text-gray-700 dark:hover:text-gray-300">{{ $service->title }}</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-gray-100">Request Quote</span>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                        Request Quote untuk: {{ $service->title }}
                    </h2>
                </x-slot>

                <form action="{{ route('quote-requests.store', $service) }}" method="POST">
                    @csrf

                    <div class="space-y-6">
                        <!-- Service Info -->
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <div class="flex items-start space-x-4">
                                @if($service->preview_image)
                                    <img src="{{ Storage::url($service->preview_image) }}" 
                                         alt="{{ $service->title }}"
                                         class="w-20 h-20 object-cover rounded-lg">
                                @endif
                                <div>
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">{{ $service->title }}</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Oleh: {{ $service->user->name }}</p>
                                    @if($service->package_prices && count($service->package_prices) > 0)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                            Mulai dari: <span class="font-bold text-primary-600">Rp {{ number_format(min(array_column($service->package_prices, 'price')), 0, ',', '.') }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Message -->
                        <x-form-group label="Pesan / Kebutuhan Proyek" name="message" required help="Jelaskan kebutuhan proyek Anda secara detail (minimal 20 karakter)">
                            <x-textarea-input name="message" rows="6" placeholder="Jelaskan kebutuhan proyek Anda, scope of work, dan detail lainnya...">{{ old('message') }}</x-textarea-input>
                        </x-form-group>

                        <!-- Budget -->
                        <x-form-group label="Budget (Opsional)" name="budget" help="Berikan estimasi budget jika ada">
                            <x-text-input name="budget" type="number" step="0.01" value="{{ old('budget') }}" placeholder="0" />
                        </x-form-group>

                        <!-- Deadline -->
                        <x-form-group label="Deadline (Opsional)" name="deadline" help="Kapan proyek harus selesai">
                            <x-text-input name="deadline" type="date" value="{{ old('deadline') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                        </x-form-group>

                        <!-- Project Location (Optional) -->
                        @auth
                            @if($projectLocations->count() > 0)
                                <x-form-group label="Lokasi Proyek (Opsional)" name="project_location_id" help="Pilih lokasi proyek untuk mendapatkan rekomendasi toko & pabrik terdekat">
                                    <select name="project_location_id" id="project_location_id" onchange="updateRecommendations()" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                                        <option value="">Pilih lokasi proyek</option>
                                        @foreach($projectLocations as $location)
                                            <option value="{{ $location->uuid }}" {{ $projectLocation && $projectLocation->uuid === $location->uuid ? 'selected' : '' }}>
                                                {{ $location->name }} - {{ $location->address }}
                                            </option>
                                        @endforeach
                                    </select>
                                </x-form-group>
                            @endif
                        @endauth

                        <!-- Requirements (Dynamic) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Requirements / Spesifikasi (Opsional)
                            </label>
                            <div id="requirementsContainer" class="space-y-2">
                                <!-- Requirements will be added here -->
                            </div>
                            <button type="button" onclick="addRequirement()" class="mt-2 text-sm text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                + Tambah Requirement
                            </button>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <a href="{{ route('services.show', $service) }}" class="flex-1">
                                <x-button variant="secondary" size="md" type="button" class="w-full">
                                    Batal
                                </x-button>
                            </a>
                            <x-button variant="primary" size="md" type="submit" class="flex-1">
                                Kirim Request Quote
                            </x-button>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Store & Factory Recommendations -->
            @if(isset($recommendations) && ($recommendations['stores']->count() > 0 || $recommendations['factories']->count() > 0))
                <x-card class="mt-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Rekomendasi Toko & Pabrik Terdekat</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Berdasarkan lokasi proyek Anda
                        </p>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($recommendations['stores']->count() > 0)
                            <div>
                                <h4 class="font-medium mb-3">Toko Terdekat</h4>
                                <div class="space-y-3">
                                    @foreach($recommendations['stores'] as $recommendation)
                                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <p class="font-medium text-sm">{{ $recommendation['store']->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ number_format($recommendation['distance'], 1) }} km
                                                    </p>
                                                </div>
                                                <a href="{{ route('stores.show', $recommendation['store']) }}" class="text-xs text-primary-600 hover:underline">
                                                    Lihat →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($recommendations['factories']->count() > 0)
                            <div>
                                <h4 class="font-medium mb-3">Pabrik Terdekat</h4>
                                <div class="space-y-3">
                                    @foreach($recommendations['factories'] as $recommendation)
                                        <div class="p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <p class="font-medium text-sm">{{ $recommendation['factory']->name }}</p>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                                        {{ $recommendation['factory']->factoryType->name ?? 'Factory' }} • {{ number_format($recommendation['distance'], 1) }} km
                                                    </p>
                                                </div>
                                                <a href="{{ route('factories.show', $recommendation['factory']) }}" class="text-xs text-primary-600 hover:underline">
                                                    Lihat →
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </x-card>
            @endif

            <!-- Material Cost Estimates -->
            @if(isset($materialEstimates) && count($materialEstimates) > 0)
                <x-card class="mt-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Estimasi Biaya Material</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Estimasi harga material yang mungkin dibutuhkan untuk proyek ini
                        </p>
                    </x-slot>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Material</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Harga Toko</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Harga Pabrik</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rekomendasi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($materialEstimates as $estimate)
                                    <tr>
                                        <td class="px-4 py-3 text-sm font-medium">{{ ucfirst($estimate['material']) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($estimate['store_price'])
                                                Rp {{ number_format($estimate['store_price'], 0, ',', '.') }}
                                                @if($estimate['store_name'])
                                                    <br><span class="text-xs text-gray-500">{{ $estimate['store_name'] }}</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($estimate['factory_price'])
                                                Rp {{ number_format($estimate['factory_price'], 0, ',', '.') }}
                                                @if($estimate['factory_name'])
                                                    <br><span class="text-xs text-gray-500">{{ $estimate['factory_name'] }}</span>
                                                @endif
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($estimate['recommended_source'])
                                                <span class="px-2 py-1 bg-{{ $estimate['recommended_source'] === 'store' ? 'blue' : 'green' }}-100 dark:bg-{{ $estimate['recommended_source'] === 'store' ? 'blue' : 'green' }}-900/30 text-{{ $estimate['recommended_source'] === 'store' ? 'blue' : 'green' }}-800 dark:text-{{ $estimate['recommended_source'] === 'store' ? 'blue' : 'green' }}-200 rounded text-xs">
                                                    {{ $estimate['recommended_source'] === 'store' ? 'Toko' : 'Pabrik' }}
                                                </span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        let requirementCount = 0;

        function addRequirement() {
            const container = document.getElementById('requirementsContainer');
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2';
            div.id = `requirement-${requirementCount}`;
            div.innerHTML = `
                <input type="text" name="requirements[]" 
                       class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800"
                       placeholder="Contoh: Desain struktur untuk gedung 5 lantai">
                <button type="button" onclick="removeRequirement(${requirementCount})" class="text-red-600 hover:text-red-800">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            `;
            container.appendChild(div);
            requirementCount++;
        }

        function removeRequirement(id) {
            const div = document.getElementById(`requirement-${id}`);
            if (div) {
                div.remove();
            }
        }

        function updateRecommendations() {
            const projectLocationId = document.getElementById('project_location_id')?.value;
            if (projectLocationId) {
                // Reload page with project location parameter
                const url = new URL(window.location.href);
                url.searchParams.set('project_location', projectLocationId);
                window.location.href = url.toString();
            }
        }
    </script>
    @endpush
</x-app-layout>

