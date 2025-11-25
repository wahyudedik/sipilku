@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-with-sidebar>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">
                Detail Kategori
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.categories.edit', $category) }}">
                    <x-button variant="primary" size="sm">Edit</x-button>
                </a>
                <a href="{{ route('admin.categories.index') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Category Info -->
                <div class="lg:col-span-2 space-y-6">
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Informasi Kategori</h3>
                        </x-slot>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama</label>
                                <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $category->name }}</p>
                            </div>
                            @if($category->description)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Deskripsi</label>
                                    <p class="text-gray-700 dark:text-gray-300">{{ $category->description }}</p>
                                </div>
                            @endif
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tipe</label>
                                    <x-badge variant="default" size="md">
                                        {{ ucfirst($category->type) }}
                                    </x-badge>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                                    <x-badge :variant="$category->is_active ? 'success' : 'default'" size="md">
                                        {{ $category->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </x-badge>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Urutan</label>
                                <p class="text-gray-700 dark:text-gray-300">{{ $category->sort_order ?? 0 }}</p>
                            </div>
                        </div>
                    </x-card>

                    <!-- Products -->
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Produk ({{ $category->products()->count() }})</h3>
                        </x-slot>
                        @if($category->products()->count() > 0)
                            <div class="space-y-3">
                                @foreach($category->products()->latest()->limit(5)->get() as $product)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $product->title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Oleh: {{ $product->user->name }}
                                            </p>
                                        </div>
                                        <x-badge :variant="match($product->status) {
                                            'approved' => 'success',
                                            'pending' => 'warning',
                                            default => 'default'
                                        }" size="sm">
                                            {{ ucfirst($product->status) }}
                                        </x-badge>
                                    </div>
                                @endforeach
                                @if($category->products()->count() > 5)
                                    <a href="{{ route('admin.products.index', ['category' => $category->id]) }}" class="text-primary-600 hover:underline text-sm">
                                        Lihat semua {{ $category->products()->count() }} produk →
                                    </a>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada produk</p>
                        @endif
                    </x-card>

                    <!-- Services -->
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Jasa ({{ $category->services()->count() }})</h3>
                        </x-slot>
                        @if($category->services()->count() > 0)
                            <div class="space-y-3">
                                @foreach($category->services()->latest()->limit(5)->get() as $service)
                                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $service->title }}</h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                                Oleh: {{ $service->user->name }}
                                            </p>
                                        </div>
                                        <x-badge :variant="match($service->status) {
                                            'approved' => 'success',
                                            'pending' => 'warning',
                                            default => 'default'
                                        }" size="sm">
                                            {{ ucfirst($service->status) }}
                                        </x-badge>
                                    </div>
                                @endforeach
                                @if($category->services()->count() > 5)
                                    <a href="{{ route('admin.services.index', ['category' => $category->id]) }}" class="text-primary-600 hover:underline text-sm">
                                        Lihat semua {{ $category->services()->count() }} jasa →
                                    </a>
                                @endif
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400 text-center py-4">Tidak ada jasa</p>
                        @endif
                    </x-card>
                </div>

                <!-- Sidebar -->
                <div>
                    <x-card>
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Gambar</h3>
                        </x-slot>
                        @if($category->image)
                            <img src="{{ Storage::url($category->image) }}" 
                                 alt="{{ $category->name }}"
                                 class="w-full rounded-lg">
                        @else
                            <div class="w-full h-48 bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                            </div>
                        @endif
                    </x-card>

                    <x-card class="mt-6">
                        <x-slot name="header">
                            <h3 class="text-lg font-medium">Statistik</h3>
                        </x-slot>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Produk</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $category->products()->count() }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Total Jasa</span>
                                <span class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $category->services()->count() }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Dibuat</span>
                                <span class="text-gray-900 dark:text-gray-100">
                                    {{ $category->created_at->format('d M Y') }}
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Diperbarui</span>
                                <span class="text-gray-900 dark:text-gray-100">
                                    {{ $category->updated_at->format('d M Y') }}
                                </span>
                            </div>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>
</x-app-with-sidebar>

