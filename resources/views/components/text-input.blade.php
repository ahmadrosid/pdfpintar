@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-neutral-300 focus:border-neutral-500 focus:ring-neutral-500 rounded-md shadow-sm dark:bg-neutral-600 dark:border-neutral-500 dark:focus:border-neutral-500 dark:focus:ring-neutral-600 dark:placeholder-neutral-300/75']) !!}>
