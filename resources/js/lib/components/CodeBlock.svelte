<script>
    import CopyIcon from "lucide-svelte/icons/copy";
    import CopyCheckIcon from "lucide-svelte/icons/copy-check";
    let showCopied = $state(false);
    let { children } = $props();

    let pre;

    function copyToClipboard() {
        if (pre) {
            navigator.clipboard.writeText(pre.textContent || "");
            showCopied = true;
            setTimeout(() => (showCopied = false), 2000);
        }
    }
</script>

<div
    class="py-6 my-3 px-3 font-mono border border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-700/70 text-black rounded-xl overflow-hidden relative group/code"
>
    <button
        onclick={copyToClipboard}
        class="absolute text-neutral-500 dark:text-neutral-300 top-3 right-4 flex items-center gap-2 py-1 px-2 border border-transparent hover:border-dashed hover:border-neutral-400 hover:rounded-md"
    >
        <span class="invisible group-hover/code:visible text-sm">
            {showCopied ? "Copied" : "Copy"}
        </span>
        {#if showCopied}
            <CopyCheckIcon class="size-3" />
        {:else}
            <CopyIcon class="size-3" />
        {/if}
    </button>

    <pre class="m-0 max-w-[600px] dark:text-white" bind:this={pre}>{@render children?.()}</pre>
</div>
