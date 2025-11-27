@props(['recommendations'])

@if($recommendations->count() > 0)
    <x-card class="mt-6">
        <x-slot name="header">
            <div class="flex items-center space-x-2">
                <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Rekomendasi Toko Terdekat</h3>
            </div>
        </x-slot>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recommendations as $recommendation)
                <a href="{{ route('stores.show', $recommendation['store']) }}" 
                   class="block border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:shadow-md hover:border-primary-500 transition">
                    <div class="flex items-start space-x-3">
                        @if($recommendation['store']->logo)
                            <img src="{{ Storage::url($recommendation['store']->logo) }}" 
                                 alt="{{ $recommendation['store']->name }}" 
                                 class="w-16 h-16 object-cover rounded">
                        @else
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <h4 class="font-semibold text-sm text-gray-900 dark:text-gray-100 truncate">
                                {{ $recommendation['store']->name }}
                            </h4>
                            <div class="mt-1 flex items-center space-x-2 text-xs text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span>{{ number_format($recommendation['distance'], 1) }} km</span>
                            </div>
                            @if($recommendation['store']->rating > 0)
                                <div class="mt-1 flex items-center">
                                    <span class="text-yellow-400 text-xs">★</span>
                                    <span class="text-xs text-gray-600 dark:text-gray-400 ml-1">
                                        {{ $recommendation['store']->rating }}/5
                                    </span>
                                    @if($recommendation['store']->total_reviews > 0)
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-1">
                                            ({{ $recommendation['store']->total_reviews }})
                                        </span>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </x-card>
@endif

