<x-app-with-sidebar>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <x-alert type="success">
                    {{ session('success') }}
                </x-alert>
            @endif
            @if(session('error'))
                <x-alert type="error">
                    {{ session('error') }}
                </x-alert>
            @endif

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Users -->
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Users</p>
                        <h3 class="text-3xl font-bold text-primary-600 dark:text-primary-400">
                            {{ $totalUsers }}
                        </h3>
                        <div class="flex justify-center space-x-4 mt-2 text-xs">
                            <span class="text-blue-600 dark:text-blue-400">{{ $totalBuyers }} Buyer</span>
                            <span class="text-green-600 dark:text-green-400">{{ $totalSellers }} Seller</span>
                        </div>
                        <a href="{{ route('admin.users.index') }}" class="mt-4 inline-block">
                            <x-button variant="primary" size="sm">Kelola Users</x-button>
                        </a>
                    </div>
                </x-card>

                <!-- Products -->
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Produk</p>
                        <h3 class="text-3xl font-bold text-blue-600 dark:text-blue-400">
                            {{ $totalProducts }}
                        </h3>
                        <div class="flex justify-center space-x-4 mt-2 text-xs">
                            <span class="text-yellow-600 dark:text-yellow-400">{{ $pendingProducts }} Pending</span>
                            <span class="text-green-600 dark:text-green-400">{{ $approvedProducts }} Approved</span>
                        </div>
                        <a href="{{ route('admin.products.index') }}" class="mt-4 inline-block">
                            <x-button variant="primary" size="sm">Kelola Produk</x-button>
                        </a>
                    </div>
                </x-card>

                <!-- Orders -->
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Pesanan</p>
                        <h3 class="text-3xl font-bold text-green-600 dark:text-green-400">
                            {{ $totalOrders }}
                        </h3>
                        <div class="flex justify-center space-x-4 mt-2 text-xs">
                            <span class="text-yellow-600 dark:text-yellow-400">{{ $pendingOrders }} Pending</span>
                            <span class="text-green-600 dark:text-green-400">{{ $completedOrders }} Completed</span>
                        </div>
                        <a href="{{ route('admin.orders.index') }}" class="mt-4 inline-block">
                            <x-button variant="primary" size="sm">Kelola Pesanan</x-button>
                        </a>
                    </div>
                </x-card>

                <!-- Revenue -->
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Revenue</p>
                        <h3 class="text-3xl font-bold text-purple-600 dark:text-purple-400">
                            Rp {{ number_format(abs($totalRevenue), 0, ',', '.') }}
                        </h3>
                        <div class="flex justify-center space-x-4 mt-2 text-xs">
                            <span>Komisi: Rp {{ number_format($totalCommissions, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </x-card>
            </div>

            <!-- Pending Actions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Seller Pending</h3>
                    </x-slot>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $pendingSellers }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Menunggu Persetujuan</p>
                        <a href="{{ route('admin.users.index', ['seller_status' => 'pending']) }}" class="mt-4 inline-block">
                            <x-button variant="warning" size="sm">Review Seller</x-button>
                        </a>
                    </div>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Produk Pending</h3>
                    </x-slot>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $pendingProducts }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Menunggu Persetujuan</p>
                        <a href="{{ route('admin.products.index', ['status' => 'pending']) }}" class="mt-4 inline-block">
                            <x-button variant="warning" size="sm">Review Produk</x-button>
                        </a>
                    </div>
                </x-card>

                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Withdrawal Pending</h3>
                    </x-slot>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-yellow-600 dark:text-yellow-400">
                            {{ $pendingWithdrawals }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">Menunggu Persetujuan</p>
                        <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="mt-4 inline-block">
                            <x-button variant="warning" size="sm">Review Withdrawal</x-button>
                        </a>
                    </div>
                </x-card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Recent Users -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">User Terbaru</h3>
                            <a href="{{ route('admin.users.index') }}" class="text-sm text-primary-600 hover:underline">
                                Lihat Semua
                            </a>
                        </div>
                    </x-slot>
                    <div class="space-y-3">
                        @foreach($recentUsers as $user)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $user->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $user->email }}
                                    </p>
                                </div>
                                <x-badge :variant="$user->is_active ? 'success' : 'default'" size="sm">
                                    {{ $user->is_active ? 'Aktif' : 'Nonaktif' }}
                                </x-badge>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <!-- Recent Orders -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">Pesanan Terbaru</h3>
                            <a href="{{ route('admin.orders.index') }}" class="text-sm text-primary-600 hover:underline">
                                Lihat Semua
                            </a>
                        </div>
                    </x-slot>
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        Order #{{ $order->uuid }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $order->user->name }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <x-badge :variant="match($order->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        default => 'default'
                                    }" size="sm">
                                        {{ ucfirst($order->status) }}
                                    </x-badge>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1">
                                        Rp {{ number_format($order->total, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>

                <!-- Recent Withdrawals -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">Withdrawal Terbaru</h3>
                            <a href="{{ route('admin.withdrawals.index') }}" class="text-sm text-primary-600 hover:underline">
                                Lihat Semua
                            </a>
                        </div>
                    </x-slot>
                    <div class="space-y-3">
                        @foreach($recentWithdrawals as $withdrawal)
                            <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex-1">
                                    <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $withdrawal->user->name }}
                                    </h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $withdrawal->created_at->format('d M Y') }}
                                    </p>
                                </div>
                                <div class="text-right">
                                    <x-badge :variant="match($withdrawal->status) {
                                        'completed' => 'success',
                                        'pending' => 'warning',
                                        default => 'default'
                                    }" size="sm">
                                        {{ ucfirst($withdrawal->status) }}
                                    </x-badge>
                                    <p class="text-sm font-semibold text-gray-900 dark:text-gray-100 mt-1">
                                        Rp {{ number_format($withdrawal->amount, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-with-sidebar>

