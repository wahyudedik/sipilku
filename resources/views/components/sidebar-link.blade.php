@props(['active' => false])

@php
$classes = ($active ?? false)
            ? 'bg-primary-50 dark:bg-gray-700 border-primary-500 text-primary-700 dark:text-primary-300'
            : 'border-transparent text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-gray-200';
@endphp

<a {{ $attributes->merge(['class' => $classes . ' group flex items-center px-3 py-2 text-sm font-medium border-l-4 transition-colors duration-150']) }}>
    {{ $slot }}
</a>

