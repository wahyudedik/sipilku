<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Reviews & Ratings Management
            </h2>
            <a href="{{ route('store.dashboard') }}">
                <x-button variant="secondary" size="sm">Back to Dashboard</x-button>
            </a>
        </div>
    </x-slot>

    <!-- Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Reviews</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $stats['total'] }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Approved</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['approved'] }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Pending</p>
                <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
            </div>
        </x-card>
        <x-card>
            <div class="text-center">
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Average Rating</p>
                <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                    {{ number_format($stats['average_rating'], 1) }}/5
                </p>
            </div>
        </x-card>
    </div>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('store.reviews.index') }}" class="flex flex-wrap gap-4">
            <div class="w-48">
                <x-form-group label="Status" name="status">
                    <x-select-input name="status">
                        <option value="">All Status</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </x-select-input>
                </x-form-group>
            </div>
            <div class="w-48">
                <x-form-group label="Rating" name="rating">
                    <x-select-input name="rating">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') === '5' ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') === '4' ? 'selected' : '' }}>4 Stars</option>
                        <option value="3" {{ request('rating') === '3' ? 'selected' : '' }}>3 Stars</option>
                        <option value="2" {{ request('rating') === '2' ? 'selected' : '' }}>2 Stars</option>
                        <option value="1" {{ request('rating') === '1' ? 'selected' : '' }}>1 Star</option>
                    </x-select-input>
                </x-form-group>
            </div>
            <div class="flex items-end">
                <x-button variant="primary" type="submit">Filter</x-button>
            </div>
        </form>
    </x-card>

    <!-- Reviews List -->
    <x-card>
        @if($reviews->count() > 0)
            <div class="space-y-4">
                @foreach($reviews as $review)
                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <p class="font-semibold">{{ $review->user->name }}</p>
                                    <div class="flex items-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                            </svg>
                                        @endfor
                                    </div>
                                    <x-badge :variant="$review->is_approved ? 'success' : 'warning'">
                                        {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                    </x-badge>
                                </div>
                                @if($review->comment)
                                    <p class="text-gray-700 dark:text-gray-300">{{ $review->comment }}</p>
                                @endif
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500">{{ $review->created_at->format('d M Y') }}</p>
                                <a href="{{ route('store.reviews.show', $review) }}" class="text-sm text-primary-600 hover:text-primary-800 mt-2 inline-block">
                                    View Details →
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500 dark:text-gray-400">No reviews found</p>
            </div>
        @endif
    </x-card>
</x-app-with-sidebar>

