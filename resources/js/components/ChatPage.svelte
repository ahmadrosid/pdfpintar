<script>
    import ChatInput from "$lib/components/ChatInput.svelte";
    import MessageItem from "$lib/components/MessageItem.svelte";
    import Trash2Icon from "lucide-svelte/icons/trash-2";
    import LoadingIcon from "lucide-svelte/icons/circle-dashed";
    import {events} from "fetch-event-stream";

    let {wire, dataset} = $props();
    let text = $state('');
    let messages = $state(dataset.messages);
    let thread = $state(dataset.thread);
    let pageRefNode = $state(null);
    let isLoading = $state(false);
    let waitingAssistantResponse = $state(false);

    async function sendMessage() {
        try {
            messages.push({
                role: "user",
                content: text,
            });

            setTimeout(() => {
                pageRefNode.scrollTop = pageRefNode.scrollHeight;
            }, 100);

            let payload = {
                documentId: dataset.document.id,
                text: text,
                threadId: thread?.id,
            };

            text = "";
            isLoading = true;
            waitingAssistantResponse = true;
            let abort = new AbortController();
            let res = await fetch("/chat/stream", {
                method: "POST",
                signal: abort.signal,
                headers: {
                    "content-type": "application/json",
                    "X-CSRF-TOKEN": dataset.csrf,
                },
                body: JSON.stringify(payload),
            });

            if (!res.ok) {
                throw res;
            }

            let stream = events(res, abort.signal);
            for await (const raw of stream) {
                const event = JSON.parse(raw.data);
                switch (event.type) {
                    case "thread":
                        thread = event.data;
                        break;
                    case "token":
                        waitingAssistantResponse = false;
                        let message = messages[messages.length - 1];
                        if (message.role === "assistant") {
                            message.content += event.data;
                        } else {
                            messages.push({
                                role: "assistant",
                                content: event.data,
                            });
                        }
                        pageRefNode.scrollTop = pageRefNode.scrollHeight + 500;
                        break;
                }
            }
        } catch (e) {
            console.log(e);
        } finally {
            isLoading = false;
        }
    }

    function scrollToBottom(node) {
        node.scrollTop = node.scrollHeight;
        pageRefNode = node;
    }

    async function clearMessages() {
        await wire.clearMessages();
        // in svelte resetting messages doesn't work, so we reload the page
        window.location.reload();
    }

    function typingEffect(node) {
        const text = node.textContent;
        const baseText = text.replace(/\.+$/, ''); // Remove trailing dots
        let count = 0;
        let timer;

        function animate() {
            count = (count + 1) % 4; // Cycle through 0, 1, 2, 3
            const dots = '.'.repeat(count);
            node.textContent = baseText + dots;
            timer = setTimeout(animate, 300); // Change dots every 300ms
        }

        animate(); // Start the animation

        return {
            destroy() {
                clearTimeout(timer); // Clean up when component is destroyed
            }
        };
    }

</script>

<div use:scrollToBottom
     class="h-screen max-h-[92vh] relative isolate flex flex-col flex-grow overflow-y-scroll scrollbar-thin">
    <div
        class="p-1.5 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 bg-white dark:bg-neutral-800 flex items-center justify-between">
        <p class="px-1 text-sm font-medium">{thread?.title}</p>
        <button onclick={clearMessages} class="flex gap-2 items-center p-1 opacity-50 hover:opacity-100">
            <Trash2Icon class="size-3"/>
            <span class="text-sm">{dataset.labels.delete}</span>
        </button>
    </div>

    <div class="mb-2 flex-1">
        {#each dataset.messages as message}
            <MessageItem {message} labels={dataset.labels}/>
        {/each}
        {#if waitingAssistantResponse}
            <div class="flex p-4 items-center">
                <p use:typingEffect class="animate-pulse font-medium text-orange-500 text-sm">Thinking...</p>
            </div>
        {/if}
    </div>

    <ChatInput bind:text {sendMessage} {isLoading}/>
</div>
