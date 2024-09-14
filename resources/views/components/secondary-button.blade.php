<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-3 bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-md font-semibold text-xs text-neutral-700 dark:text-neutral-200 uppercase tracking-widest shadow-sm hover:bg-neutral-50 dark:hover:bg-neutral-600 focus:outline-none focus:ring-2 focus:ring-teal-500 dark:focus:ring-teal-400 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
