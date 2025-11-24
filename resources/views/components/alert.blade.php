@props(['type' => 'info', 'dismissible' => false])

@php
$typeClasses = [
    'success' => 'bg-green-50 border-green-400 text-green-700 dark:bg-green-900 dark:border-green-700 dark:text-green-200',
    'error' => 'bg-red-50 border-red-400 text-red-700 dark:bg-red-900 dark:border-red-700 dark:text-red-200',
    'warning' => 'bg-yellow-50 border-yellow-400 text-yellow-700 dark:bg-yellow-900 dark:border-yellow-700 dark:text-yellow-200',
    'info' => 'bg-blue-50 border-blue-400 text-blue-700 dark:bg-blue-900 dark:border-blue-700 dark:text-blue-200',
];
@endphp

<div x-data="{ show: true }" x-show="show" {{ $attributes->merge(['class' => 'p-4 border rounded-lg ' . $typeClasses[$type]]) }}>
    <div class="flex items-start">
        <div class="flex-1">
            {{ $slot }}
        </div>
        @if($dismissible)
            <button @click="show = false" class="ml-4 text-current opacity-50 hover:opacity-75">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        @endif
    </div>
</div>

