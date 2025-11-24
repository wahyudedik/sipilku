<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Quote Request
            </h2>
            <a href="{{ route('seller.quote-requests.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Service Info -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Informasi Jasa</h3>
                </x-slot>
                <div class="flex items-start space-x-4">
                    @if($quoteRequest->service->preview_image)
                        <img src="{{ Storage::url($quoteRequest->service->preview_image) }}" 
                             alt="{{ $quoteRequest->service->title }}"
                             class="w-24 h-24 object-cover rounded-lg">
                    @endif
                    <div>
                        <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                            {{ $quoteRequest->service->title }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            {{ $quoteRequest->service->short_description ?? Str::limit($quoteRequest->service->description, 100) }}
                        </p>
                    </div>
                </div>
            </x-card>

            <!-- Buyer Request -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Permintaan dari Buyer</h3>
                </x-slot>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Buyer</p>
                        <p class="font-medium text-gray-900 dark:text-gray-100">{{ $quoteRequest->user->name }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $quoteRequest->user->email }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pesan/Kebutuhan</p>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $quoteRequest->message }}</p>
                        </div>
                    </div>

                    @if($quoteRequest->requirements && count($quoteRequest->requirements) > 0)
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Requirements/Spesifikasi</p>
                            <ul class="list-disc list-inside space-y-1">
                                @foreach($quoteRequest->requirements as $requirement)
                                    <li class="text-gray-700 dark:text-gray-300">{{ $requirement }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        @if($quoteRequest->budget)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Budget</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    Rp {{ number_format($quoteRequest->budget, 0, ',', '.') }}
                                </p>
                            </div>
                        @endif
                        @if($quoteRequest->deadline)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Deadline</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $quoteRequest->deadline->format('d M Y') }}
                                </p>
                            </div>
                        @endif
                    </div>

                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tanggal Request</p>
                        <p class="text-gray-700 dark:text-gray-300">{{ $quoteRequest->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </x-card>

            <!-- Quote Response (if already quoted) -->
            @if($quoteRequest->status === 'quoted' || $quoteRequest->status === 'accepted' || $quoteRequest->status === 'rejected')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Quote Response</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Harga Quote</p>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($quoteRequest->quoted_price, 0, ',', '.') }}
                            </p>
                        </div>
                        @if($quoteRequest->quote_message)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pesan</p>
                                <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                    <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ $quoteRequest->quote_message }}</p>
                                </div>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tanggal Quote</p>
                            <p class="text-gray-700 dark:text-gray-300">{{ $quoteRequest->quoted_at->format('d M Y H:i') }}</p>
                        </div>
                    </div>
                </x-card>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Status -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Status</h3>
                </x-slot>
                <div class="space-y-3">
                    <x-badge :variant="match($quoteRequest->status) {
                        'pending' => 'warning',
                        'quoted' => 'info',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        'cancelled' => 'default',
                        default => 'default'
                    }" size="lg" class="w-full justify-center">
                        {{ ucfirst($quoteRequest->status) }}
                    </x-badge>
                </div>
            </x-card>

            <!-- Actions -->
            @if($quoteRequest->status === 'pending')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Berikan Quote</h3>
                    </x-slot>
                    <form action="{{ route('seller.quote-requests.respond', $quoteRequest) }}" method="POST">
                        @csrf
                        <div class="space-y-4">
                            <x-form-group label="Harga Quote" name="quoted_price" required>
                                <x-text-input name="quoted_price" type="number" step="0.01" min="0" value="{{ old('quoted_price') }}" placeholder="0" />
                            </x-form-group>
                            <x-form-group label="Pesan Quote" name="quote_message" required help="Jelaskan detail quote, scope of work, dan informasi lainnya">
                                <x-textarea-input name="quote_message" rows="6" placeholder="Saya akan mengerjakan proyek ini dengan...">{{ old('quote_message') }}</x-textarea-input>
                            </x-form-group>
                            <x-button variant="primary" size="md" type="submit" class="w-full">
                                Kirim Quote
                            </x-button>
                        </div>
                    </form>
                </x-card>
            @endif

            @if($quoteRequest->status === 'accepted')
                <x-alert type="success">
                    Quote telah diterima oleh buyer. Silakan lanjutkan ke proses order.
                </x-alert>
            @endif

            @if($quoteRequest->status === 'rejected')
                <x-alert type="error">
                    Quote ditolak oleh buyer.
                </x-alert>
            @endif
        </div>
    </div>
</x-app-with-sidebar>

