<div class="flex justify-between container mx-auto p-4">
    <a href="/">
        <p class="font-extrabold tracking-wide text-black dark:text-neutral-300">
            PDFPINTAR
        </p>
    </a>
    <div>
        <a href="{{ route('tools.pdf-generator') }}" class="font-medium text-sm text-neutral-600 hover:text-neutral-900 hover:underline focus:outline focus:outline-2 focus:rounded-sm focus:outline-none dark:text-neutral-300 dark:hover:text-neutral-600 pr-4">
            PDF Generator
        </a>
        @auth
        <a href="{{ route('documents.index') }}" class="font-medium text-sm text-neutral-600 hover:text-neutral-900 hover:underline focus:outline focus:outline-2 focus:rounded-sm focus:outline-none dark:text-neutral-300 dark:hover:text-neutral-600">
            Documents
        </a>
        @else
        <a href="{{ route('login') }}" class="font-medium text-sm py-2 px-3 text-neutral-600 hover:text-primary focus:outline focus:outline-2 focus:rounded-sm focus:outline-none dark:text-neutral-400 dark:hover:text-neutral-600">
            Log in
        </a>
        <a href="{{ route('register') }}" class="font-medium text-sm ml-4 py-2 px-3 rounded-md text-neutral-600 hover:text-primary focus:outline focus:outline-2 focus:rounded-sm focus:outline-none dark:text-neutral-400 dark:hover:text-neutral-600">
            Register
        </a>
        @endauth
    </div>
</div>