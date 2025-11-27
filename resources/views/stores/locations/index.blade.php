<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Lokasi Toko: {{ $store->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('stores.locations.create', $store) }}">
                    <x-button variant="primary" size="sm">Tambah Lokasi</x-button>
                </a>
                <a href="{{ route('stores.my-store') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif

            @if($locations->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($locations as $location)
                        <x-card>
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $location->name }}
                                    </h3>
                                    <div class="flex items-center space-x-2 mt-2">
                                        @if($location->is_primary)
                                            <x-badge variant="success" size="sm">Primary</x-badge>
                                        @endif
                                        @if($location->is_active)
                                            <x-badge variant="info" size="sm">Active</x-badge>
                                        @else
                                            <x-badge variant="default" size="sm">Inactive</x-badge>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('stores.locations.edit', [$store, $location]) }}">
                                        <x-button variant="secondary" size="sm">Edit</x-button>
                                    </a>
                                    <form action="{{ route('stores.locations.destroy', [$store, $location]) }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="danger" size="sm" type="submit">Hapus</x-button>
                                    </form>
                                </div>
                            </div>

                            <div class="space-y-2 text-sm">
                                <p class="text-gray-600 dark:text-gray-400">{{ $location->full_address }}</p>
                                @if($location->phone)
                                    <p class="text-gray-600 dark:text-gray-400">📞 {{ $location->phone }}</p>
                                @endif
                                @if($location->email)
                                    <p class="text-gray-600 dark:text-gray-400">✉️ {{ $location->email }}</p>
                                @endif
                                @if($location->hasCoordinates())
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $location->latitude }}, {{ $location->longitude }}
                                    </p>
                                    <a href="https://www.google.com/maps?q={{ $location->latitude }},{{ $location->longitude }}" 
                                       target="_blank" 
                                       class="text-primary-600 hover:underline text-xs">
                                        Buka di Google Maps
                                    </a>
                                @endif
                            </div>
                        </x-card>
                    @endforeach
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada lokasi toko.</p>
                        <a href="{{ route('stores.locations.create', $store) }}">
                            <x-button variant="primary">Tambah Lokasi Pertama</x-button>
                        </a>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

