
<x-full-screen-layout>
    <div class="relative sm:flex sm:justify-center min-h-screen bg-white dark:bg-neutral-800 selection:bg-neutral-500 selection:text-white">
        <div class="top-0 inset-x-0 absolute bg-gradient-to-b from-white to-white/0 dark:from-neutral-800 dark:to-neutral-800">
            <x-public-navigation />
        </div>

        <div class="container max-w-7xl w-full mx-auto p-6 lg:p-8 my-16">
            <h1 class="text-2xl font-bold !leading-tight sm:text-3xl lg:text-4xl text-balance mb-4">
                {{__('Merge PDF')}}
            </h1>
            <livewire:merge-document-pdf />
        </div>
    </div>

    <x-public-footer />
</x-full-screen-layout>