@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-teal-400 text-sm font-medium leading-5 text-neutral-900 focus:outline-none focus:border-teal-700 transition duration-150 ease-in-out dark:border-teal-500 dark:text-neutral-200 dark:focus:border-teal-600'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-neutral-500 hover:text-neutral-700 hover:border-neutral-300 focus:outline-none focus:text-neutral-700 focus:border-neutral-300 transition duration-150 ease-in-out dark:text-neutral-400 dark:hover:text-neutral-500 dark:hover:border-neutral-400 dark:focus:text-neutral-500 dark:focus:border-neutral-400';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
