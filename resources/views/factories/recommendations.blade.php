@php
use Illuminate\Support\Facades\Storage;
use App\Models\FactoryType;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Rekomendasi Pabrik
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Search Form -->
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Cari Rekomendasi Pabrik</h3>
                </x-slot>
                <form method="GET" action="{{ route('factories.recommendations') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Latitude *
                            </label>
                            <x-text-input type="number" name="latitude" value="{{ request('latitude') }}" step="0.00000001" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Longitude *
                            </label>
                            <x-text-input type="number" name="longitude" value="{{ request('longitude') }}" step="0.00000001" required />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Tipe Pabrik
                            </label>
                            <select name="factory_type_id" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Tipe</option>
                                @foreach(FactoryType::where('is_active', true)->get() as $type)
                                    <option value="{{ $type->uuid }}" {{ request('factory_type_id') === $type->uuid ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Max Jarak (km)
                            </label>
                            <x-text-input type="number" name="max_distance" value="{{ request('max_distance', 100) }}" min="1" max="200" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Limit
                            </label>
                            <x-text-input type="number" name="limit" value="{{ request('limit', 10) }}" min="1" max="50" />
                        </div>
                        <div class="flex items-end">
                            <x-button variant="primary" size="md" type="submit" class="w-full">Cari Rekomendasi</x-button>
                        </div>
                    </div>
                </form>
            </x-card>

            @if(isset($recommendations) && $recommendations->count() > 0)
                <x-recommended-factories-widget 
                    :recommendations="$recommendations" 
                    title="Hasil Rekomendasi Pabrik"
                    :showViewAll="false" />
            @elseif(request()->has('latitude'))
                <x-card>
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Tidak ada rekomendasi pabrik ditemukan untuk lokasi tersebut.
                    </p>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

