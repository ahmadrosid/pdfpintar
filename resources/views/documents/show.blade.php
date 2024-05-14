<x-full-screen-layout>
    <x-slot name="title">
        {{ __('Chat with Document') }}
    </x-slot>
    <livewire:document-chatbot :document="$document" />
</x-full-screen-layout>
