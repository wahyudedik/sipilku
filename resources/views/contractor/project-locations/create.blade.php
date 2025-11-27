<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Add Project Location
        </h2>
    </x-slot>

    <form action="{{ route('contractor.project-locations.store') }}" method="POST">
        @csrf

        <x-card>
            <x-slot name="header">
                <h3 class="text-lg font-medium">Location Information</h3>
            </x-slot>

            <x-form-group label="Location Name" name="name" required>
                <x-text-input name="name" value="{{ old('name') }}" placeholder="e.g., Project Site A" />
            </x-form-group>

            <x-form-group label="Description" name="description">
                <x-textarea-input name="description" rows="3">{{ old('description') }}</x-textarea-input>
            </x-form-group>

            <x-form-group label="Address" name="address" required>
                <x-textarea-input name="address" rows="2" required>{{ old('address') }}</x-textarea-input>
            </x-form-group>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-group label="City" name="city" required>
                    <x-text-input name="city" value="{{ old('city') }}" />
                </x-form-group>

                <x-form-group label="Province" name="province" required>
                    <x-text-input name="province" value="{{ old('province') }}" />
                </x-form-group>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-group label="Postal Code" name="postal_code">
                    <x-text-input name="postal_code" value="{{ old('postal_code') }}" />
                </x-form-group>

                <x-form-group label="Country" name="country">
                    <x-text-input name="country" value="{{ old('country', 'Indonesia') }}" />
                </x-form-group>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-form-group label="Latitude" name="latitude" help="Optional: For location-based recommendations">
                    <x-text-input name="latitude" type="number" step="any" value="{{ old('latitude') }}" placeholder="-6.2088" />
                </x-form-group>

                <x-form-group label="Longitude" name="longitude" help="Optional: For location-based recommendations">
                    <x-text-input name="longitude" type="number" step="any" value="{{ old('longitude') }}" placeholder="106.8456" />
                </x-form-group>
            </div>

            <x-form-group label="Status" name="is_active">
                <label class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300">
                    <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">Active</span>
                </label>
            </x-form-group>
        </x-card>

        <div class="flex justify-end space-x-4 mt-6">
            <a href="{{ route('contractor.project-locations.index') }}">
                <x-button variant="secondary">Cancel</x-button>
            </a>
            <x-button variant="primary" type="submit">Create Location</x-button>
        </div>
    </form>
</x-app-with-sidebar>

