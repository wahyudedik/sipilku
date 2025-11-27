<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Review Details
            </h2>
            <a href="{{ route('store.reviews.index') }}">
                <x-button variant="secondary" size="sm">Back to Reviews</x-button>
            </a>
        </div>
    </x-slot>

    <x-card>
        <div class="space-y-6">
            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Customer</p>
                <p class="font-semibold text-lg">{{ $review->user->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Rating</p>
                <div class="flex items-center space-x-1">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-8 h-8 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                    @endfor
                    <span class="ml-2 text-lg font-semibold">{{ $review->rating }}/5</span>
                </div>
            </div>

            @if($review->comment)
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Comment</p>
                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $review->comment }}</p>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Status</p>
                    <x-badge :variant="$review->is_approved ? 'success' : 'warning'">
                        {{ $review->is_approved ? 'Approved' : 'Pending Approval' }}
                    </x-badge>
                </div>
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Date</p>
                    <p class="font-semibold">{{ $review->created_at->format('d M Y H:i') }}</p>
                </div>
            </div>
        </div>
    </x-card>
</x-app-with-sidebar>

