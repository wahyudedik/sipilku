@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Bandingkan Pabrik
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <x-card class="mb-6">
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Form Perbandingan Pabrik</h3>
                </x-slot>
                <form method="GET" action="{{ route('factories.comparison.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nama Produk
                            </label>
                            <x-text-input name="product_name" value="{{ $productName }}" placeholder="Contoh: Ready Mix K-200" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Quantity
                            </label>
                            <x-text-input type="number" name="quantity" value="{{ $quantity ?? 1 }}" step="0.01" min="0.01" />
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Latitude
                            </label>
                            <x-text-input type="number" name="latitude" value="{{ $latitude }}" step="0.00000001" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Longitude
                            </label>
                            <x-text-input type="number" name="longitude" value="{{ $longitude }}" step="0.00000001" />
                        </div>
                    </div>
                    <x-button variant="primary" size="md" type="submit">Bandingkan</x-button>
                </form>
            </x-card>

            @if($comparisons->count() > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Hasil Perbandingan ({{ $comparisons->count() }} pabrik)</h3>
                    </x-slot>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pabrik</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Produk</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Harga Produk</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jarak</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ongkir</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Biaya</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rating</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($comparisons as $comparison)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                        <td class="px-4 py-3">
                                            <div class="flex items-center space-x-2">
                                                @if($comparison['factory']->logo)
                                                    <img src="{{ Storage::url($comparison['factory']->logo) }}" alt="{{ $comparison['factory']->name }}" class="w-10 h-10 object-cover rounded">
                                                @endif
                                                <div>
                                                    <p class="font-medium text-sm">{{ $comparison['factory']->name }}</p>
                                                    @if($comparison['factory']->factoryType)
                                                        <p class="text-xs text-gray-500">{{ $comparison['factory']->factoryType->name }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if(isset($comparison['product']))
                                                {{ $comparison['product']->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if(isset($comparison['product_price']))
                                                Rp {{ number_format($comparison['product_price'], 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if(isset($comparison['distance']))
                                                {{ number_format($comparison['distance'], 2) }} km
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if(isset($comparison['delivery_cost']))
                                                Rp {{ number_format($comparison['delivery_cost'], 0, ',', '.') }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if(isset($comparison['total_cost']))
                                                <span class="font-semibold text-primary-600">
                                                    Rp {{ number_format($comparison['total_cost'], 0, ',', '.') }}
                                                </span>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($comparison['factory']->rating > 0)
                                                <div class="flex items-center">
                                                    <span class="text-yellow-400">★</span>
                                                    <span class="ml-1">{{ $comparison['factory']->rating }}/5</span>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <a href="{{ route('factories.show', $comparison['factory']) }}" class="text-primary-600 hover:underline text-sm">
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @elseif(request()->has('product_name'))
                <x-card>
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Tidak ada pabrik ditemukan untuk produk "{{ $productName }}".
                    </p>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

