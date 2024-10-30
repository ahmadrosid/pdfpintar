<x-app-layout>
    @if(config('app.require_email_verification') && !Auth::user()->hasVerifiedEmail())
    <x-slot name="banner">
        <div class="bg-red-500 dark:bg-red-600 p-3">
            <div class="flex items-center justify-between w-full h-full px-3 mx-auto max-w-7xl">
                <div class="flex flex-col w-full h-full text-base leading-6 text-white duration-150 ease-out sm:flex-row sm:items-center opacity-80 hover:opacity-100">
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4 mr-1">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3" />
                            <path d="M12 9v4" />
                            <path d="M12 17h.01" />
                        </svg>
                        <strong class="font-semibold">{{__('Verify your email address')}}</strong><span class="hidden w-px h-4 mx-3 rounded-full sm:block bg-neutral-200"></span>
                    </span>
                    <span class="block pt-1 pb-2 leading-none sm:inline sm:pt-0 sm:pb-0">
                        @if (session('message'))
                                {{ session('message') }}
                        @else
                        {{__('Please verify your email address to start using pdfpintar.')}}
                        @endif
                    </span>
                </div>

                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button class="text-sm py-2 text-neutral-200 rounded-md hover:text-neutral-300 w-[150px] mr-4 font-bold hover:underline">
                        {{__('Resend email')}}
                    </button>
                </form>

                <button @click="bannerVisible=false; setTimeout(()=>{ bannerVisible = true }, 1000);" class="flex items-center flex-shrink-0 translate-x-1 ease-out duration-150 justify-center w-6 h-6 p-1.5 text-white rounded-full hover:bg-neutral-100 hover:text-black">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-full h-full">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </x-slot>
    @endif
    <x-slot name="header">
        <h2 class="font-semibold text-lg leading-tight">
            {{ __('Your documents') }}
        </h2>
    </x-slot>

    <livewire:document-list />
    {{-- <x-support-bubble /> --}}
</x-app-layout>