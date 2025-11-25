@php
use Illuminate\Support\Facades\Storage;
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Landing Page Preview
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('admin.landing-page.edit') }}">
                    <x-button variant="primary" size="sm">Edit</x-button>
                </a>
                <a href="{{ route('admin.dashboard') }}">
                    <x-button variant="secondary" size="sm">Kembali</x-button>
                </a>
            </div>
        </div>
    </x-slot>

    <!-- Hero Section -->
    <section class="bg-primary-600 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                <div>
                    <h1 class="text-4xl md:text-5xl font-bold mb-4">
                        {{ $settings['hero_title'] ?? 'Selamat Datang di Sipilku' }}
                    </h1>
                    <p class="text-xl mb-6">
                        {{ $settings['hero_subtitle'] ?? 'Platform terpercaya untuk produk dan jasa teknik sipil' }}
                    </p>
                    @if($settings['hero_button_text'] ?? null)
                        <a href="{{ $settings['hero_button_link'] ?? route('products.index') }}" 
                           class="inline-block bg-white text-primary-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                            {{ $settings['hero_button_text'] }}
                        </a>
                    @endif
                </div>
                @if($settings['hero_image'] ?? null)
                    <div>
                        <img src="{{ Storage::url($settings['hero_image']) }}" alt="Hero" class="rounded-lg shadow-lg">
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Features Section -->
    @if(isset($settings['features']) && count($settings['features']) > 0)
        <section class="py-16 bg-gray-50 dark:bg-gray-900">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                @if($settings['features_title'] ?? null)
                    <div class="text-center mb-12">
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ $settings['features_title'] }}
                        </h2>
                        @if($settings['features_subtitle'] ?? null)
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ $settings['features_subtitle'] }}
                            </p>
                        @endif
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($settings['features'] as $feature)
                        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                            @if($feature['icon'] ?? null)
                                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center mb-4">
                                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                            @endif
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $feature['title'] ?? '' }}
                            </h3>
                            <p class="text-gray-600 dark:text-gray-400">
                                {{ $feature['description'] ?? '' }}
                            </p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- About Section -->
    @if($settings['about_title'] ?? null)
        <section class="py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
                    @if($settings['about_image'] ?? null)
                        <div>
                            <img src="{{ Storage::url($settings['about_image']) }}" alt="About" class="rounded-lg shadow-lg">
                        </div>
                    @endif
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                            {{ $settings['about_title'] }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">
                            {{ $settings['about_content'] ?? '' }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    @endif

    <!-- CTA Section -->
    @if($settings['cta_title'] ?? null)
        <section class="py-16 bg-primary-600 text-white">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h2 class="text-3xl font-bold mb-4">
                    {{ $settings['cta_title'] }}
                </h2>
                @if($settings['cta_subtitle'] ?? null)
                    <p class="text-xl mb-6">
                        {{ $settings['cta_subtitle'] }}
                    </p>
                @endif
                @if($settings['cta_button_text'] ?? null)
                    <a href="{{ $settings['cta_button_link'] ?? route('register') }}" 
                       class="inline-block bg-white text-primary-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100 transition-colors">
                        {{ $settings['cta_button_text'] }}
                    </a>
                @endif
            </div>
        </section>
    @endif
</x-app-layout>

