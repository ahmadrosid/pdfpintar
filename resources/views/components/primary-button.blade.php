<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2.5 bg-neutral-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-neutral-700 focus:bg-neutral-700 active:bg-neutral-900 focus:outline-none focus:ring-2 focus:ring-teal-500 focus:ring-offset-2 transition ease-in-out duration-150 dark:bg-neutral-800 dark:hover:bg-neutral-600 dark:focus:bg-neutral-600 dark:active:bg-neutral-800']) }}>
    {{ $slot }}
</button>
