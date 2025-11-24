<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Bandingkan Quotes
            </h2>
            <a href="{{ route('buyer.quote-requests.index') }}">
                <x-button variant="secondary" size="sm">Kembali</x-button>
            </a>
        </div>
    </x-slot>

    @if($quotes->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Jasa / Seller
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Harga Quote
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Pesan Quote
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Tanggal Quote
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            Aksi
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($quotes as $quote)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    @if($quote->service->preview_image)
                                        <img src="{{ Storage::url($quote->service->preview_image) }}" 
                                             alt="{{ $quote->service->title }}"
                                             class="w-12 h-12 object-cover rounded">
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $quote->service->title }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $quote->service->user->name }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <p class="text-lg font-bold text-primary-600 dark:text-primary-400">
                                    Rp {{ number_format($quote->quoted_price, 0, ',', '.') }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-700 dark:text-gray-300 line-clamp-3">
                                    {{ $quote->quote_message ?? '-' }}
                                </p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                {{ $quote->quoted_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <a href="{{ route('buyer.quote-requests.show', $quote) }}" 
                                   class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Summary -->
        <x-card class="mt-6">
            <x-slot name="header">
                <h3 class="text-lg font-medium">Ringkasan</h3>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Quotes</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $quotes->count() }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Harga Terendah</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($quotes->min('quoted_price'), 0, ',', '.') }}
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Harga Tertinggi</p>
                    <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">
                        Rp {{ number_format($quotes->max('quoted_price'), 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </x-card>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada quotes untuk dibandingkan</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pilih quotes untuk dibandingkan dari halaman Quote Requests.</p>
                <div class="mt-6">
                    <a href="{{ route('buyer.quote-requests.index') }}">
                        <x-button variant="primary">Kembali ke Quote Requests</x-button>
                    </a>
                </div>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

