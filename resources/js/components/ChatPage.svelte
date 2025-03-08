<script>
    import ChatInput from "$lib/components/ChatInput.svelte";
    import MessageItem from "$lib/components/MessageItem.svelte";
    import { events } from "fetch-event-stream";

    let { wire, dataset } = $props();
    let text = $state('');
    let messages = $state(dataset.messages);
    let threadId = $state(dataset.threadId);

    async function sendMessage() {
        try {
            messages.push({
                role: "user",
                content: text,
            });
            let abort = new AbortController();
            let res = await fetch("/chat/stream", {
                method: "POST",
                signal: abort.signal,
                headers: {
                    "content-type": "application/json",
                    "X-CSRF-TOKEN": dataset.csrf,
                },
                body: JSON.stringify({
                    stream: true,
                    documentId: dataset.document.id,
                    text: text,
                    threadId: threadId,
                }),
            });

            if (!res.ok) {
                throw res;
            }

            text = "";
            messages.push({
                role: "assistant",
                content: "",
            });

            let stream = events(res, abort.signal);
            for await (const raw of stream) {
                const event = JSON.parse(raw.data);
                if (event.type === "token") {
                    console.log(event);
                }
                switch (event.type) {
                    case "token":
                        let message = messages[messages.length - 1];
                        message.content += event.data;
                        break;
                }
            }
        } catch (e) {
            console.log(e);
        }
    }
</script>

<div class="h-screen max-h-[92vh] relative isolate flex flex-col flex-grow overflow-y-scroll scrollbar-thin">
    <div class="p-2 border-b border-neutral-200 dark:border-neutral-700 sticky top-0 z-10 bg-white dark: bg-neutral-800">
        {dataset.document.file_name}
    </div>

    <div class="mb-2 flex-1">
        {#each dataset.messages as message}
            <MessageItem {message}/>
        {/each}
    </div>

    <ChatInput bind:text {sendMessage} isLoading={false}/>
</div>
