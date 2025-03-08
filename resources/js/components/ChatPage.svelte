<script>
    import ChatInput from "$lib/components/ChatInput.svelte";

    let { wire, dataset } = $props();
    let text = $state('');

    function sendMessage() {}
</script>

<div class="h-screen max-h-[92vh] relative isolate flex flex-col flex-grow">
    <div class="p-2 border-b border-neutral-200 dark:border-neutral-700">
        This is the chat page! {dataset.document.file_name}
    </div>

    <div class="p-4 mb-2 flex-1">
        Chat body
    </div>

    <ChatInput bind:text {sendMessage} isLoading={false}/>
</div>
