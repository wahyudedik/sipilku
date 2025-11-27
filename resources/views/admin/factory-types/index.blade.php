@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Factory Type Management
            </h2>
            <a href="{{ route('admin.factory-types.create') }}">
                <x-button variant="primary" size="sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Tipe Pabrik
                </x-button>
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
    @endif
    @if(session('error'))
        <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
    @endif

    <!-- Filters -->
    <x-card class="mb-6">
        <form method="GET" action="{{ route('admin.factory-types.index') }}" class="flex flex-wrap gap-4">
            <div class="flex-1 min-w-[200px]">
                <x-text-input 
                    name="search" 
                    value="{{ request('search') }}" 
                    placeholder="Cari tipe pabrik..." 
                    class="w-full" />
            </div>
            <div>
                <x-select-input 
                    name="status" 
                    :options="[
                        '' => 'Semua Status',
                        'active' => 'Aktif',
                        'inactive' => 'Nonaktif'
                    ]" 
                    value="{{ request('status', '') }}" />
            </div>
            <div class="flex gap-2">
                <x-button variant="primary" size="md" type="submit">Filter</x-button>
                <a href="{{ route('admin.factory-types.index') }}">
                    <x-button variant="secondary" size="md" type="button">Reset</x-button>
                </a>
            </div>
        </form>
    </x-card>

    <!-- Factory Types Grid -->
    @if($factoryTypes->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($factoryTypes as $factoryType)
                <x-card>
                    <div class="flex items-start space-x-4">
                        @if($factoryType->icon)
                            <img src="{{ Storage::url($factoryType->icon) }}" 
                                 alt="{{ $factoryType->name }}"
                                 class="w-16 h-16 object-cover rounded-lg">
                        @elseif($factoryType->image)
                            <img src="{{ Storage::url($factoryType->image) }}" 
                                 alt="{{ $factoryType->name }}"
                                 class="w-16 h-16 object-cover rounded-lg">
                        @else
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $factoryType->name }}
                                    </h3>
                                    <div class="mt-1 flex items-center space-x-2">
                                        <x-badge :variant="$factoryType->is_active ? 'success' : 'default'" size="sm">
                                            {{ $factoryType->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </x-badge>
                                    </div>
                                    @if($factoryType->description)
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ $factoryType->description }}
                                        </p>
                                    @endif
                                    @if($factoryType->default_units)
                                        <div class="mt-2 flex flex-wrap gap-1">
                                            @foreach($factoryType->default_units as $unit)
                                                <x-badge variant="info" size="xs">{{ $unit }}</x-badge>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between">
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $factoryType->factories()->count() }} pabrik</span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('admin.factory-types.edit', $factoryType) }}" 
                                       class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.factory-types.destroy', $factoryType) }}" 
                                          method="POST" 
                                          class="inline"
                                          onsubmit="return confirm('Hapus tipe pabrik ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 dark:text-red-400">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $factoryTypes->links() }}
        </div>
    @else
        <x-card>
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada tipe pabrik</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat tipe pabrik baru.</p>
                <a href="{{ route('admin.factory-types.create') }}" class="mt-4 inline-block">
                    <x-button variant="primary">Tambah Tipe Pabrik</x-button>
                </a>
            </div>
        </x-card>
    @endif
</x-app-with-sidebar>

