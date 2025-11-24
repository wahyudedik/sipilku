<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Jasa Saya
            </h2>
            <a href="{{ route('seller.services.create') }}">
                <x-button variant="primary" size="md">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Jasa
                </x-button>
            </a>
        </div>
    </x-slot>

    @if($services->count() > 0)
        <div class="grid grid-cols-1 gap-4">
            @foreach($services as $service)
                <x-card>
                    <div class="flex items-start space-x-4">
                        <!-- Preview Image -->
                        <div class="flex-shrink-0">
                            @if($service->preview_image)
                                <img src="{{ Storage::url($service->preview_image) }}" 
                                     alt="{{ $service->title }}"
                                     class="w-24 h-24 object-cover rounded-lg">
                            @else
                                <div class="w-24 h-24 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Service Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $service->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                        {{ $service->short_description ?? Str::limit($service->description, 100) }}
                                    </p>
                                    <div class="mt-2 flex items-center space-x-4">
                                        @if($service->package_prices && count($service->package_prices) > 0)
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                                Mulai dari: <span class="font-bold text-primary-600 dark:text-primary-400">
                                                    Rp {{ number_format(min(array_column($service->package_prices, 'price')), 0, ',', '.') }}
                                                </span>
                                            </span>
                                        @endif
                                        @if($service->package_prices)
                                            <span class="text-sm text-gray-500">{{ count($service->package_prices) }} paket</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="ml-4 flex items-center space-x-2">
                                    <x-badge :variant="match($service->status) {
                                        'approved' => 'success',
                                        'pending' => 'warning',
                                        'rejected' => 'danger',
                                        default => 'default'
                                    }">
                                        {{ ucfirst($service->status) }}
                                    </x-badge>
                                </div>
                            </div>

                            <div class="mt-4 flex items-center justify-between">
                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    @if($service->category)
                                        <span>{{ $service->category->name }}</span>
                                    @endif
                                    <span>{{ $service->completed_orders }} pesanan selesai</span>
                                    @if($service->rating > 0)
                                        <span>{{ number_format($service->rating, 1) }} ⭐</span>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('seller.services.show', $service) }}" 
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        Lihat
                                    </a>
                                    <a href="{{ route('seller.services.edit', $service) }}" 
                                       class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                        Edit
                                    </a>
                                    <form action="{{ route('seller.services.destroy', $service) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus jasa ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $services->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada jasa</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat jasa pertama Anda.</p>
                <div class="mt-6">
                    <a href="{{ route('seller.services.create') }}">
                        <x-button variant="primary">
                            Tambah Jasa
                        </x-button>
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

