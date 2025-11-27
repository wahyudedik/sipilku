<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Beri Review untuk {{ $store->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Tulis Review</h3>
                </x-slot>
                <form action="{{ route('store-reviews.store', $store) }}" method="POST">
                    @csrf
                    <div class="space-y-6">
                        <!-- Overall Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rating Keseluruhan *
                            </label>
                            <div class="flex items-center space-x-2" id="ratingContainer">
                                @for($i = 5; $i >= 1; $i--)
                                    <input type="radio" name="rating" value="{{ $i }}" id="rating{{ $i }}" class="hidden" {{ old('rating') == $i ? 'checked' : '' }} required>
                                    <label for="rating{{ $i }}" class="cursor-pointer">
                                        <svg class="w-8 h-8 text-gray-300 hover:text-yellow-400 transition-colors rating-star" data-rating="{{ $i }}" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    </label>
                                @endfor
                            </div>
                            <x-input-error :messages="$errors->get('rating')" class="mt-2" />
                        </div>

                        <!-- Detailed Ratings (Optional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Rating Detail (Opsional)
                            </label>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Kualitas Produk</label>
                                    <select name="ratings_breakdown[product_quality]" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                                        <option value="">Pilih rating</option>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ old('ratings_breakdown.product_quality') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Pelayanan</label>
                                    <select name="ratings_breakdown[service]" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                                        <option value="">Pilih rating</option>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ old('ratings_breakdown.service') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                                        @endfor
                                    </select>
                                </div>
                                <div>
                                    <label class="text-sm text-gray-600 dark:text-gray-400">Harga</label>
                                    <select name="ratings_breakdown[price]" class="mt-1 w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800">
                                        <option value="">Pilih rating</option>
                                        @for($i = 5; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ old('ratings_breakdown.price') == $i ? 'selected' : '' }}>{{ $i }} Bintang</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Comment -->
                        <div>
                            <label for="comment" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Komentar (Opsional)
                            </label>
                            <textarea name="comment" id="comment" rows="5" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500" placeholder="Bagikan pengalaman Anda dengan toko ini...">{{ old('comment') }}</textarea>
                            <x-input-error :messages="$errors->get('comment')" class="mt-2" />
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('stores.show', $store) }}" class="px-4 py-2 border border-gray-300 dark:border-gray-700 rounded-md text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
                                Batal
                            </a>
                            <x-button type="submit" variant="primary">
                                Kirim Review
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

            // Update star colors based on selected rating
            function updateStars(selectedRating) {
                ratingStars.forEach(star => {
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

            // Handle star click
            ratingStars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = parseInt(this.dataset.rating);
                    document.getElementById('rating' + rating).checked = true;
                    updateStars(rating);
                });
            });

            // Handle input change
            ratingInputs.forEach(input => {
                input.addEventListener('change', function() {
                    updateStars(parseInt(this.value));
                });
            });

            // Initialize stars based on old input or default
            const checkedInput = document.querySelector('input[name="rating"]:checked');
            if (checkedInput) {
                updateStars(parseInt(checkedInput.value));
            }
        });
    </script>
    @endpush
</x-app-layout>

