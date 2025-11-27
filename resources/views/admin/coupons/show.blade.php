<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Kupon
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.coupons.edit', $coupon) }}">
                    <x-button variant="secondary" size="sm">Edit</x-button>
                </a>
                <a href="{{ route('admin.coupons.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2">
            <x-card>
                <x-slot name="header">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium">{{ $coupon->name }}</h3>
                        <x-badge :variant="$coupon->isValid() ? 'success' : 'default'">
                            {{ $coupon->isValid() ? 'Active' : 'Inactive' }}
                        </x-badge>
                    </div>
                </x-slot>

                <div class="space-y-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Kode Kupon</p>
                        <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $coupon->code }}</p>
                    </div>

                    @if($coupon->description)
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Deskripsi</p>
                            <p class="text-gray-900 dark:text-gray-100">{{ $coupon->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Tipe</p>
                            <p class="font-semibold">{{ ucfirst($coupon->type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Nilai</p>
                            <p class="font-semibold text-lg">
                                @if($coupon->type === 'percentage')
                                    {{ $coupon->value }}%
                                @else
                                    Rp {{ number_format($coupon->value, 0, ',', '.') }}
                                @endif
                            </p>
                        </div>
                        @if($coupon->minimum_purchase)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Minimum Purchase</p>
                                <p class="font-semibold">Rp {{ number_format($coupon->minimum_purchase, 0, ',', '.') }}</p>
                            </div>
                        @endif
                        @if($coupon->maximum_discount)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Maximum Discount</p>
                                <p class="font-semibold">Rp {{ number_format($coupon->maximum_discount, 0, ',', '.') }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Usage Count</p>
                            <p class="font-semibold">{{ $coupon->usage_count }}</p>
                        </div>
                        @if($coupon->usage_limit)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Usage Limit</p>
                                <p class="font-semibold">{{ $coupon->usage_limit }}</p>
                            </div>
                        @endif
                        @if($coupon->usage_limit_per_user)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Usage Limit Per User</p>
                                <p class="font-semibold">{{ $coupon->usage_limit_per_user }}</p>
                            </div>
                        @endif
                        @if($coupon->starts_at)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Start Date</p>
                                <p class="font-semibold">{{ $coupon->starts_at->format('d M Y H:i') }}</p>
                            </div>
                        @endif
                        @if($coupon->expires_at)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Expiry Date</p>
                                <p class="font-semibold {{ $coupon->expires_at->isPast() ? 'text-red-600' : '' }}">
                                    {{ $coupon->expires_at->format('d M Y H:i') }}
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

