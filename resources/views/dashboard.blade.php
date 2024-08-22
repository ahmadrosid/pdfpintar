<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg leading-tight">
            {{ __('Your documents') }}
        </h2>
    </x-slot>

    <livewire:document-list />
</x-app-layout>
