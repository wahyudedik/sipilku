@props(['name', 'options' => [], 'placeholder' => 'Pilih...', 'value' => null])

<select {{ $attributes->merge([
    'name' => $name,
    'id' => $name,
    'class' => 'block w-full rounded-md border-gray-300 dark:border-gray-700 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-800 dark:text-gray-300 sm:text-sm'
]) }}>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif

    @foreach($options as $optionValue => $optionLabel)
        <option value="{{ $optionValue }}" {{ ($value !== null && $value == $optionValue) ? 'selected' : '' }}>
            {{ $optionLabel }}
        </option>
    @endforeach
</select>

