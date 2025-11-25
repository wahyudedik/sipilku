<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Riwayat Perhitungan
            </h2>
            <a href="{{ route('tools.index') }}">
                <x-button variant="secondary" size="sm">Kembali ke Tools</x-button>
            </a>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('tools.history') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[150px]">
                <select name="type" class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                    <option value="rab" {{ request('type') === 'rab' ? 'selected' : '' }}>RAB Calculator</option>
                    <option value="volume_material" {{ request('type') === 'volume_material' ? 'selected' : '' }}>Volume Material</option>
                    <option value="struktur" {{ request('type') === 'struktur' ? 'selected' : '' }}>Struktur Calculator</option>
                    <option value="pondasi" {{ request('type') === 'pondasi' ? 'selected' : '' }}>Pondasi Calculator</option>
                    <option value="estimasi_waktu" {{ request('type') === 'estimasi_waktu' ? 'selected' : '' }}>Estimasi Waktu</option>
                    <option value="overhead_profit" {{ request('type') === 'overhead_profit' ? 'selected' : '' }}>Overhead & Profit</option>
                </select>
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('tools.history') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    @if($calculations->count() > 0)
        <div class="space-y-4">
            @foreach($calculations as $calculation)
                <x-card>
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $calculation->title ?? $calculation->getTypeLabel() }}
                                </h3>
                                <x-badge variant="default" size="sm">
                                    {{ $calculation->getTypeLabel() }}
                                </x-badge>
                            </div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                {{ $calculation->created_at->format('d M Y H:i') }}
                            </p>
                            @if($calculation->notes)
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Illuminate\Support\Str::limit($calculation->notes, 100) }}
                                </p>
                            @endif
                        </div>
                        <div class="ml-4 flex space-x-2">
                            <a href="{{ route('tools.show', $calculation) }}">
                                <x-button variant="primary" size="sm">Lihat Detail</x-button>
                            </a>
                            <form action="{{ route('tools.destroy', $calculation) }}" method="POST" onsubmit="return confirm('Hapus perhitungan ini?')">
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
            {{ $calculations->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p>Tidak ada perhitungan yang disimpan</p>
                <a href="{{ route('tools.index') }}" class="mt-4 inline-block">
                    <x-button variant="primary">Gunakan Tools</x-button>
                </a>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

