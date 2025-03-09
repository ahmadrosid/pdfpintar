<script>
    import Markdown from "$lib/components/Markdown.svelte";
    import CopyIcon from "lucide-svelte/icons/copy";
    import CopyCheckIcon from "lucide-svelte/icons/copy-check";
    import * as Popover from "$lib/components/ui/popover/index.js";

    let { message, labels } = $props();

    let copied = $state(false);

    function copyMessage(message) {
        copied = true;
        navigator.clipboard.writeText(message);
        setTimeout(() => {
            copied =false;
        }, 2000);
    }
</script>

<div class="p-4 group {message.role === 'user' ? 'bg-neutral-100 dark:bg-neutral-700/70' : 'bg-white dark:bg-neutral-800'}">
    <p class="pb-4 text-sm font-medium {message.role === 'user' ? 'text-teal-500 dark:text-teal-400' : 'text-orange-500 dark:text-orange-400'}">
        {message.role === 'assistant' ? 'pdfpintar' : 'You'}
    </p>

    <div class="prose prose-sm dark:prose-invert max-w-none">
        <Markdown md={message.content}/>
    </div>

    <div class="pt-2 justify-end flex gap-3 items-center">
        <button onclick={() => copyMessage(message.content)} class="opacity-70 hover:opacity-100 p-1 invisible group-hover:visible text-xs flex gap-2 items-center">
            {#if copied}
                <CopyCheckIcon class="size-3" /> Copied
            {:else}
                <CopyIcon class="size-3" /> Copy
            {/if}
        </button>
        <Popover.Root>
            <Popover.Trigger>
                <p class="flex gap-2 items-center text-xs opacity-70 hover:opacity-100 invisible group-hover:visible">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" class="text-neutral-600 dark:text-neutral-300 size-3">
                        <path d="M9 11v6l2-2M9 17l-2-2" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M22 10v5c0 5-2 7-7 7H9c-5 0-7-2-7-7V9c0-5 2-7 7-7h5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M22 10h-4c-3 0-4-1-4-4V2l8 8Z" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    Download
                </p>
            </Popover.Trigger>
            <Popover.Content class="w-44 p-2 bg-white dark:bg-neutral-600 border-neutral-200 dark:border-neutral-600" align="end">
                <div class="flex flex-col">
                    <button class="py-1.5 px-3 text-sm hover:opacity-70 dark:hover:bg-neutral-800 rounded-md flex">{labels.download_as_pdf}</button>
                    <button class="py-1.5 px-3 text-sm hover:opacity-70 dark:hover:bg-neutral-800 rounded-md flex">{labels.download_as_excel}</button>
                    <button class="py-1.5 px-3 text-sm hover:opacity-70 dark:hover:bg-neutral-800 rounded-md flex">{labels.download_as_word}</button>
                </div>
            </Popover.Content>
          </Popover.Root>
          
    </div>
</div>
