@props(['name', 'rows' => 3])

<textarea {{ $attributes->merge([
    'name' => $name,
    'id' => $name,
    'rows' => $rows,
    'class' => 'block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 sm:text-sm'
]) }}>{{ $slot }}</textarea>

