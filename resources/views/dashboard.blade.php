<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-gray-800 leading-tight">
            {{ __('Your documents') }}
        </h2>
    </x-slot>

    <livewire:document-list />
</x-app-layout>
