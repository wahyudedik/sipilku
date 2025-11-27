<aside x-data="{ open: false }" 
      @toggle-sidebar.window="open = !open"
      :class="{'translate-x-0': open, '-translate-x-full': !open}"
      class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transform transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:z-auto">
    <div class="flex flex-col w-64">
        <div class="flex flex-col flex-grow bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 pt-5 pb-4 overflow-y-auto">
            <div class="flex items-center flex-shrink-0 px-4">
                <a href="{{ url('/') }}" class="flex items-center">
                    <x-application-logo class="h-8 w-auto" />
                    <span class="ml-2 text-xl font-bold text-gray-900 dark:text-gray-100">Sipilku</span>
                </a>
            </div>
            <div class="mt-5 flex-grow flex flex-col">
                <nav class="flex-1 px-2 space-y-1">
                    <!-- Dashboard -->
                    <x-sidebar-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        <x-icon name="home" class="w-5 h-5" />
                        <span>Dashboard</span>
                    </x-sidebar-link>

                    <!-- Tools -->
                    <x-sidebar-link :href="route('tools.index')" :active="request()->routeIs('tools.*')">
                        <x-icon name="tool" class="w-5 h-5" />
                        <span>Tools</span>
                    </x-sidebar-link>

                    @auth
                        @if(auth()->user()->isBuyer())
                            <!-- Buyer Menu -->
                            <x-sidebar-link href="#" :active="request()->routeIs('products.*')">
                                <x-icon name="package" class="w-5 h-5" />
                                <span>Produk</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('services.index')" :active="request()->routeIs('services.*')">
                                <x-icon name="briefcase" class="w-5 h-5" />
                                <span>Jasa</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('buyer.quote-requests.index')" :active="request()->routeIs('buyer.quote-requests.*')">
                                <x-icon name="message-circle" class="w-5 h-5" />
                                <span>Quote Saya</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('messages.index')" :active="request()->routeIs('messages.*')">
                                <x-icon name="message" class="w-5 h-5" />
                                <span>Pesan</span>
                            </x-sidebar-link>
                            <x-sidebar-link href="#" :active="request()->routeIs('orders.*')">
                                <x-icon name="shopping-bag" class="w-5 h-5" />
                                <span>Pesanan Saya</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('balance.index')" :active="request()->routeIs('balance.*')">
                                <x-icon name="wallet" class="w-5 h-5" />
                                <span>Saldo Saya</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('payments.history')" :active="request()->routeIs('payments.*')">
                                <x-icon name="file-invoice" class="w-5 h-5" />
                                <span>Riwayat Pembayaran</span>
                            </x-sidebar-link>
                        @endif

                        @if(auth()->user()->isSeller())
                            <!-- Seller Menu -->
                            <x-sidebar-link :href="route('seller.dashboard')" :active="request()->routeIs('seller.dashboard')">
                                <x-icon name="home" class="w-5 h-5" />
                                <span>Dashboard</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('seller.products.index')" :active="request()->routeIs('seller.products.*')">
                                <x-icon name="package" class="w-5 h-5" />
                                <span>Produk Saya</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('seller.services.index')" :active="request()->routeIs('seller.services.*')">
                                <x-icon name="briefcase" class="w-5 h-5" />
                                <span>Jasa Saya</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('seller.quote-requests.index')" :active="request()->routeIs('seller.quote-requests.*')">
                                <x-icon name="message-circle" class="w-5 h-5" />
                                <span>Quote Requests</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('seller.commissions.index')" :active="request()->routeIs('seller.commissions.*')">
                                <x-icon name="currency-dollar" class="w-5 h-5" />
                                <span>Komisi & Penghasilan</span>
                            </x-sidebar-link>
                            <x-sidebar-link href="#" :active="request()->routeIs('seller.orders.*')">
                                <x-icon name="shopping-bag" class="w-5 h-5" />
                                <span>Pesanan</span>
                            </x-sidebar-link>
                            <x-sidebar-link href="#" :active="request()->routeIs('seller.withdrawals.*')">
                                <x-icon name="credit-card" class="w-5 h-5" />
                                <span>Penarikan</span>
                            </x-sidebar-link>
                        @endif

                        @if(auth()->user()->isAdmin())
                            <!-- Admin Menu -->
                            <x-sidebar-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                <x-icon name="home" class="w-5 h-5" />
                                <span>Dashboard</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')">
                                <x-icon name="users" class="w-5 h-5" />
                                <span>User Management</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.products.index')" :active="request()->routeIs('admin.products.*')">
                                <x-icon name="package" class="w-5 h-5" />
                                <span>Produk</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.services.index')" :active="request()->routeIs('admin.services.*')">
                                <x-icon name="briefcase" class="w-5 h-5" />
                                <span>Jasa</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.orders.index')" :active="request()->routeIs('admin.orders.*')">
                                <x-icon name="shopping-bag" class="w-5 h-5" />
                                <span>Pesanan</span>
                            </x-sidebar-link>
                            <x-sidebar-link href="#" :active="request()->routeIs('admin.transactions.*')">
                                <x-icon name="clipboard" class="w-5 h-5" />
                                <span>Transaksi</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')">
                                <x-icon name="tag" class="w-5 h-5" />
                                <span>Kategori</span>
                            </x-sidebar-link>
                            <x-sidebar-link :href="route('admin.landing-page.index')" :active="request()->routeIs('admin.landing-page.*')">
                                <x-icon name="layout-dashboard" class="w-5 h-5" />
                                <span>Landing Page</span>
                            </x-sidebar-link>
                        @endif

                        <!-- Common Menu -->
                        <x-sidebar-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
                            <x-icon name="user" class="w-5 h-5" />
                            <span>Profile</span>
                        </x-sidebar-link>
                    @endauth
                </nav>
            </div>
        </div>
    </div>
</aside>

