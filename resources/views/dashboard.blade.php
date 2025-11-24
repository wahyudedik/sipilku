<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Dashboard
        </h2>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @if(auth()->user()->isSeller())
            <!-- Seller Dashboard -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Produk Saya</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        {{ auth()->user()->products()->count() }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Total Produk</p>
                    <a href="{{ route('seller.products.index') }}" class="mt-4 inline-block">
                        <x-button variant="primary" size="sm">Kelola Produk</x-button>
                    </a>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Produk Pending</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ auth()->user()->products()->where('status', 'pending')->count() }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Menunggu Persetujuan</p>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Produk Disetujui</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-green-600 dark:text-green-400">
                        {{ auth()->user()->products()->where('status', 'approved')->count() }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Produk Aktif</p>
                </div>
            </x-card>
        @endif

        @if(auth()->user()->isAdmin())
            <!-- Admin Dashboard -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Produk Pending</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                        {{ \App\Models\Product::where('status', 'pending')->count() }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Menunggu Persetujuan</p>
                    <a href="{{ route('admin.products.index', ['status' => 'pending']) }}" class="mt-4 inline-block">
                        <x-button variant="warning" size="sm">Review Produk</x-button>
                    </a>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Total Produk</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        {{ \App\Models\Product::count() }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Semua Produk</p>
                    <a href="{{ route('admin.products.index') }}" class="mt-4 inline-block">
                        <x-button variant="primary" size="sm">Kelola Produk</x-button>
                    </a>
                </div>
            </x-card>

            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Total Seller</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                        {{ \App\Models\User::whereHas('roles', function($q) { $q->where('name', 'seller'); })->count() }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Seller Terdaftar</p>
                </div>
            </x-card>
        @endif

        @if(auth()->user()->isBuyer())
            <!-- Buyer Dashboard -->
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Pesanan Saya</h3>
                </x-slot>
                <div class="text-center">
                    <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                        {{ auth()->user()->orders()->count() }}
                    </p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Total Pesanan</p>
                </div>
            </x-card>
        @endif
    </div>
</x-app-with-sidebar>
