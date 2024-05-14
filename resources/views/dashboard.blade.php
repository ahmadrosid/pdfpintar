<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="flex justify-between items-center py-5 border-b border-gray-200">
                    <div class="px-4">
                        <x-text-input placeholder="Search documents" class="mt-2 block w-full" />
                    </div>
                    <livewire:upload-document />
                </div>
                <div class="p-6 text-gray-900">
                    <livewire:document-list />
                </div>
            </div>
        </div>
    </div>
</x-app-layout>