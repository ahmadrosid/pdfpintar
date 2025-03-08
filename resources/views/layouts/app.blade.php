<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') . ' - ' . ($title ?? __('Chat with your PDF documents')) }}</title>
        <meta name="description" content="PDFPINTAR - Ubah Cara Anda Membaca PDF" />

        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="PDFPINTAR - Ubah Cara Anda Membaca PDF">
        <meta property="og:description" content="Gali Informasi Lebih Cepat. PDFPINTAR membantu Anda menggali informasi penting dengan mudah. Ajukan pertanyaan, dapatkan jawaban instan.">
        <meta property="og:image" content="https://res.cloudinary.com/dr15yjl8w/image/upload/v1722770981/public/e6w5shtwz1thgozzsoxb.png">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="id_ID">
        <meta property="og:site_name" content="pdfpintar">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Bricolage+Grotesque:opsz,wght@12..96,200..800&family=Manrope:wght@200..800&display=swap" rel="stylesheet">
        @if(config('app.env') == 'production')
        <script defer data-domain="pdfpintar.com" src="https://vince.ngooding.com/js/script.js"></script>
        @endif

        <!-- Scripts -->
        @filepondScripts
        @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/js/mingle.svelte.js'])
    </head>

    <body class="font-sans antialiased bg-neutral-100 dark:bg-neutral-900 dark:text-neutral-300">
        <div class="min-h-screen">
            @if (isset($banner))
            <div x-data="{
                    bannerVisible: false,
                    bannerVisibleAfter: 300,
                }"
                x-show="bannerVisible"
                x-transition:enter="transition ease-out duration-500"
                x-transition:enter-start="-translate-y-10"
                x-transition:enter-end="translate-y-0"
                x-transition:leave="transition ease-in duration-300"
                x-transition:leave-start="translate-y-0"
                x-transition:leave-end="-translate-y-10"
                x-init="
                    setTimeout(()=>{ bannerVisible = true }, bannerVisibleAfter);
                "
                class="fixed top-0 left-0 w-full h-auto py-2 duration-300 ease-out shadow-sm sm:py-0 sm:h-10 z-50" x-cloak>
                {{$banner}}
            </div>
            @endif
            <livewire:layout.navigation />

            <!-- Page Heading -->
            @if (isset($header))
                <header class="dark:bg-neutral-700/75 dark:text-white bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
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
