<x-full-screen-layout>
    <x-slot name="title">
        {{__('Chat with your PDF documents')}}
    </x-slot>

    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-square bg-center bg-white dark:bg-neutral-800 selection:bg-neutral-500 selection:text-white pb-12">
        <div class="top-0 inset-x-0 absolute bg-gradient-to-b from-white to-white/0 dark:from-neutral-800 dark:to-neutral-800">
            <x-public-navigation />
        </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto p-6 lg:p-8">
                <div class="mt-[70px] max-w-5xl mx-auto pb-8 text-center">
                    <h1 class="text-3xl text-balance font-bold text-neutral-900 md:text-4xl xl:text-6xl xl:leading-tight dark:text-neutral-200">
                        {{__('Stop Wasting Time Reading PDF Start Asking Questions')}}
                    </h1>
                    <div class="max-w-2xl mx-auto">
                        <h2 class="mt-6 leading-snug text-neutral-500 xl:mt-5 xl:text-xl dark:text-neutral-400">
                            {{__('With pdfpintar, your PDFs become a source of interactive learning. Ask questions, gain insights, and discover what you didn\'t know.')}}
                        </h2>
                    </div>
                    <div class="flex gap-4 py-8 justify-center items-center">
                        @auth
                        <a href="{{ route('dashboard') }}">
                            <x-primary-button>{{__('Dashboard')}}</x-primary-button>
                        </a>
                        @else
                        <a href="{{ route('login') }}">
                            <x-primary-button>{{__('Try now for free')}}</x-primary-button>
                        </a>
                        @endif
                        <livewire:modal-demo />
                    </div>
                </div>
                <div class="mx-auto max-w-7xl rounded-3xl shadow-2xl overflow-hidden p-2 bg-neutral-700 dark:bg-neutral-800">
                    <img src="http://res.cloudinary.com/dr15yjl8w/image/upload/v1722770981/public/e6w5shtwz1thgozzsoxb.png" class="rounded-2xl" />
                </div>
            </div>
        </div>
    </div>

    <x-public-footer />
</x-full-screen-layout>
