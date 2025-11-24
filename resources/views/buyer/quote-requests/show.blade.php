<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Quote Request
            </h2>
            <a href="{{ route('buyer.quote-requests.index') }}">
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
                    <div class="flex-1">
                        <h4 class="font-semibold text-lg text-gray-900 dark:text-gray-100">
                            {{ $quoteRequest->service->title }}
                        </h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                            Seller: {{ $quoteRequest->service->user->name }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            {{ $quoteRequest->service->short_description ?? Str::limit($quoteRequest->service->description, 100) }}
                        </p>
                        <a href="{{ route('services.show', $quoteRequest->service) }}" 
                           class="text-primary-600 hover:text-primary-800 dark:text-primary-400 text-sm mt-2 inline-block">
                            Lihat detail jasa →
                        </a>
                    </div>
                </div>
            </x-card>

            <!-- Your Request -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Permintaan Anda</h3>
                </x-slot>
                <div class="space-y-4">
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

            <!-- Quote Response -->
            @if($quoteRequest->status === 'quoted')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Quote dari Seller</h3>
                    </x-slot>
                    <div class="space-y-4">
                        <div>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Harga Quote</p>
                            <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                                Rp {{ number_format($quoteRequest->quoted_price, 0, ',', '.') }}
                            </p>
                        </div>
                        @if($quoteRequest->quote_message)
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Pesan dari Seller</p>
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

            @if($quoteRequest->status === 'accepted')
                <x-alert type="success">
                    <strong>Quote diterima!</strong> Silakan lanjutkan ke proses order.
                </x-alert>
            @endif

            @if($quoteRequest->status === 'rejected')
                <x-alert type="error">
                    Quote telah ditolak.
                </x-alert>
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
            @if($quoteRequest->status === 'quoted')
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Aksi</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <form action="{{ route('buyer.quote-requests.accept', $quoteRequest) }}" method="POST">
                            @csrf
                            <x-button variant="success" size="md" type="submit" class="w-full">
                                ✓ Terima Quote
                            </x-button>
                        </form>
                        <form action="{{ route('buyer.quote-requests.reject', $quoteRequest) }}" method="POST"
                              onsubmit="return confirm('Apakah Anda yakin ingin menolak quote ini?')">
                            @csrf
                            <x-button variant="danger" size="md" type="submit" class="w-full">
                                ✗ Tolak Quote
                            </x-button>
                        </form>
                    </div>
                </x-card>
            @endif

            @if($quoteRequest->status === 'pending')
                <x-alert type="info">
                    Menunggu response dari seller...
                </x-alert>
            @endif
        </div>
    </div>
</x-app-with-sidebar>

