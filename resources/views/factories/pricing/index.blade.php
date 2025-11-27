<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pricing Management - {{ $factory->name }}
            </h2>
            <a href="{{ route('factories.dashboard', $factory) }}">
                <x-button variant="secondary" size="sm">Back to Dashboard</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Delivery Pricing</h3>
                </x-slot>
                <form action="{{ route('factories.pricing.update', $factory) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <x-form-group label="Delivery Price per KM *" name="delivery_price_per_km" required>
                            <x-text-input type="number" name="delivery_price_per_km" step="0.01" min="0" 
                                         value="{{ old('delivery_price_per_km', $factory->delivery_price_per_km) }}" required />
                            <x-input-error :messages="$errors->get('delivery_price_per_km')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Price charged per kilometer for delivery</p>
                        </x-form-group>

                        <x-form-group label="Maximum Delivery Distance (KM)" name="max_delivery_distance">
                            <x-text-input type="number" name="max_delivery_distance" step="0.1" min="0" 
                                         value="{{ old('max_delivery_distance', $factory->max_delivery_distance) }}" />
                            <x-input-error :messages="$errors->get('max_delivery_distance')" class="mt-2" />
                            <p class="text-xs text-gray-500 mt-1">Maximum distance you can deliver (leave empty for no limit)</p>
                        </x-form-group>

                        <div class="flex justify-end">
                            <x-button variant="primary" type="submit">Update Pricing</x-button>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Pricing Information -->
            <x-card class="mt-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Pricing Information</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Current Delivery Rate</span>
                        <span class="font-semibold">
                            Rp {{ number_format($factory->delivery_price_per_km ?? 0, 0, ',', '.') }} / km
                        </span>
                    </div>
                    @if($factory->max_delivery_distance)
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Max Delivery Distance</span>
                            <span class="font-semibold">{{ number_format($factory->max_delivery_distance, 1) }} km</span>
                        </div>
                    @else
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Max Delivery Distance</span>
                            <span class="font-semibold text-gray-500">No limit</span>
                        </div>
                    @endif
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>

