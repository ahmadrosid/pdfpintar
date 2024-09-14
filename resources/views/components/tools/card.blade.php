@props(['title', 'description', 'imageUrl', 'href', 'premium' => false])

<a wire:navigate href="{{ $href }}" 
   class="relative flex flex-col gap-2 rounded-lg border-2 border-transparent bg-neutral-200/75 p-4 text-center outline-none transition-all hover:border-neutral-500 dark:hover:border-neutral-600  dark:bg-neutral-700 dark:shadow-black/25 lg:max-w-xs">
    <div class="mx-auto mt-2">
        <img src="{{ $imageUrl }}" alt="{{ $title }}">
    </div>
    <h5 class="text-xl font-semibold mt-1">{{ $title }}</h5>
    <p class="text-sm text-foreground/60">{{ $description }}</p>

    @if($premium)
        <span class="absolute right-4 top-4 rounded-lg border border-red px-2 pb-1 pt-0.5 text-xs font-semibold leading-3 text-red">
            Upgrade
        </span>
    @endif
</a>