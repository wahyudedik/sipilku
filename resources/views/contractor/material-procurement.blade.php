<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Material Procurement
            </h2>
            <a href="{{ route('contractor.material-requests.create') }}">
                <x-button variant="primary">Create Request</x-button>
            </a>
        </div>
    </x-slot>

    <form method="GET" action="{{ route('contractor.material-procurement') }}" class="mb-6">
        <x-card>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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

    @if($stores->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($stores as $store)
                <x-card>
                    <div class="flex items-start space-x-4 mb-4">
                        @if($store->logo)
                            <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-16 h-16 object-cover rounded">
                        @endif
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold">{{ $store->name }}</h3>
                            @if($store->distance !== null)
                                <p class="text-sm text-primary-600 dark:text-primary-400 font-semibold">
                                    {{ number_format($store->distance, 2) }} km away
                                </p>
                            @endif
                            @if($store->nearest_location)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $store->nearest_location->full_address }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($store->description)
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                            {{ Str::limit($store->description, 100) }}
                        </p>
                    @endif

                    <div class="flex items-center justify-between text-sm mb-4">
                        <div class="flex items-center space-x-2">
                            @if($store->rating > 0)
                                <span class="text-yellow-500">★</span>
                                <span>{{ $store->rating }}/5</span>
                            @endif
                            <span class="text-gray-500">({{ $store->total_reviews }} reviews)</span>
                        </div>
                        <x-badge variant="success">Verified</x-badge>
                    </div>

                    <div class="flex space-x-2">
                        <a href="{{ route('contractor.material-requests.create', ['store' => $store->uuid, 'project_location' => $selectedProjectLocation?->uuid]) }}" class="flex-1">
                            <x-button variant="primary" size="sm" class="w-full">Request Quote</x-button>
                        </a>
                        <a href="{{ route('contractor.material-requests.index') }}" class="flex-1">
                            <x-button variant="secondary" size="sm" class="w-full">View Requests</x-button>
                        </a>
                    </div>
                </x-card>
            @endforeach
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No stores available.</p>
                @if($selectedProjectLocation)
                    <p class="text-sm text-gray-400">Try selecting a different project location.</p>
                @endif
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

