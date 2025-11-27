@props(['recommendations', 'title' => 'Rekomendasi Pabrik', 'maxItems' => 6])

@php
use Illuminate\Support\Facades\Storage;

// Limit items if specified
$displayRecommendations = $maxItems ? $recommendations->take($maxItems) : $recommendations;
@endphp

@if($displayRecommendations->count() > 0)
    <div class="space-y-3">
        @if($title)
            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 flex items-center space-x-2">
                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <span>{{ $title }}</span>
            </h4>
        @endif
        <div class="space-y-2">
            @foreach($displayRecommendations as $recommendation)
                <a href="{{ route('factories.show', $recommendation['factory']) }}" 
                   class="block p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:shadow-md hover:border-primary-500 transition group">
                    <div class="flex items-center space-x-3">
                        @if($recommendation['factory']->logo)
                            <img src="{{ Storage::url($recommendation['factory']->logo) }}" 
                                 alt="{{ $recommendation['factory']->name }}" 
                                 class="w-12 h-12 object-cover rounded flex-shrink-0">
                        @else
                            <div class="w-12 h-12 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h5 class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate group-hover:text-primary-600 transition">
                                    {{ $recommendation['factory']->name }}
                                </h5>
                                @if(isset($recommendation['recommendation_score']) && $recommendation['recommendation_score'] >= 80)
                                    <span class="text-xs text-green-600 dark:text-green-400 font-semibold flex-shrink-0">★</span>
                                @endif
                            </div>
                            @if($recommendation['factory']->factoryType)
                                <x-badge variant="default" size="xs" class="mt-1 text-xs">
                                    {{ $recommendation['factory']->factoryType->name }}
                                </x-badge>
                            @endif
                            <div class="flex items-center space-x-3 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                @if(isset($recommendation['distance']))
                                    <span>{{ number_format($recommendation['distance'], 1) }} km</span>
                                @endif
                                @if($recommendation['factory']->rating > 0)
                                    <span class="flex items-center">
                                        <span class="text-yellow-400 text-xs">★</span>
                                        <span class="ml-0.5">{{ number_format($recommendation['factory']->rating, 1) }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @if($recommendations->count() > $maxItems)
            <a href="{{ route('factories.recommendations', request()->only(['latitude', 'longitude'])) }}" 
               class="block text-center text-sm text-primary-600 dark:text-primary-400 hover:underline pt-2">
                Lihat {{ $recommendations->count() - $maxItems }} lainnya →
            </a>
        @endif
    </div>
@endif

