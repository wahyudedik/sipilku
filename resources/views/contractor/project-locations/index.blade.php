<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Project Locations
            </h2>
            <a href="{{ route('contractor.project-locations.create') }}">
                <x-button variant="primary">Add New Location</x-button>
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    @if($projectLocations->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($projectLocations as $location)
                <x-card>
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold">{{ $location->name }}</h3>
                            @if($location->description)
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $location->description }}</p>
                            @endif
                        </div>
                        <x-badge :variant="$location->is_active ? 'success' : 'default'">
                            {{ $location->is_active ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </div>

                    <div class="space-y-2 text-sm">
                        <p class="text-gray-600 dark:text-gray-400">
                            <strong>Address:</strong> {{ $location->full_address }}
                        </p>
                        @if($location->hasCoordinates())
                            <p class="text-gray-600 dark:text-gray-400">
                                <strong>Coordinates:</strong> {{ $location->latitude }}, {{ $location->longitude }}
                            </p>
                        @endif
                    </div>

                    <div class="flex space-x-2 mt-4">
                        <a href="{{ route('contractor.project-locations.edit', $location) }}" class="flex-1">
                            <x-button variant="secondary" size="sm" class="w-full">Edit</x-button>
                        </a>
                        <form action="{{ route('contractor.project-locations.destroy', $location) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure?')">
                            @csrf
                            @method('DELETE')
                            <x-button variant="danger" size="sm" type="submit" class="w-full">Delete</x-button>
                        </form>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $projectLocations->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400 mb-4">No project locations yet.</p>
                <a href="{{ route('contractor.project-locations.create') }}">
                    <x-button variant="primary">Add Your First Project Location</x-button>
                </a>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

