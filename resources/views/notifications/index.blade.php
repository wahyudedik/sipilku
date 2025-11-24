<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Notifikasi
                @if($unreadCount > 0)
                    <x-badge variant="danger" size="sm" class="ml-2">{{ $unreadCount }}</x-badge>
                @endif
            </h2>
            <div class="flex items-center space-x-2">
                @if($unreadCount > 0)
                    <form action="{{ route('notifications.mark-all-read') }}" method="POST">
                        @csrf
                        <x-button variant="secondary" size="sm" type="submit">Tandai Semua Dibaca</x-button>
                    </form>
                @endif
                <a href="{{ route('notifications.preferences') }}">
                    <x-button variant="secondary" size="sm">Pengaturan</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('notifications.index') }}" 
               class="px-4 py-2 rounded-lg {{ !request('filter') ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                Semua
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
               class="px-4 py-2 rounded-lg {{ request('filter') === 'unread' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                Belum Dibaca
                @if($unreadCount > 0)
                    <x-badge variant="danger" size="sm" class="ml-1">{{ $unreadCount }}</x-badge>
                @endif
            </a>
            <a href="{{ route('notifications.index', ['filter' => 'read']) }}" 
               class="px-4 py-2 rounded-lg {{ request('filter') === 'read' ? 'bg-primary-100 text-primary-700 dark:bg-primary-900 dark:text-primary-300' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                Sudah Dibaca
            </a>
        </div>
    </x-card>

    @if($notifications->count() > 0)
        <div class="space-y-2">
            @foreach($notifications as $notification)
                <x-card class="{{ $notification->read_at ? 'opacity-75' : 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' }}">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-start space-x-3">
                                @if(!$notification->read_at)
                                    <div class="flex-shrink-0 w-2 h-2 bg-blue-500 rounded-full mt-2"></div>
                                @endif
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        @if($notification->type === 'App\Notifications\NewMessageNotification')
                                            💬 Pesan Baru
                                        @elseif($notification->type === 'App\Notifications\OrderStatusNotification')
                                            📦 Status Pesanan
                                        @elseif($notification->type === 'App\Notifications\ProductApprovedNotification')
                                            ✅ Produk Disetujui
                                        @elseif($notification->type === 'App\Notifications\ServiceApprovedNotification')
                                            ✅ Jasa Disetujui
                                        @elseif($notification->type === 'App\Notifications\PaymentConfirmedNotification')
                                            💳 Pembayaran Dikonfirmasi
                                        @else
                                            🔔 Notifikasi
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        @if($notification->type === 'App\Notifications\NewMessageNotification')
                                            Dari: {{ $notification->data['sender_name'] ?? 'N/A' }}
                                            @if(isset($notification->data['message_preview']))
                                                - {{ Str::limit($notification->data['message_preview'], 100) }}
                                            @endif
                                        @elseif($notification->type === 'App\Notifications\OrderStatusNotification')
                                            Status: {{ ucfirst($notification->data['status'] ?? 'N/A') }}
                                            - {{ $notification->data['orderable_title'] ?? 'N/A' }}
                                        @elseif($notification->type === 'App\Notifications\ProductApprovedNotification')
                                            {{ $notification->data['product_title'] ?? 'N/A' }}
                                            {{ $notification->data['is_approved'] ? 'disetujui' : 'ditolak' }}
                                        @elseif($notification->type === 'App\Notifications\ServiceApprovedNotification')
                                            {{ $notification->data['service_title'] ?? 'N/A' }}
                                            {{ $notification->data['is_approved'] ? 'disetujui' : 'ditolak' }}
                                        @elseif($notification->type === 'App\Notifications\PaymentConfirmedNotification')
                                            Order: {{ $notification->data['order_uuid'] ?? 'N/A' }}
                                            - Rp {{ number_format($notification->data['total'] ?? 0, 0, ',', '.') }}
                                        @endif
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            @if(!$notification->read_at)
                                <form action="{{ route('notifications.mark-read', $notification->id) }}" method="POST">
                                    @csrf
                                    <x-button variant="secondary" size="sm" type="submit">Tandai Dibaca</x-button>
                                </form>
                            @endif
                            <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                  onsubmit="return confirm('Hapus notifikasi ini?')">
                                @csrf
                                @method('DELETE')
                                <x-button variant="danger" size="sm" type="submit">Hapus</x-button>
                            </form>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada notifikasi</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Anda belum memiliki notifikasi.</p>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

