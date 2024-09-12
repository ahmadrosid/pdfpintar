<x-full-screen-layout>
    <div class="relative sm:flex sm:justify-center min-h-screen bg-square bg-center bg-white dark:bg-neutral-800 selection:bg-neutral-500 selection:text-white">
        <div class="top-0 inset-x-0 absolute bg-gradient-to-b from-white to-white/0 dark:from-neutral-800 dark:to-neutral-800">
            <x-public-navigation />
        </div>

        <div class="max-w-7xl w-full mx-auto p-6 lg:p-8 mt-12">
            <livewire:markdown-converter />
        </div>
    </div>

    <x-public-footer />
</x-full-screen-layout>