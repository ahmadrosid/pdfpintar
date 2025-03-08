<script>
    import ChatInput from "$lib/components/ChatInput.svelte";
    import Markdown from "$lib/components/Markdown.svelte";

    let { wire, dataset } = $props();
    let text = $state('');

    function sendMessage() {}
</script>

<div class="h-screen max-h-[92vh] relative isolate flex flex-col flex-grow">
    <div class="p-2 border-b border-neutral-200 dark:border-neutral-700">
        {dataset.document.file_name}
    </div>

    <div class="mb-2 flex-1">
        {#each dataset.messages as message}
            <div class="bg-white p-4 {message.role === 'assistant' ? 'dark:bg-neutral-800' : 'dark:bg-neutral-700'}">
                <p class="pb-4 text-sm font-medium {message.role === 'assistant' ? 'text-teal-500 dark:text-teal-400' : 'text-orange-500 dark:text-orange-400'}">
                    {message.role === 'assistant' ? 'pdfpintar' : 'You'}
                </p>

                <div class="prose dark:prose-invert max-w-none">
                    <Markdown md={message.content}/>
                </div>
            </div>
        {/each}
    </div>

    <ChatInput bind:text {sendMessage} isLoading={false}/>
</div>
