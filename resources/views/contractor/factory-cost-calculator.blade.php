<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Factory Product Cost Calculator
        </h2>
    </x-slot>

    <x-card class="mb-6">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Calculate Factory Product Cost</h3>
        </x-slot>

        <form id="calculatorForm" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-group label="Factory Type" name="factory_type_id" required>
                    <x-select-input name="factory_type_id" id="factory_type_id" required>
                        <option value="">Select Factory Type</option>
                        @foreach($factoryTypes as $type)
                            <option value="{{ $type->uuid }}" data-units="{{ json_encode($type->default_units ?? []) }}">
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </x-select-input>
                </x-form-group>

                <x-form-group label="Project Location" name="project_location_id">
                    <x-select-input name="project_location_id" id="project_location_id">
                        <option value="">Select Project Location (Optional)</option>
                        @foreach($projectLocations as $location)
                            <option value="{{ $location->uuid }}" 
                                data-lat="{{ $location->latitude }}" 
                                data-lng="{{ $location->longitude }}">
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </x-select-input>
                </x-form-group>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-group label="Volume/Quantity" name="quantity" required>
                    <x-text-input name="quantity" type="number" step="0.01" min="0" id="quantity" required placeholder="0" />
                </x-form-group>

                <x-form-group label="Unit" name="unit" required>
                    <x-select-input name="unit" id="unit" required>
                        <option value="">Select Unit</option>
                    </x-select-input>
                </x-form-group>

                <x-form-group label="Price per Unit (Rp)" name="price_per_unit" required>
                    <x-text-input name="price_per_unit" type="number" step="0.01" min="0" id="price_per_unit" required placeholder="0" />
                </x-form-group>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-group label="Delivery Distance (km)" name="distance" id="distance_group">
                    <x-text-input name="distance" type="number" step="0.01" min="0" id="distance" placeholder="Auto-calculated if location selected" />
                </x-form-group>

                <x-form-group label="Delivery Price per km (Rp)" name="delivery_price_per_km" id="delivery_price_group">
                    <x-text-input name="delivery_price_per_km" type="number" step="0.01" min="0" id="delivery_price_per_km" placeholder="0" />
                </x-form-group>
            </div>

            <div class="flex justify-end">
                <x-button type="button" variant="primary" onclick="calculateCost()">Calculate</x-button>
            </div>
        </form>
    </x-card>

    <x-card id="resultCard" class="hidden">
        <x-slot name="header">
            <h3 class="text-lg font-medium">Calculation Result</h3>
        </x-slot>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Product Cost</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400" id="productCost">Rp 0</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Delivery Cost</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="deliveryCost">Rp 0</p>
                </div>
            </div>
            <div class="border-t pt-4">
                <div class="flex justify-between items-center">
                    <p class="text-lg font-semibold">Total Cost</p>
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400" id="totalCost">Rp 0</p>
                </div>
            </div>
        </div>
    </x-card>

    @push('scripts')
    <script>
        // Update units when factory type changes
        document.getElementById('factory_type_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const units = JSON.parse(selected.dataset.units || '[]');
            const unitSelect = document.getElementById('unit');
            
            unitSelect.innerHTML = '<option value="">Select Unit</option>';
            units.forEach(unit => {
                const option = document.createElement('option');
                option.value = unit;
                option.textContent = unit;
                unitSelect.appendChild(option);
            });
        });

        // Auto-calculate distance when project location is selected
        document.getElementById('project_location_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            if (selected.value && selected.dataset.lat && selected.dataset.lng) {
                // In a real implementation, you would fetch factory locations and calculate distance
                // For now, we'll just show that distance can be calculated
                document.getElementById('distance').placeholder = 'Select factory to calculate distance';
            }
        });

        function calculateCost() {
            const quantity = parseFloat(document.getElementById('quantity').value) || 0;
            const pricePerUnit = parseFloat(document.getElementById('price_per_unit').value) || 0;
            const distance = parseFloat(document.getElementById('distance').value) || 0;
            const deliveryPricePerKm = parseFloat(document.getElementById('delivery_price_per_km').value) || 0;

            const productCost = quantity * pricePerUnit;
            const deliveryCost = distance * deliveryPricePerKm;
            const totalCost = productCost + deliveryCost;

            document.getElementById('productCost').textContent = 'Rp ' + productCost.toLocaleString('id-ID');
            document.getElementById('deliveryCost').textContent = 'Rp ' + deliveryCost.toLocaleString('id-ID');
            document.getElementById('totalCost').textContent = 'Rp ' + totalCost.toLocaleString('id-ID');
            document.getElementById('resultCard').classList.remove('hidden');
        }
    </script>
    @endpush
</x-app-with-sidebar>

