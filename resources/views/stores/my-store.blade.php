@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Toko Saya
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('stores.edit', $store) }}">
                    <x-button variant="secondary" size="sm">Edit Toko</x-button>
                </a>
                <a href="{{ route('stores.show', $store) }}">
                    <x-button variant="primary" size="sm">Lihat Profil</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
            @endif
            @if(session('info'))
                <x-alert type="info" class="mb-6">{{ session('info') }}</x-alert>
            @endif

            <!-- Store Status -->
            <x-card class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @if($store->logo)
                            <img src="{{ Storage::url($store->logo) }}" alt="{{ $store->name }}" class="w-20 h-20 object-cover rounded-lg">
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $store->name }}</h3>
                            <div class="flex items-center space-x-2 mt-2">
                                <x-badge :variant="match($store->status) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    'suspended' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($store->status) }}
                                </x-badge>
                                @if($store->is_verified)
                                    <x-badge variant="success">Verified</x-badge>
                                @endif
                                @if($store->is_active)
                                    <x-badge variant="info">Active</x-badge>
                                @else
                                    <x-badge variant="default">Inactive</x-badge>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if($store->status === 'pending')
                    <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Status:</strong> Toko Anda sedang menunggu persetujuan admin. Anda akan mendapat notifikasi setelah toko disetujui atau ditolak.
                        </p>
                    </div>
                @elseif($store->status === 'rejected')
                    <div class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <p class="text-sm text-red-800 dark:text-red-200">
                            <strong>Alasan Penolakan:</strong> {{ $store->rejection_reason }}
                        </p>
                    </div>
                @endif
            </x-card>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Produk</p>
                        <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $store->products->count() }}</p>
                    </div>
                </x-card>

                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Orders</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $store->total_orders }}</p>
                    </div>
                </x-card>

                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Rating</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $store->rating }}/5</p>
                    </div>
                </x-card>

                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Material Requests</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $store->materialRequests->count() }}</p>
                    </div>
                </x-card>
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Produk</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $store->products->count() }}</p>
                    </div>
                </x-card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Store Information -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Informasi Toko</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Nama</p>
                            <p class="font-semibold">{{ $store->name }}</p>
                        </div>
                        @if($store->description)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Deskripsi</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ $store->description }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Phone</p>
                            <p class="font-semibold">{{ $store->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                            <p class="font-semibold">{{ $store->email ?? '-' }}</p>
                        </div>
                        @if($store->business_license)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">SIUP</p>
                                <p class="font-semibold">{{ $store->business_license }}</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Locations -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">Lokasi Toko</h3>
                            <a href="{{ route('stores.locations.index', $store) }}">
                                <x-button variant="primary" size="sm">Kelola Lokasi</x-button>
                            </a>
                        </div>
                    </x-slot>
                    <div class="space-y-3">
                        @forelse($store->locations as $location)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <p class="font-semibold">{{ $location->name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $location->full_address }}</p>
                                        @if($location->is_primary)
                                            <x-badge variant="success" size="xs" class="mt-1">Primary</x-badge>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Belum ada lokasi</p>
                            <a href="{{ route('stores.locations.create', $store) }}">
                                <x-button variant="primary" size="sm">Tambah Lokasi</x-button>
                            </a>
                        @endforelse
                    </div>
                </x-card>

                <!-- Quick Actions -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Aksi Cepat</h3>
                    </x-slot>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <a href="{{ route('stores.products.index', $store) }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                <div>
                                    <p class="font-semibold">Kelola Produk</p>
                                    <p class="text-sm text-gray-500">Tambah, edit, atau hapus produk</p>
                                </div>
                            </div>
                        </a>
                        <a href="{{ route('stores.categories.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <div>
                                    <p class="font-semibold">Kelola Kategori</p>
                                    <p class="text-sm text-gray-500">Atur kategori produk</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>

