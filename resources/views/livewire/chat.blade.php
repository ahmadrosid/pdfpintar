<div class="flex flex-col border border-neutral-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 h-full max-h-[92vh]">
    <div class="flex-1 overflow-y-auto">
        @if(count($messages) == 0)
        <div class="flex items-center justify-center w-full h-full text-xl dark:text-neutral-300">
            {{__('Ask any question about this PDF document.')}}
        </div>
        @endif
        <div class="chat-messages flex flex-col">
            @foreach ($messages as $message)
            @if ($message['role'] == 'user')
            <div class="message bg-white dark:bg-neutral-800 p-4 {{ $message['role'] }}">
                <div class="font-semibold text-teal-400 dark:text-teal-300 text-sm">
                    {{Auth::user()->name}}
                </div>
                <div class="prose prose-sm dark:prose-invert">
                    <x-markdown>{{ $message['content'] }}</x-markdown>
                </div>
            </div>
            @elseif ($message['role'] == 'assistant')
            <div class="message bg-neutral-300/25 dark:bg-neutral-500/25 p-4">
                <div class="font-semibold text-sm dark:text-neutral-200">
                    pdfpintar
                </div>
                <div class="prose prose-sm dark:prose-invert">
                    <x-markdown theme="github-dark">{!! $message['content'] !!}</x-markdown>
                </div>
                <x-chat-action :index="$loop->index" :message="$message" />
            </div>
            @endif
            @endforeach

            <div>
                @if ($isWriting)
                <div class="message bg-neutral-200 dark:bg-neutral-600 p-4">
                    <div class="font-semibold text-sm dark:text-neutral-200">
                        pdfpintar
                    </div>
                    <div
                        class="py-4" 
                        x-data="{
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
                        <div x-ref="markdown" class="prose prose-sm dark:prose-invert">
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

    <form wire:submit.prevent="sendMessage" class="p-4 bg-neutral-50 dark:bg-neutral-700">
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
                    type="text" wire:model="userInput" placeholder="Type your message here..." rows="1" class="resize-none flex w-full max-h-[400px] px-3 py-2 text-sm bg-white dark:bg-neutral-600 border rounded-md border-neutral-300 dark:border-neutral-400 placeholder:text-neutral-400 dark:placeholder:text-neutral-600 focus:ring-0 focus:border-neutral-300 dark:focus:border-neutral-400 focus:outline-none disabled:cursor-not-allowed disabled:opacity-50 overflow-hidden"></textarea>
            <div>
                <div class="flex">
                    <flux:button wire:click="sendMessage">Send</flux:button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.4/purify.min.js" integrity="sha512-W5fT2qIB5mnnYGQpzMLesMO7UmqtR7o712igk1FUXP+ftlu94UYDAngTS83l+0s3MwRmtqGDyWncZfiUjsCNHw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js" integrity="sha512-xeUh+KxNyTufZOje++oQHstlMQ8/rpyzPuM+gjMFYK3z5ILJGE7l2NvYL+XfliKURMpBIKKp1XoPN/qswlSMFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
