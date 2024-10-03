@php
$classes = Flux::classes()
    ->add('p-3 rounded-xl')
    ->add('bg-white dark:bg-zinc-700')
    ->add('border border-zinc-200 dark:border-zinc-600')
@endphp

<div {{ $attributes->class($classes) }} data-flux-card>
    {{ $slot }}
</div>
