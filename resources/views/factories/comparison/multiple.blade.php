@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Perbandingan Pabrik (Multi-Kriteria)
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($comparisons->count() > 0)
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Perbandingan {{ $comparisons->count() }} Pabrik</h3>
                    </x-slot>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Pabrik</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipe</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rating</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Review</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Produk</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Sertifikat</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Skor Kualitas</th>
                                    @if($request->latitude && $request->longitude)
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jarak</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Ongkir/km</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Biaya Delivery</th>
                                    @endif
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
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($comparison['factory']->factoryType)
                                                {{ $comparison['factory']->factoryType->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($comparison['rating'] > 0)
                                                <div class="flex items-center">
                                                    <span class="text-yellow-400">★</span>
                                                    <span class="ml-1 text-sm">{{ number_format($comparison['rating'], 1) }}/5</span>
                                                </div>
                                            @else
                                                <span class="text-gray-400 text-sm">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $comparison['total_reviews'] }} review
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $comparison['product_count'] }} produk
                                        </td>
                                        <td class="px-4 py-3 text-sm">
                                            {{ $comparison['certification_count'] }} sertifikat
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center">
                                                <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2 mr-2" style="width: 100px;">
                                                    <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $comparison['quality_score'] }}%"></div>
                                                </div>
                                                <span class="text-sm font-semibold">{{ number_format($comparison['quality_score'], 1) }}</span>
                                            </div>
                                        </td>
                                        @if($request->latitude && $request->longitude)
                                            <td class="px-4 py-3 text-sm">
                                                @if($comparison['distance'])
                                                    {{ number_format($comparison['distance'], 2) }} km
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($comparison['delivery_price_per_km'])
                                                    Rp {{ number_format($comparison['delivery_price_per_km'], 0, ',', '.') }}/km
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-4 py-3 text-sm">
                                                @if($comparison['delivery_cost'])
                                                    <span class="font-semibold text-primary-600">
                                                        Rp {{ number_format($comparison['delivery_cost'], 0, ',', '.') }}
                                                    </span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-4 py-3">
                                            <a href="{{ route('factories.show', $comparison['factory']) }}" class="text-primary-600 hover:underline text-sm">
                                                Detail
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </x-card>
            @else
                <x-card>
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                        Tidak ada pabrik untuk dibandingkan.
                    </p>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

