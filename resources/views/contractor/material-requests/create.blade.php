<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Create Material Request
        </h2>
    </x-slot>

    <form action="{{ route('contractor.material-requests.store') }}" method="POST">
        @csrf

        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Store & Location</h3>
            </x-slot>

            <x-form-group label="Select Stores (You can request from multiple stores)" name="store_ids" required>
                <div class="space-y-2">
                    @foreach($stores as $store)
                        <label class="flex items-center space-x-2 p-2 border border-gray-200 dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer">
                            <input type="checkbox" 
                                   name="store_ids[]" 
                                   value="{{ $store->uuid }}" 
                                   class="rounded border-gray-300 text-primary-600 focus:ring-primary-500"
                                   {{ $selectedStore && $selectedStore->uuid === $store->uuid ? 'checked' : '' }}>
                            <span class="flex-1">
                                <span class="font-medium">{{ $store->name }}</span>
                                @if($store->rating > 0)
                                    <span class="text-sm text-gray-500 ml-2">⭐ {{ $store->rating }}/5</span>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>
                <p class="text-sm text-gray-500 mt-2">Select one or more stores to request quotes from. You'll be able to compare quotes later.</p>
            </x-form-group>

            <x-form-group label="Project Location" name="project_location_id">
                <x-select-input name="project_location_id">
                    <option value="">Select Project Location (Optional)</option>
                    @foreach($projectLocations as $location)
                        <option value="{{ $location->uuid }}" {{ $selectedProjectLocation && $selectedProjectLocation->uuid === $location->uuid ? 'selected' : '' }}>
                            {{ $location->name }}
                        </option>
                    @endforeach
                </x-select-input>
            </x-form-group>
        </x-card>

        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Requested Items</h3>
            </x-slot>

            <div id="itemsContainer">
                <div class="item-row space-y-4 mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-group label="Item Name" name="items[0][name]" required>
                            <x-text-input name="items[0][name]" placeholder="e.g., Semen Portland" required />
                        </x-form-group>
                        <x-form-group label="Quantity" name="items[0][quantity]" required>
                            <x-text-input name="items[0][quantity]" type="number" step="0.01" min="1" placeholder="0" required />
                        </x-form-group>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form-group label="Unit" name="items[0][unit]" required>
                            <x-text-input name="items[0][unit]" placeholder="e.g., sak, kg, m3" required />
                        </x-form-group>
                        <x-form-group label="Description" name="items[0][description]">
                            <x-text-input name="items[0][description]" placeholder="Optional description" />
                        </x-form-group>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addItem()" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                + Add Another Item
            </button>
        </x-card>

        <x-card class="mb-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Additional Information</h3>
            </x-slot>

            <x-form-group label="Message" name="message">
                <x-textarea-input name="message" rows="4" placeholder="Any additional information or special requirements...">{{ old('message') }}</x-textarea-input>
            </x-form-group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-group label="Budget (Optional)" name="budget">
                    <x-text-input name="budget" type="number" step="0.01" min="0" placeholder="0" value="{{ old('budget') }}" />
                </x-form-group>

                <x-form-group label="Deadline (Optional)" name="deadline">
                    <x-text-input name="deadline" type="date" value="{{ old('deadline') }}" />
                </x-form-group>
            </div>
        </x-card>

        <div class="flex justify-end space-x-4">
            <a href="{{ route('contractor.material-requests.index') }}">
                <x-button variant="secondary">Cancel</x-button>
            </a>
            <x-button variant="primary" type="submit">Submit Request</x-button>
        </div>
    </form>

    @push('scripts')
    <script>
        let itemCount = 1;
        function addItem() {
            const container = document.getElementById('itemsContainer');
            const newItem = document.createElement('div');
            newItem.className = 'item-row space-y-4 mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-lg';
            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-group label="Item Name" name="items[${itemCount}][name]" required>
                        <x-text-input name="items[${itemCount}][name]" placeholder="e.g., Semen Portland" required />
                    </x-form-group>
                    <x-form-group label="Quantity" name="items[${itemCount}][quantity]" required>
                        <x-text-input name="items[${itemCount}][quantity]" type="number" step="0.01" min="1" placeholder="0" required />
                    </x-form-group>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-form-group label="Unit" name="items[${itemCount}][unit]" required>
                        <x-text-input name="items[${itemCount}][unit]" placeholder="e.g., sak, kg, m3" required />
                    </x-form-group>
                    <x-form-group label="Description" name="items[${itemCount}][description]">
                        <x-text-input name="items[${itemCount}][description]" placeholder="Optional description" />
                    </x-form-group>
                </div>
                <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">Remove</button>
            `;
            container.appendChild(newItem);
            itemCount++;
        }
    </script>
    @endpush
</x-app-with-sidebar>

