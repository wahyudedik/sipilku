@props(['recommendations', 'title' => 'Rekomendasi Pabrik Terdekat', 'showViewAll' => false])

@php
use Illuminate\Support\Facades\Storage;

// Group by factory type for better organization
$groupedByType = $recommendations->groupBy(function($item) {
    return $item['factory']->factoryType ? $item['factory']->factoryType->name : 'Lainnya';
});

// Get unique factory types count
$factoryTypesCount = $recommendations->pluck('factory.factoryType.name')->filter()->unique()->count();
@endphp

@if($recommendations->count() > 0)
    <x-card class="mt-6">
        <x-slot name="header">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">{{ $title }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $recommendations->count() }} pabrik dari {{ $factoryTypesCount }} tipe berbeda
                        </p>
                    </div>
                </div>
                @if($showViewAll)
                    <a href="{{ route('factories.recommendations', request()->only(['latitude', 'longitude'])) }}" 
                       class="text-sm text-primary-600 dark:text-primary-400 hover:underline">
                        Lihat Semua →
                    </a>
                @endif
            </div>
        </x-slot>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recommendations as $recommendation)
                <a href="{{ route('factories.show', $recommendation['factory']) }}" 
                   class="group block border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-lg hover:border-primary-500 dark:hover:border-primary-400 transition-all duration-200 bg-white dark:bg-gray-800">
                    <div class="flex items-start space-x-3">
                        <!-- Factory Logo -->
                        <div class="flex-shrink-0">
                            @if($recommendation['factory']->logo)
                                <img src="{{ Storage::url($recommendation['factory']->logo) }}" 
                                     alt="{{ $recommendation['factory']->name }}" 
                                     class="w-16 h-16 object-cover rounded-lg ring-2 ring-gray-200 dark:ring-gray-700 group-hover:ring-primary-500 transition">
                            @else
                                <div class="w-16 h-16 bg-gradient-to-br from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-800 rounded-lg flex items-center justify-center ring-2 ring-gray-200 dark:ring-gray-700 group-hover:ring-primary-500 transition">
                                    <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Factory Info -->
                        <div class="flex-1 min-w-0">
                            <!-- Factory Name & Type Badge -->
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <h4 class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">
                                    {{ $recommendation['factory']->name }}
                                </h4>
                                @if(isset($recommendation['recommendation_score']))
                                    <div class="flex-shrink-0">
                                        <div class="px-2 py-0.5 bg-primary-100 dark:bg-primary-900/30 rounded-full">
                                            <span class="text-xs font-bold text-primary-700 dark:text-primary-300">
                                                {{ number_format($recommendation['recommendation_score'], 0) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Factory Type Badge -->
                            @if($recommendation['factory']->factoryType)
                                <div class="mb-2">
                                    <x-badge variant="default" size="xs" class="text-xs">
                                        {{ $recommendation['factory']->factoryType->name }}
                                    </x-badge>
                                </div>
                            @endif
                            
                            <!-- Distance & Location -->
                            <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-400 mb-2">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="font-medium">{{ number_format($recommendation['distance'], 1) }} km</span>
                            </div>
                            
                            <!-- Delivery Cost -->
                            @if($recommendation['delivery_cost'] > 0)
                                <div class="flex items-center space-x-2 text-xs text-gray-600 dark:text-gray-400 mb-2">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                    </svg>
                                    <span>Ongkir: <span class="font-semibold text-gray-900 dark:text-gray-100">Rp {{ number_format($recommendation['delivery_cost'], 0, ',', '.') }}</span></span>
                                </div>
                            @endif
                            
                            <!-- Rating & Reviews -->
                            @if($recommendation['factory']->rating > 0)
                                <div class="flex items-center justify-between pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <div class="flex items-center space-x-1">
                                        <svg class="w-4 h-4 text-yellow-400 fill-current" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-xs font-semibold text-gray-900 dark:text-gray-100">
                                            {{ number_format($recommendation['factory']->rating, 1) }}
                                        </span>
                                        @if($recommendation['factory']->total_reviews > 0)
                                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                                ({{ $recommendation['factory']->total_reviews }})
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Recommendation Score Badge (if high score) -->
                                    @if(isset($recommendation['recommendation_score']) && $recommendation['recommendation_score'] >= 80)
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-3 h-3 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="text-xs font-medium text-green-600 dark:text-green-400">Terbaik</span>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <!-- Factory Types Summary -->
        @if($factoryTypesCount > 1)
            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">Tipe pabrik yang tersedia:</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($groupedByType->keys() as $typeName)
                        <x-badge variant="default" size="xs" class="text-xs">
                            {{ $typeName }} ({{ $groupedByType[$typeName]->count() }})
                        </x-badge>
                    @endforeach
                </div>
            </div>
        @endif
    </x-card>
@endif

