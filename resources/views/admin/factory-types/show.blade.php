@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Tipe Pabrik
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.factory-types.edit', $factoryType) }}">
                    <x-button variant="secondary" size="sm">Edit</x-button>
                </a>
                <a href="{{ route('admin.factory-types.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <x-card>
                <div class="flex items-start space-x-4 mb-4">
                    @if($factoryType->icon)
                        <img src="{{ Storage::url($factoryType->icon) }}" alt="{{ $factoryType->name }}" class="w-20 h-20 object-cover rounded">
                    @elseif($factoryType->image)
                        <img src="{{ Storage::url($factoryType->image) }}" alt="{{ $factoryType->name }}" class="w-20 h-20 object-cover rounded">
                    @endif
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $factoryType->name }}</h1>
                        <div class="mt-2">
                            <x-badge :variant="$factoryType->is_active ? 'success' : 'default'">
                                {{ $factoryType->is_active ? 'Aktif' : 'Nonaktif' }}
                            </x-badge>
                        </div>
                    </div>
                </div>

                @if($factoryType->description)
                    <div class="prose dark:prose-invert max-w-none">
                        {!! nl2br(e($factoryType->description)) !!}
                    </div>
                @endif

                @if($factoryType->default_units && count($factoryType->default_units) > 0)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Default Units:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($factoryType->default_units as $unit)
                                <x-badge variant="info">{{ $unit }}</x-badge>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($factoryType->specifications_template)
                    <div class="mt-4">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Specifications Template:</p>
                        <pre class="bg-gray-100 dark:bg-gray-800 p-4 rounded text-sm overflow-x-auto">{{ json_encode($factoryType->specifications_template, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                @endif
            </x-card>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <x-card>
                <x-slot name="header">
                    <h3 class="text-lg font-medium">Statistik</h3>
                </x-slot>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Total Pabrik</span>
                        <span class="font-semibold">{{ $factoryType->factories()->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Sort Order</span>
                        <span class="font-semibold">{{ $factoryType->sort_order }}</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-with-sidebar>

