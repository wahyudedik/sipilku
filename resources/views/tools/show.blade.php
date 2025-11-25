<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Perhitungan
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('tools.history') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <x-card>
        <div class="space-y-6">
            <!-- Header -->
            <div class="border-b border-gray-200 dark:border-gray-700 pb-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                            {{ $calculation->title ?? $calculation->getTypeLabel() }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $calculation->created_at->format('d M Y H:i') }}
                        </p>
                    </div>
                    <x-badge variant="default" size="md">
                        {{ $calculation->getTypeLabel() }}
                    </x-badge>
                </div>
            </div>

            <!-- Input Data -->
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Input Data</h4>
                <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                    <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ json_encode($calculation->inputs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            <!-- Results -->
            <div>
                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Hasil Perhitungan</h4>
                <div class="bg-primary-50 dark:bg-primary-900 p-4 rounded-lg">
                    <pre class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ json_encode($calculation->results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            <!-- Notes -->
            @if($calculation->notes)
                <div>
                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-3">Catatan</h4>
                    <div class="bg-gray-50 dark:bg-gray-800 p-4 rounded-lg">
                        <p class="text-gray-700 dark:text-gray-300">{{ $calculation->notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="flex space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                <form action="{{ route('tools.destroy', $calculation) }}" method="POST" onsubmit="return confirm('Hapus perhitungan ini?')">
                    @csrf
                    @method('DELETE')
                    <x-button variant="danger" size="md" type="submit">Hapus</x-button>
                </form>
            </div>
        </div>
    </x-card>
</x-app-with-sidebar>

