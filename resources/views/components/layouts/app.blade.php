<x-full-screen-layout>
    <x-slot name="title">
        {{__('Your all-in-one PDF toolkit')}}
    </x-slot>
    <div class="relative sm:flex sm:justify-center min-h-screen bg-white dark:bg-neutral-800 selection:bg-neutral-500 selection:text-white">
        <div class="top-0 inset-x-0 absolute bg-gradient-to-b from-white to-white/0 dark:from-neutral-800 dark:to-neutral-800">
            <x-public-navigation />
        </div>

        {{$slot}}
    </div>

    <x-public-footer />
</x-full-screen-layout>