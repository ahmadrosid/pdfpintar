<script>
    import { Textarea } from "$lib/components/ui/textarea/index.js";
    import ArrowUpIcon from "lucide-svelte/icons/arrow-up";
    import Loader2Icon from "lucide-svelte/icons/loader-2";
    let { text = $bindable(), sendMessage, isLoading } = $props();
    let ref = null;

    function handleKeyDown(event) {
        if (event.key === "Enter" && !(event.metaKey || event.shiftKey)) {
            event.preventDefault();
            sendMessage();
        }
    }

    $effect(() => {
        if (text) {
            ref.style.height = '0px';
            ref.style.height = ref.scrollHeight + 'px';
        } else {
            ref.style.height = '5px';
            ref.style.height = ref.scrollHeight + 'px';
        }
    });
</script>

<div class="p-4 sticky left-0 right-0 bottom-0">
    <div class="bg-white dark:bg-neutral-700 border border-neutral-300 dark:border-neutral-600 rounded-xl p-3">
        <Textarea
            bind:ref={ref}
            bind:value={text}
            rows="1"
            style="max-height: 24rem; resize: none;"
            placeholder={isLoading
                            ? "Thinking..."
                            : "Type your message here..."}
            onkeydown={handleKeyDown}
            class="mb-2 py-1 px-1 resize-none bg-transparent border-0 focus-visible:ring-0 focus:outline-none"/>
        <div class="flex justify-end">
            <button
                onclick={sendMessage}
                class="bg-neutral-900 text-white {text.length===0 && !isLoading ? 'opacity-30' : 'opacity-90'} hover:opacity-90 rounded-full p-1.5 shadow"
            >
                {#if isLoading}
                    <Loader2Icon class="size-4 animate-spin"/>
                {:else}
                    <ArrowUpIcon class="size-4"/>
                {/if}
            </button>
        </div>
    </div>
</div>
