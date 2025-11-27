<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Factory Procurement
            </h2>
            <a href="{{ route('contractor.factory-requests.create') }}">
                <x-button variant="primary">Create Request</x-button>
            </a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('contractor.factory-procurement') }}" class="mb-6">
        <x-card>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <x-form-group label="Factory Type" name="factory_type">
                    <x-select-input name="factory_type" onchange="this.form.submit()">
                        <option value="">All Types</option>
                        @foreach($factoryTypes as $type)
                            <option value="{{ $type->uuid }}" {{ $factoryTypeId === $type->uuid ? 'selected' : '' }}>
                                {{ $type->name }}
                            </option>
                        @endforeach
                    </x-select-input>
                </x-form-group>

                <x-form-group label="Project Location" name="project_location">
                    <x-select-input name="project_location" onchange="this.form.submit()">
                        <option value="">All Locations</option>
                        @foreach($projectLocations as $location)
                            <option value="{{ $location->uuid }}" {{ $selectedProjectLocation && $selectedProjectLocation->uuid === $location->uuid ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </x-select-input>
                </x-form-group>
            </div>
        </x-card>
    </form>

    @if($factories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($factories as $factory)
                <x-card>
                    <div class="flex items-start space-x-4 mb-4">
                        @if($factory->logo)
                            <img src="{{ Storage::url($factory->logo) }}" alt="{{ $factory->name }}" class="w-16 h-16 object-cover rounded">
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold">{{ $factory->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $factory->factoryType->name ?? 'Factory' }}
                            </p>
                            @if($factory->distance !== null)
                                <p class="text-sm text-primary-600 dark:text-primary-400 font-semibold mt-1">
                                    {{ number_format($factory->distance, 2) }} km away
                                </p>
                            @endif
                            @if($factory->nearest_location)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $factory->nearest_location->full_address }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($factory->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                            {{ Str::limit($factory->description, 100) }}
                        </p>
                    @endif

                    <div class="space-y-2 text-sm mb-4">
                        @if($factory->delivery_cost !== null)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Delivery Cost:</span>
                                <span class="font-semibold">Rp {{ number_format($factory->delivery_cost, 0, ',', '.') }}/km</span>
                            </div>
                        @endif
                        @if($factory->delivery_price_per_km)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Price per km:</span>
                                <span>Rp {{ number_format($factory->delivery_price_per_km, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        <div class="flex items-center space-x-2">
                            @if($factory->rating > 0)
                                <span class="text-yellow-500">★</span>
                                <span>{{ $factory->rating }}/5</span>
                            @endif
                            <span class="text-gray-500">({{ $factory->total_reviews }} reviews)</span>
                        </div>
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('contractor.factory-requests.create', ['factory' => $factory->uuid, 'factory_type' => $factory->factory_type_id, 'project_location' => $selectedProjectLocation?->uuid]) }}" class="flex-1">
                            <x-button variant="primary" size="sm" class="w-full">Request Quote</x-button>
                        </a>
                        <a href="{{ route('contractor.factory-requests.index') }}" class="flex-1">
                            <x-button variant="secondary" size="sm" class="w-full">View Requests</x-button>
                        </a>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No factories available.</p>
                @if($factoryTypeId || $selectedProjectLocation)
                    <p class="text-sm text-gray-400">Try adjusting your filters.</p>
                @endif
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

