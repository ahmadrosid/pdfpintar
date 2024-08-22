@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-300 focus:border-gray-500 focus:ring-gray-500 rounded-md shadow-sm dark:bg-gray-600 dark:border-gray-500 dark:focus:border-gray-500 dark:focus:ring-gray-600 dark:placeholder-gray-300/75']) !!}>
