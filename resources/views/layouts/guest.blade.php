<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="PDFPINTAR - Ubah Cara Anda Membaca PDF">
        <meta property="og:description" content="Gali Informasi Lebih Cepat. PDFPINTAR membantu Anda menggali informasi penting dengan mudah. Ajukan pertanyaan, dapatkan jawaban instan.">
        <meta property="og:image" content="https://res.cloudinary.com/dr15yjl8w/image/upload/v1722770981/public/e6w5shtwz1thgozzsoxb.png">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="id_ID">
        <meta property="og:site_name" content="pdfpintar">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-neutral-900 antialiased dark:bg-neutral-800 dark:text-neutral-300">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-neutral-100 dark:bg-neutral-800">
            <div>
                <a href="/" wire:navigate>
                    <x-application-logo class="w-20 h-20 fill-current text-neutral-500 dark:text-neutral-300" />
                </a>
            </div>

            <div class="w-full sm:max-w-lg mt-6 px-12 py-8 bg-white shadow-md overflow-hidden sm:rounded-xl dark:bg-neutral-700 border dark:border-neutral-600">
                {{ $slot }}
            </div>
        </div>
        @stack('scripts')
        <script>
        function updateDarkMode() {
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }

        updateDarkMode();
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', updateDarkMode);
        </script>
    </body>
</html>
