<div class="flex flex-col border border-gray-200 bg-white h-full max-h-[92vh]">
    <div class="flex-1 overflow-y-auto">
        @if(count($messages) == 0)
        <div class="flex items-center justify-center w-full h-full text-xl">
            Ask any question about the document.
        </div>
        @endif
        <div class="chat-messages flex flex-col">
            @foreach ($messages as $message)
            @if ($message['role'] == 'user')
            <div class="message bg-white p-4 {{ $message['role'] }}">
                <div class="font-semibold text-orange-400 text-sm">
                    You
                </div>
                <div class="prose prose-sm">
                    <x-markdown>{{ $message['content'] }}</x-markdown>
                </div>
            </div>
            @elseif ($message['role'] == 'assistant')
            <div class="message bg-gray-300/25 p-4">
                <div class="font-semibold text-sm">
                    pdfpintar
                </div>
                <div class="prose prose-sm">
                    <x-markdown>{{ $message['content'] }}</x-markdown>
                </div>
            </div>
            @endif
            @endforeach

            <div>
                @if ($isWriting)
                <div class="message bg-gray-200 p-4">
                    <div class="font-semibold text-sm">
                        pdfpintar
                    </div>
                    <div x-data="{
                            init() {
                                const observer = new MutationObserver((mutations) => {
                                    mutations.forEach((mutation) => {
                                        if (mutation.type === 'childList') {
                                            const textContent = $refs.stream.textContent.trim();
                                            if ($refs.loader) {
                                                $refs.loader.remove();
                                            }
                                            if (textContent) {
                                                setTimeout(() => {
                                                    document.getElementById('scroll_target').scrollIntoView({ behavior: 'smooth' });
                                                }, 100);
                                                const markdownHtml = marked.parse(textContent);
                                                const markdownElement = $refs.markdown;
                                                markdownElement.innerHTML = DOMPurify.sanitize(markdownHtml);
                                            } else {
                                                observer.disconnect();
                                            }
                                        }
                                    });
                                });
                                observer.observe($refs.stream, {childList: true, subtree: true, characterData: true});
                            }
                        }">
                        <div x-ref="stream" class="hidden" wire:stream="ai-response"></div>
                        <div x-ref="markdown" class="prose prose-sm">
                            <div class="thinking-container pt-2">
                                <div class="dot"></div>
                                <div class="dot"></div>
                                <div class="dot"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <form wire:submit.prevent="sendMessage" class="p-4 bg-gray-50">
        <div class="flex items-end gap-2">
            <x-chat-setting />
            <textarea x-data="{
                        init() {
                            $el.style.height = '5px';
                            $el.style.height = $el.scrollHeight + 'px';
                        },
                        resize: () => {
                            $el.style.height = '5px';
                            $el.style.height = $el.scrollHeight + 'px';
                        },
                        handleSubmit: (e) => {
                            if (e.key === 'Enter') {
                                if (e.shiftKey) {
                                    return;
                                }
                                $wire.sendMessage();
                                e.preventDefault();
                            }
                        }
                    }" 
                    @keydown="handleSubmit" 
                    @input="resize" 
                    type="text" wire:model="userInput" placeholder="Type your message here..." rows="1" class="resize-none flex w-full max-h-[400px] px-3 py-2 text-sm bg-white border rounded-md border-neutral-300 placeholder:text-neutral-400 focus:ring-0 focus:border-neutral-300 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 overflow-hidden"></textarea>
            <div>
                <div class="flex gap-2">
                    <!-- <button x-on:click="
                            window.dispatchEvent(new CustomEvent('jumpToPage', {
                                detail: { pageIndex: 2 }
                            }));
                        ">
                        p:2
                    </button> -->
                    <button wire:click="sendMessage" class="bg-gray-800 hover:bg-gray-700 text-white py-2 px-3 rounded text-sm">Send</button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.4/purify.min.js" integrity="sha512-W5fT2qIB5mnnYGQpzMLesMO7UmqtR7o712igk1FUXP+ftlu94UYDAngTS83l+0s3MwRmtqGDyWncZfiUjsCNHw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js" integrity="sha512-xeUh+KxNyTufZOje++oQHstlMQ8/rpyzPuM+gjMFYK3z5ILJGE7l2NvYL+XfliKURMpBIKKp1XoPN/qswlSMFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
