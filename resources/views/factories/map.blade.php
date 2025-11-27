@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Peta Lokasi Pabrik
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('factories.map') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-form-group label="Tipe Pabrik">
                                <select name="factory_type_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                    <option value="">Semua Tipe</option>
                                    @foreach($factoryTypes as $type)
                                        <option value="{{ $type->uuid }}" {{ $factoryTypeId === $type->uuid ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </x-form-group>
                        </div>
                        <div>
                            <x-form-group label="Latitude">
                                <x-text-input type="number" name="latitude" step="0.00000001" value="{{ $latitude }}" />
                            </x-form-group>
                        </div>
                        <div>
                            <x-form-group label="Longitude">
                                <x-text-input type="number" name="longitude" step="0.00000001" value="{{ $longitude }}" />
                            </x-form-group>
                        </div>
                        <div class="flex items-end">
                            <x-button variant="primary" size="md" type="submit" class="w-full">Filter</x-button>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Map -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Peta Lokasi Pabrik ({{ $factories->count() }} pabrik)</h3>
                </x-slot>
                <div id="map" class="w-full h-[700px] rounded-lg"></div>
            </x-card>

            <!-- Factory List -->
            @if($factories->count() > 0)
                <x-card class="mt-6">
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Daftar Pabrik</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($factories as $factory)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md transition cursor-pointer"
                                 onclick="focusFactory({{ $factory['latitude'] }}, {{ $factory['longitude'] }})">
                                <div class="flex items-start space-x-3">
                                    @if($factory['logo'])
                                        <img src="{{ Storage::url($factory['logo']) }}" alt="{{ $factory['name'] }}" class="w-12 h-12 object-cover rounded">
                                    @endif
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('factories.show', $factory['slug']) }}" class="hover:text-primary-600">
                                                {{ $factory['name'] }}
                                            </a>
                                        </h4>
                                        @if($factory['factory_type'])
                                            <x-badge variant="default" size="xs" class="mt-1">
                                                {{ $factory['factory_type']['name'] }}
                                            </x-badge>
                                        @endif
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                                            {{ $factory['address'] }}
                                        </p>
                                        @if($factory['operating_hours'])
                                            <div class="mt-2 text-xs text-gray-500">
                                                <strong>Jam Operasional:</strong>
                                                @if(is_array($factory['operating_hours']))
                                                    <div class="mt-1">
                                                        @foreach(array_slice($factory['operating_hours'], 0, 3) as $day => $hours)
                                                            <div>{{ ucfirst($day) }}: {{ $hours }}</div>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="mt-1">{{ $factory['operating_hours'] }}</div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places&callback=initMap" async defer></script>
    <script>
        let map;
        let markers = [];
        const factories = @json($factories);

        function initMap() {
            const defaultLocation = { lat: {{ $latitude }}, lng: {{ $longitude }} };
            
            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: {{ $zoom }},
            });

            // Add factory markers
            factories.forEach(function(factory) {
                if (factory.latitude && factory.longitude) {
                    const marker = new google.maps.Marker({
                        position: { lat: parseFloat(factory.latitude), lng: parseFloat(factory.longitude) },
                        map: map,
                        title: factory.name,
                    });

                    const infoWindow = new google.maps.InfoWindow({
                        content: `
                            <div class="p-3 min-w-[200px]">
                                <h3 class="font-semibold text-lg mb-2">${factory.name}</h3>
                                ${factory.factory_type ? `<p class="text-sm text-gray-600 mb-2">${factory.factory_type.name}</p>` : ''}
                                <p class="text-sm text-gray-700 mb-2">${factory.address}</p>
                                ${factory.operating_hours ? `
                                    <div class="text-xs text-gray-600 mb-2">
                                        <strong>Jam Operasional:</strong>
                                        ${typeof factory.operating_hours === 'object' 
                                            ? Object.entries(factory.operating_hours).slice(0, 3).map(([day, hours]) => 
                                                `<div>${day}: ${hours}</div>`
                                            ).join('')
                                            : factory.operating_hours
                                        }
                                    </div>
                                ` : ''}
                                <a href="/factories/${factory.slug}" class="text-primary-600 hover:underline text-sm font-medium">
                                    Lihat Detail →
                                </a>
                            </div>
                        `,
                    });

                    marker.addListener('click', function() {
                        infoWindow.open(map, marker);
                    });

                    markers.push(marker);
                }
            });

            // Fit bounds to show all markers
            if (markers.length > 0) {
                const bounds = new google.maps.LatLngBounds();
                markers.forEach(marker => bounds.extend(marker.getPosition()));
                map.fitBounds(bounds);
            }
        }

        function focusFactory(lat, lng) {
            map.setCenter({ lat: lat, lng: lng });
            map.setZoom(15);
        }
    </script>
    @endpush
</x-app-layout>

