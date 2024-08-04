<x-full-screen-layout>
    <div class="relative sm:flex sm:justify-center sm:items-center min-h-screen bg-square bg-center bg-white dark:bg-dots-lighter selection:bg-red-500 selection:text-white">
        <div class="top-0 inset-x-0 absolute bg-gradient-to-b from-white to-white/0">
            <div class="flex justify-between container mx-auto p-4">
                <div>
                    <p class="font-extrabold tracking-wide text-black">
                        PDFPINTAR
                    </p>
                </div>
                <div>
                    @auth
                    <a href="{{ route('documents.index') }}" class="font-medium text-sm text-gray-600 hover:text-gray-900 hover:underline focus:outline focus:outline-2 focus:rounded-sm focus:outline-none">
                        Documents
                    </a>
                    @else
                    <a href="{{ route('login') }}" class="font-medium text-sm py-2 px-3 text-gray-600 hover:text-primary focus:outline focus:outline-2 focus:rounded-sm focus:outline-none">
                        Log in
                    </a>
                    <a href="{{ route('register') }}" class="font-medium text-sm ml-4 py-2 px-3 rounded-md text-gray-600 hover:text-primary focus:outline focus:outline-2 focus:rounded-sm focus:outline-none">
                        Register
                    </a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto p-6 lg:p-8">
            <div class="mt-[70px] max-w-5xl mx-auto pb-8 text-center">
                <h1 class="text-3xl font-bold text-gray-900 md:text-4xl xl:text-6xl xl:leading-tight">
                    Ubah Cara Anda Membaca PDF <br />
                    Gali Informasi Lebih Cepat
                </h1>
                <div class="max-w-2xl mx-auto">
                    <h2 class="mt-6 leading-snug text-gray-500 xl:mt-5 xl:text-xl">
                        PDFPINTAR membantu Anda menggali informasi penting dengan mudah. Ajukan pertanyaan, dapatkan jawaban instan.
                    </h2>
                </div>
                <div class="flex gap-4 py-8 justify-center items-center">
                    @auth
                    <a href="{{ route('dashboard') }}">
                        <x-primary-button>Dashboard</x-primary-button>
                    </a>
                    @else
                    <a href="{{ route('login') }}">
                        <x-primary-button>Try for Free</x-primary-button>
                    </a>
                    @endif
                    <livewire:modal-demo />
                </div>
            </div>
            <div class="mx-auto max-w-7xl rounded-3xl shadow-2xl overflow-hidden p-2 bg-gray-700">
                <img src="http://res.cloudinary.com/dr15yjl8w/image/upload/v1722770981/public/e6w5shtwz1thgozzsoxb.png" class="rounded-2xl" />
            </div>
        </div>
    </div>

    <footer class="bg-white">
        <div class="px-4 py-12 mx-auto max-w-7xl sm:px-6 md:flex md:items-center md:justify-between lg:px-8">
            <div class="flex justify-center space-x-6 md:order-2">
                <span class="inline-flex justify-center w-full gap-3 lg:ml-auto md:justify-start md:w-auto">
                    <a href="https://github.com/ahmadrosid" class="w-6 h-6 transition fill-black hover:text-blue-500">
                        <span class="sr-only">github</span>
                        <x-icon-github class="w-5 h-5 md hydrated" aria-label="logo github" />
                    </a>
                    <a href="https://twitter.com/_ahmadrosid" class="w-6 h-6 transition fill-black hover:text-blue-500">
                        <span class="sr-only">twitter</span>
                        <x-icon-twitter class="w-5 h-5 md hydrated" aria-label="logo twitter" />
                    </a>
                    <a href="https://linkedin.com/in/ahmadrosid" class="w-6 h-6 transition fill-black hover:text-blue-500">
                        <span class="sr-only">Linkedin</span>
                        <x-icon-linkedin class="w-5 h-5 md hydrated" role="img" aria-label="logo linkedin" />
                    </a>
                </span>
            </div>
            <div class="mt-8 md:mt-0 md:order-1">
                <p class="text-base text-center text-gray-400">
                    <span class="mx-auto mt-2 text-sm text-gray-500">
                        Copyright © 2023
                        <a href="https://ahmadrosid.com" class="mx-2 text-blue-500 hover:underline" rel="noopener noreferrer">
                            @ahmadrosid
                        </a>
                    </span>
                </p>
            </div>
        </div>
    </footer>
</x-full-screen-layout>