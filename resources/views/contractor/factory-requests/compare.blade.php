@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Compare Factory Quotes
        </h2>
    </x-slot>

    @if($comparisons->count() > 0)
        <x-card class="mb-6">
            <x-slot name="header">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-medium">Quote Comparison ({{ $comparisons->count() }} factories)</h3>
                    @if($projectLocation)
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Project: {{ $projectLocation->name }}
                        </p>
                    @endif
                </div>
            </x-slot>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Factory</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Type</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Product Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Delivery</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Additional Fees</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Total Cost</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Distance</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Quality Score</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Rating</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($comparisons as $comparison)
                            @php
                                $factoryRequest = $comparison['factory_request'];
                                $factory = $comparison['factory'];
                                $breakdown = $comparison['cost_breakdown'];
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 {{ $loop->first ? 'bg-primary-50 dark:bg-primary-900/20' : '' }}">
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        @if($factory->logo)
                                            <img src="{{ Storage::url($factory->logo) }}" alt="{{ $factory->name }}" class="w-10 h-10 object-cover rounded">
                                        @endif
                                        <div>
                                            <p class="font-medium text-sm">{{ $factory->name }}</p>
                                            @if($loop->first)
                                                <span class="text-xs text-green-600 dark:text-green-400 font-semibold">Best Price</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($factory->factoryType)
                                        {{ $factory->factoryType->name }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    Rp {{ number_format($breakdown['product_price'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($breakdown['delivery_cost'] > 0)
                                        Rp {{ number_format($breakdown['delivery_cost'], 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($breakdown['additional_fees'] > 0)
                                        Rp {{ number_format($breakdown['additional_fees'], 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-primary-600 dark:text-primary-400">
                                        Rp {{ number_format($comparison['total_cost'], 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($comparison['distance'])
                                        {{ number_format($comparison['distance'], 2) }} km
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center space-x-2">
                                        <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2" style="width: 60px;">
                                            <div class="bg-primary-600 h-2 rounded-full" style="width: {{ $comparison['quality_score'] }}%"></div>
                                        </div>
                                        <span class="text-xs font-semibold">{{ number_format($comparison['quality_score'], 1) }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm">
                                    @if($comparison['rating'] > 0)
                                        <div class="flex items-center">
                                            <span class="text-yellow-400 text-xs">★</span>
                                            <span class="ml-1">{{ number_format($comparison['rating'], 1) }}/5</span>
                                            <span class="text-xs text-gray-500 ml-1">({{ $comparison['total_reviews'] }})</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('contractor.factory-requests.show', $factoryRequest) }}" class="text-primary-600 hover:underline text-sm">
                                            View
                                        </a>
                                        @if($factoryRequest->status === 'quoted')
                                            <form action="{{ route('contractor.factory-requests.accept', $factoryRequest) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="text-green-600 hover:underline text-sm">Accept</button>
                                            </form>
                                        @endif
                                    </div>
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
                No quotes available for comparison.
            </p>
        </x-card>
    @endif
</x-app-with-sidebar>

