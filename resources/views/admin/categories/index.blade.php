@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Category Management
            </h2>
            <a href="{{ route('admin.categories.create') }}">
                <x-button variant="primary" size="sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Kategori
                </x-button>
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

            <!-- Filters -->
            <x-card class="mb-6">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Cari kategori..."
                               class="w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                    </div>
                    <div>
                        <select name="type" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="all" {{ request('type', 'all') === 'all' ? 'selected' : '' }}>Semua Tipe</option>
                            <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Produk</option>
                            <option value="service" {{ request('type') === 'service' ? 'selected' : '' }}>Jasa</option>
                            <option value="both" {{ request('type') === 'both' ? 'selected' : '' }}>Keduanya</option>
                        </select>
                    </div>
                    <div>
                        <select name="status" class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 focus:border-primary-500 focus:ring-primary-500">
                            <option value="" {{ request('status') === '' ? 'selected' : '' }}>Semua Status</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <x-button variant="primary" size="md" type="submit">Filter</x-button>
                        <a href="{{ route('admin.categories.index') }}">
                            <x-button variant="secondary" size="md" type="button">Reset</x-button>
                        </a>
                    </div>
                </form>
            </x-card>

            <!-- Categories Grid -->
            @if($categories->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($categories as $category)
                        <x-card>
                            <div class="flex items-start space-x-4">
                                @if($category->image)
                                    <img src="{{ Storage::url($category->image) }}" 
                                         alt="{{ $category->name }}"
                                         class="w-20 h-20 object-cover rounded-lg">
                                @else
                                    <div class="w-20 h-20 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                        <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start justify-between">
                                        <div class="flex-1">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $category->name }}
                                            </h3>
                                            <div class="mt-1 flex items-center space-x-2">
                                                <x-badge :variant="$category->is_active ? 'success' : 'default'" size="sm">
                                                    {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                                </x-badge>
                                                <x-badge variant="default" size="sm">
                                                    {{ ucfirst($category->type) }}
                                                </x-badge>
                                            </div>
                                            @if($category->description)
                                                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                    {{ $category->description }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-4 flex items-center justify-between">
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <span>{{ $category->products()->count() }} produk</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $category->services()->count() }} jasa</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                                Edit
                                            </a>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Hapus kategori ini?')">
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
                    {{ $categories->links() }}
                </div>
            @else
                <x-card>
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">Tidak ada kategori</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Mulai dengan membuat kategori baru.</p>
                        <a href="{{ route('admin.categories.create') }}" class="mt-4 inline-block">
                            <x-button variant="primary">Tambah Kategori</x-button>
                        </a>
                    </div>
                </x-card>
            @endif
        </div>
    </div>
</x-app-with-sidebar>

