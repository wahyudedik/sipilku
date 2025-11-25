<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Tools Teknik Sipil
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8 text-center">
                <h1 class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                    Tools Teknik Sipil
                </h1>
                <p class="text-lg text-gray-600 dark:text-gray-400">
                    Kumpulan kalkulator untuk membantu perhitungan proyek teknik sipil
                </p>
            </div>

            <!-- Tools Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <!-- RAB Calculator -->
                <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('tools.rab') }}'">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-5m-6 5h6m-6 0h-2a2 2 0 01-2-2V9a2 2 0 012-2h2m6 0h2a2 2 0 012 2v11a2 2 0 01-2 2h-2m-6-13V5a2 2 0 012-2h2a2 2 0 012 2v2" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">RAB Calculator</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Hitung Rencana Anggaran Biaya proyek dengan detail item pekerjaan
                        </p>
                        <x-button variant="primary" size="md">Gunakan Tool</x-button>
                    </div>
                </x-card>

                <!-- Volume Material Calculator -->
                <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('tools.volume-material') }}'">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Volume Material</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Hitung volume material untuk berbagai bentuk geometri
                        </p>
                        <x-button variant="primary" size="md">Gunakan Tool</x-button>
                    </div>
                </x-card>

                <!-- Struktur Calculator -->
                <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('tools.struktur') }}'">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Struktur Calculator</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Hitung kebutuhan beton dan besi untuk struktur bangunan
                        </p>
                        <x-button variant="primary" size="md">Gunakan Tool</x-button>
                    </div>
                </x-card>

                <!-- Pondasi Calculator -->
                <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('tools.pondasi') }}'">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-yellow-100 dark:bg-yellow-900 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Pondasi Calculator</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Hitung volume dan biaya pondasi (footing, strip, raft)
                        </p>
                        <x-button variant="primary" size="md">Gunakan Tool</x-button>
                    </div>
                </x-card>

                <!-- Estimasi Waktu Proyek -->
                <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('tools.estimasi-waktu') }}'">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Estimasi Waktu Proyek</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Estimasi durasi proyek berdasarkan aktivitas dan tenaga kerja
                        </p>
                        <x-button variant="primary" size="md">Gunakan Tool</x-button>
                    </div>
                </x-card>

                <!-- Overhead & Profit Calculator -->
                <x-card class="hover:shadow-lg transition-shadow cursor-pointer" onclick="window.location.href='{{ route('tools.overhead-profit') }}'">
                    <div class="text-center">
                        <div class="mx-auto w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">Overhead & Profit</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            Hitung overhead dan profit dari biaya langsung proyek
                        </p>
                        <x-button variant="primary" size="md">Gunakan Tool</x-button>
                    </div>
                </x-card>
            </div>

            <!-- Recent Calculations -->
            @auth
                @if($recentCalculations->count() > 0)
                    <x-card>
                        <x-slot name="header">
                            <div class="flex justify-between items-center">
                                <h3 class="text-lg font-medium">Perhitungan Terakhir</h3>
                                <a href="{{ route('tools.history') }}" class="text-sm text-primary-600 hover:underline">
                                    Lihat Semua
                                </a>
                            </div>
                        </x-slot>
                        <div class="space-y-3">
                            @foreach($recentCalculations as $calculation)
                                <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $calculation->title ?? $calculation->getTypeLabel() }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $calculation->created_at->format('d M Y H:i') }}
                                        </p>
                                    </div>
                                    <a href="{{ route('tools.show', $calculation) }}" class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </x-card>
                @endif
            @endauth
        </div>
    </div>
</x-app-layout>

