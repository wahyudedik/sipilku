<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Coupon Management
            </h2>
            <a href="{{ route('admin.coupons.create') }}">
                <x-button variant="primary" size="sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Kupon
                </x-button>
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.coupons.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari kupon..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        'all' => 'Semua Status',
                        'active' => 'Active',
                        'expired' => 'Expired',
                        'inactive' => 'Inactive'
                    ]" 
                    value="{{ request('status', 'all') }}" />
            </div>
            <div>
                <x-select-input 
                    name="type" 
                    :options="[
                        'all' => 'Semua Tipe',
                        'percentage' => 'Percentage',
                        'fixed' => 'Fixed'
                    ]" 
                    value="{{ request('type', 'all') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('admin.coupons.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($coupons->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($coupons as $coupon)
                <x-card>
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                {{ $coupon->name }}
                            </h3>
                            <p class="text-2xl font-bold text-primary-600 dark:text-primary-400 mt-2">
                                {{ $coupon->code }}
                            </p>
                        </div>
                        <x-badge :variant="$coupon->isValid() ? 'success' : 'default'" size="sm">
                            {{ $coupon->isValid() ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </div>

                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Type:</span>
                            <span class="font-semibold">{{ ucfirst($coupon->type) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Value:</span>
                            <span class="font-semibold">
                                @if($coupon->type === 'percentage')
                                    {{ $coupon->value }}%
                                @else
                                    Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                @endif
                            </span>
                        </div>
                        @if($coupon->minimum_purchase)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Min Purchase:</span>
                                <span class="font-semibold">Rp {{ number_format($coupon->minimum_purchase, 0, ',', '.') }}</span>
                            </div>
                        @endif
                        @if($coupon->usage_limit)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Usage:</span>
                                <span class="font-semibold">{{ $coupon->usage_count }} / {{ $coupon->usage_limit }}</span>
                            </div>
                        @else
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Usage:</span>
                                <span class="font-semibold">{{ $coupon->usage_count }} times</span>
                            </div>
                        @endif
                        @if($coupon->expires_at)
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Expires:</span>
                                <span class="font-semibold {{ $coupon->expires_at->isPast() ? 'text-red-600' : '' }}">
                                    {{ $coupon->expires_at->format('d M Y') }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <a href="{{ route('admin.coupons.edit', $coupon) }}" 
                               class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                Edit
                            </a>
                            <form action="{{ route('admin.coupons.destroy', $coupon) }}" 
                                  method="POST" 
                                  class="inline"
                                  onsubmit="return confirm('Hapus kupon ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $coupons->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <p>Tidak ada kupon ditemukan.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

