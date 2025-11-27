@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Kategori Produk Toko
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('stores.categories.create') }}">
                    <x-button variant="primary" size="sm">Tambah Kategori</x-button>
                </a>
                <a href="{{ route('stores.my-store') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif

            @if(session('error'))
                <x-alert type="error" class="mb-6">{{ session('error') }}</x-alert>
            @endif

            <!-- Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('stores.categories.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-text-input 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Cari kategori..." 
                                class="w-full" />
                        </div>
                        <div>
                            <select name="status" class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900">
                                <option value="">Semua Status</option>
                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="flex space-x-2">
                            <x-button variant="primary" size="md" type="submit">Filter</x-button>
                            <a href="{{ route('stores.categories.index') }}">
                                <x-button variant="secondary" size="md" type="button">Reset</x-button>
                            </a>
                        </div>
                    </div>
                </form>
            </x-card>

            <!-- Categories List -->
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <x-card>
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex-1">
                                    @if($category->image)
                                        <img src="{{ Storage::url($category->image) }}" alt="{{ $category->name }}" class="w-full h-32 object-cover rounded-lg mb-3">
                                    @else
                                        <div class="w-full h-32 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center mb-3">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                            </svg>
                                        </div>
                                    @endif
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $category->name }}
                                    </h3>
                                    @if($category->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">
                                            {{ $category->description }}
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    @if($category->is_active)
                                        <x-badge variant="success" size="sm">Aktif</x-badge>
                                    @else
                                        <x-badge variant="default" size="sm">Nonaktif</x-badge>
                                    @endif
                                    <span class="text-xs text-gray-500">
                                        {{ $category->products()->count() }} produk
                                    </span>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('stores.categories.edit', $category) }}">
                                        <x-button variant="secondary" size="sm">Edit</x-button>
                                    </a>
                                    <form action="{{ route('stores.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-button variant="danger" size="sm" type="submit">Hapus</x-button>
                                    </form>
                                </div>
                            </div>
                        </x-card>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $categories->links() }}
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">Belum ada kategori.</p>
                        <a href="{{ route('stores.categories.create') }}">
                            <x-button variant="primary">Tambah Kategori Pertama</x-button>
                        </a>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-layout>

