<script>
    import Markdown from "$lib/components/Markdown.svelte";
    import CopyIcon from "lucide-svelte/icons/copy";
    import CopyCheckIcon from "lucide-svelte/icons/copy-check";

    let { message } = $props();

    let copied = $state(false);

    function copyMessage(message) {
        copied = true;
        navigator.clipboard.writeText(message);
        setTimeout(() => {
            copied =false;
        }, 2000);
    }
</script>

<div class="p-4 group {message.role === 'assistant' ? 'bg-neutral-100 dark:bg-neutral-800' : 'bg-white dark:bg-neutral-700'}">
    <p class="pb-4 text-sm font-medium {message.role === 'assistant' ? 'text-teal-500 dark:text-teal-400' : 'text-orange-500 dark:text-orange-400'}">
        {message.role === 'assistant' ? 'pdfpintar' : 'You'}
    </p>

    <div class="prose prose-sm dark:prose-invert max-w-none">
        <Markdown md={message.content}/>
    </div>

    <div class="pt-2 justify-end flex">
        <button class="opacity-70 hover:opacity-100 p-1 invisible group-hover:visible">
            {#if copied}
                <CopyCheckIcon class="size-3" />
            {:else}
                <CopyIcon onclick={() => copyMessage(message.content)} class="size-3" />
            {/if}
        </button>
    </div>
</div>