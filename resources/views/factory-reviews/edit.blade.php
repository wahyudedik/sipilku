<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Edit Review untuk {{ $factory->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Edit Review</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $factory->factoryType->name ?? 'Factory' }}
                    </p>
                </x-slot>
                <form action="{{ route('factory-reviews.update', [$factory, $review]) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-6">
                        <!-- Overall Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rating Keseluruhan *
                            </label>
                            <div class="flex items-center space-x-2" id="ratingContainer">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" class="hidden" {{ old('rating', $review->rating) == $i ? 'checked' : '' }} required>
                                    <label for="rating{{ $i }}" class="cursor-pointer">
                                        <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 transition-colors rating-star" data-rating="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                        </div>

                        <!-- Quality Ratings -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                                Rating Detail *
                            </label>
                            <div class="space-y-4">
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400 mb-2 block">Kualitas Produk</label>
                                    <div class="flex items-center space-x-2" id="productQualityContainer">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="ratings_breakdown[product_quality]" value="{{ $i }}" id="product_quality{{ $i }}" class="hidden" {{ old('ratings_breakdown.product_quality', $review->ratings_breakdown['product_quality'] ?? '') == $i ? 'checked' : '' }}>
                                            <label for="product_quality{{ $i }}" class="cursor-pointer">
                                                <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors quality-star" data-rating="{{ $i }}" data-category="product_quality" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400 mb-2 block">Kualitas Delivery</label>
                                    <div class="flex items-center space-x-2" id="deliveryQualityContainer">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="ratings_breakdown[delivery_quality]" value="{{ $i }}" id="delivery_quality{{ $i }}" class="hidden" {{ old('ratings_breakdown.delivery_quality', $review->ratings_breakdown['delivery_quality'] ?? '') == $i ? 'checked' : '' }}>
                                            <label for="delivery_quality{{ $i }}" class="cursor-pointer">
                                                <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors quality-star" data-rating="{{ $i }}" data-category="delivery_quality" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400 mb-2 block">Kualitas Pelayanan</label>
                                    <div class="flex items-center space-x-2" id="serviceQualityContainer">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="ratings_breakdown[service_quality]" value="{{ $i }}" id="service_quality{{ $i }}" class="hidden" {{ old('ratings_breakdown.service_quality', $review->ratings_breakdown['service_quality'] ?? '') == $i ? 'checked' : '' }}>
                                            <label for="service_quality{{ $i }}" class="cursor-pointer">
                                                <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors quality-star" data-rating="{{ $i }}" data-category="service_quality" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400 mb-2 block">Harga (Value for Money)</label>
                                    <div class="flex items-center space-x-2" id="priceContainer">
                                        @for($i = 5; $i >= 1; $i--)
                                            <input type="radio" name="ratings_breakdown[price]" value="{{ $i }}" id="price{{ $i }}" class="hidden" {{ old('ratings_breakdown.price', $review->ratings_breakdown['price'] ?? '') == $i ? 'checked' : '' }}>
                                            <label for="price{{ $i }}" class="cursor-pointer">
                                                <svg class="w-6 h-6 text-gray-300 hover:text-yellow-400 transition-colors quality-star" data-rating="{{ $i }}" data-category="price" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                </svg>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comment -->
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Komentar (Opsional)
                            </label>
                            <textarea name="comment" id="comment" rows="5" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Bagikan pengalaman Anda dengan pabrik ini...">{{ old('comment', $review->comment) }}</textarea>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('factories.show', $factory) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Batal
                            </a>
                            <x-button type="submit" variant="primary">
                                Update Review
                            </x-button>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingStars = document.querySelectorAll('.rating-star');
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            const qualityStars = document.querySelectorAll('.quality-star');

            function updateStars(selectedRating, stars, inputs) {
                stars.forEach(star => {
                    const starRating = parseInt(star.dataset.rating);
                    if (starRating <= selectedRating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
            }

            ratingStars.forEach(star => {
                if (star.closest('#ratingContainer')) {
                    star.addEventListener('click', function() {
                        const rating = parseInt(this.dataset.rating);
                        document.getElementById('rating' + rating).checked = true;
                        updateStars(rating, Array.from(document.querySelectorAll('#ratingContainer .rating-star')), ratingInputs);
                    });
                }
            });

            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    updateStars(parseInt(this.value), Array.from(document.querySelectorAll('#ratingContainer .rating-star')), ratingInputs);
                });
            });

            qualityStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    const category = this.dataset.category;
                    const inputId = category + rating;
                    const input = document.getElementById(inputId);
                    if (input) {
                        input.checked = true;
                        const container = this.closest('[id$="Container"]');
                        if (container) {
                            const containerStars = container.querySelectorAll('.quality-star');
                            updateStars(rating, Array.from(containerStars), [input]);
                        }
                    }
                });
            });

            const checkedInput = document.querySelector('input[name="rating"]:checked');
            if (checkedInput) {
                updateStars(parseInt(checkedInput.value), Array.from(document.querySelectorAll('#ratingContainer .rating-star')), ratingInputs);
            }

            ['product_quality', 'delivery_quality', 'service_quality', 'price'].forEach(category => {
                const checked = document.querySelector(`input[name="ratings_breakdown[${category}]"]:checked`);
                if (checked) {
                    const rating = parseInt(checked.value);
                    const container = document.getElementById(category + 'Container') || document.getElementById(category.replace('_', '') + 'Container');
                    if (container) {
                        const containerStars = container.querySelectorAll('.quality-star');
                        updateStars(rating, Array.from(containerStars), [checked]);
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>

