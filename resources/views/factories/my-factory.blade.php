@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Pabrik Saya
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('factories.edit', $factory) }}">
                    <x-button variant="secondary" size="sm">Edit Pabrik</x-button>
                </a>
                <a href="{{ route('factories.show', $factory) }}">
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

            <!-- Factory Status -->
            <x-card class="mb-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        @if($factory->logo)
                            <img src="{{ Storage::url($factory->logo) }}" alt="{{ $factory->name }}" class="w-20 h-20 object-cover rounded-lg">
                        @endif
                        <div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $factory->name }}</h3>
                            <div class="flex items-center space-x-2 mt-2">
                                <x-badge :variant="match($factory->status) {
                                    'approved' => 'success',
                                    'pending' => 'warning',
                                    'rejected' => 'danger',
                                    'suspended' => 'danger',
                                    default => 'default'
                                }">
                                    {{ ucfirst($factory->status) }}
                                </x-badge>
                                @if($factory->is_verified)
                                    <x-badge variant="success">Verified</x-badge>
                                @endif
                                @if($factory->is_active)
                                    <x-badge variant="info">Active</x-badge>
                                @else
                                    <x-badge variant="default">Inactive</x-badge>
                                @endif
                                @if($factory->factoryType)
                                    <x-badge variant="default">{{ $factory->factoryType->name }}</x-badge>
                                @endif
                                <x-badge variant="info">{{ ucfirst($factory->category) }}</x-badge>
                            </div>
                        </div>
                    </div>
                </div>

                @if($factory->status === 'pending')
                    <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Status:</strong> Pabrik Anda sedang menunggu persetujuan admin. Anda akan mendapat notifikasi setelah pabrik disetujui atau ditolak.
                        </p>
                    </div>
                @elseif($factory->status === 'rejected')
                    <div class="mt-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                        <p class="text-sm text-red-800 dark:text-red-200">
                            <strong>Alasan Penolakan:</strong> {{ $factory->rejection_reason }}
                        </p>
                    </div>
                @endif
            </x-card>

            <!-- Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Produk</p>
                        <p class="text-2xl font-bold text-primary-600 dark:text-primary-400">{{ $factory->products->count() }}</p>
                    </div>
                </x-card>

                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Total Orders</p>
                        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $factory->total_orders }}</p>
                    </div>
                </x-card>

                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Rating</p>
                        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $factory->rating }}/5</p>
                    </div>
                </x-card>

                <x-card>
                    <div class="text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Factory Requests</p>
                        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $factory->factoryRequests->count() }}</p>
                    </div>
                </x-card>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Factory Information -->
                <x-card>
                    <x-slot name="header">
                        <h3 class="text-lg font-medium">Informasi Pabrik</h3>
                    </x-slot>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Nama</p>
                            <p class="font-semibold">{{ $factory->name }}</p>
                        </div>
                        @if($factory->factoryType)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Tipe Pabrik</p>
                                <p class="font-semibold">{{ $factory->factoryType->name }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Kategori</p>
                            <p class="font-semibold">{{ ucfirst($factory->category) }}</p>
                        </div>
                        @if($factory->description)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Deskripsi</p>
                                <p class="text-gray-900 dark:text-gray-100">{{ $factory->description }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Phone</p>
                            <p class="font-semibold">{{ $factory->phone ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Email</p>
                            <p class="font-semibold">{{ $factory->email ?? '-' }}</p>
                        </div>
                        @if($factory->business_license)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Izin Operasional</p>
                                <p class="font-semibold">{{ $factory->business_license }}</p>
                            </div>
                        @endif
                        @if($factory->delivery_price_per_km)
                            <div>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Harga Delivery</p>
                                <p class="font-semibold">Rp {{ number_format($factory->delivery_price_per_km, 0, ',', '.') }}/km</p>
                            </div>
                        @endif
                    </div>
                </x-card>

                <!-- Locations -->
                <x-card>
                    <x-slot name="header">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-medium">Lokasi Pabrik</h3>
                            <a href="{{ route('factories.locations.index', $factory) }}">
                                <x-button variant="primary" size="sm">Kelola Lokasi</x-button>
                            </a>
                        </div>
                    </x-slot>
                    <div class="space-y-3">
                        @forelse($factory->locations as $location)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        @if($location->name)
                                            <p class="font-semibold">{{ $location->name }}</p>
                                        @endif
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $location->full_address }}</p>
                                        @if($location->is_primary)
                                            <x-badge variant="success" size="xs" class="mt-1">Primary</x-badge>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-500 dark:text-gray-400">Belum ada lokasi</p>
                            <a href="{{ route('factories.locations.create', $factory) }}">
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
                        <a href="{{ route('factories.products.index', $factory) }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
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
                        <a href="{{ route('contractor.factory-requests.index') }}" class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                            <div class="flex items-center space-x-3">
                                <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <div>
                                    <p class="font-semibold">Factory Requests</p>
                                    <p class="text-sm text-gray-500">Lihat permintaan dari kontraktor</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </x-card>
            </div>
        </div>
    </div>
</x-app-layout>

