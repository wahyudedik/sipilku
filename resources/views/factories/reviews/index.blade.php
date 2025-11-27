<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Reviews & Ratings - {{ $factory->name }}
            </h2>
            <a href="{{ route('factories.dashboard', $factory) }}">
                <x-button variant="secondary" size="sm">Back to Dashboard</x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <x-card>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Total Reviews</p>
                    <p class="text-2xl font-bold">{{ $stats['total'] }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Approved</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['approved'] }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Pending</p>
                    <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['pending'] }}</p>
                </x-card>
                <x-card>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Average Rating</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        {{ number_format($stats['average_rating'], 1) }}/5
                    </p>
                </x-card>
            </div>

            <!-- Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('factories.reviews.index', $factory) }}" class="flex items-center space-x-4">
                    <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Reviews</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </form>
            </x-card>

            <!-- Reviews List -->
            @if($reviews->count() > 0)
                <div class="space-y-4">
                    @foreach($reviews as $review)
                        <x-card>
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <p class="font-semibold">{{ $review->user->name }}</p>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            @endfor
                                        </div>
                                        <x-badge :variant="$review->is_approved ? 'success' : 'warning'" size="xs">
                                            {{ $review->is_approved ? 'Approved' : 'Pending' }}
                                        </x-badge>
                                    </div>
                                    @if($review->comment)
                                        <p class="text-gray-700 dark:text-gray-300 mb-2">{{ $review->comment }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $review->created_at->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $reviews->links() }}
                </div>
            @else
                <x-card>
                    <p class="text-center text-gray-500 dark:text-gray-400 py-8">No reviews found.</p>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

