<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Quote Requests Saya
            </h2>
            @if($quoteRequests->where('status', 'quoted')->count() > 1)
                <a href="{{ route('buyer.quote-requests.compare') }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                    Bandingkan Quotes
                </a>
            @endif
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('buyer.quote-requests.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'Semua Status',
                        'pending' => 'Pending',
                        'quoted' => 'Quoted',
                        'accepted' => 'Accepted',
                        'rejected' => 'Rejected',
                        'cancelled' => 'Cancelled'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('buyer.quote-requests.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($quoteRequests->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($quoteRequests as $quoteRequest)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-start space-x-4">
                                @if($quoteRequest->service->preview_image)
                                    <img src="{{ Storage::url($quoteRequest->service->preview_image) }}" 
                                         alt="{{ $quoteRequest->service->title }}"
                                         class="w-20 h-20 object-cover rounded-lg">
                                @endif
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-1">
                                        {{ $quoteRequest->service->title }}
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                        Seller: <span class="font-medium">{{ $quoteRequest->service->user->name }}</span>
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-2">
                                        {{ Str::limit($quoteRequest->message, 150) }}
                                    </p>
                                    @if($quoteRequest->quoted_price)
                                        <p class="text-lg font-bold text-primary-600 dark:text-primary-400 mb-2">
                                            Rp {{ number_format($quoteRequest->quoted_price, 0, ',', '.') }}
                                        </p>
                                    @endif
                                    <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                        @if($quoteRequest->budget)
                                            <span>Budget: Rp {{ number_format($quoteRequest->budget, 0, ',', '.') }}</span>
                                        @endif
                                        @if($quoteRequest->deadline)
                                            <span>Deadline: {{ $quoteRequest->deadline->format('d M Y') }}</span>
                                        @endif
                                        <span>{{ $quoteRequest->created_at->format('d M Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="ml-4 flex flex-col items-end space-y-2">
                            <x-badge :variant="match($quoteRequest->status) {
                                'pending' => 'warning',
                                'quoted' => 'info',
                                'accepted' => 'success',
                                'rejected' => 'danger',
                                'cancelled' => 'default',
                                default => 'default'
                            }">
                                {{ ucfirst($quoteRequest->status) }}
                            </x-badge>
                            <a href="{{ route('buyer.quote-requests.show', $quoteRequest) }}" 
                               class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                Lihat Detail
                            </a>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $quoteRequests->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada quote request</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum membuat permintaan quote.</p>
                <div class="mt-6">
                    <a href="{{ route('services.index') }}">
                        <x-button variant="primary">Jelajahi Jasa</x-button>
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

