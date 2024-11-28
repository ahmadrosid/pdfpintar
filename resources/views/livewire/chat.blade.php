<div class="flex flex-col border border-neutral-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 h-full max-h-[92vh]">
    <div class="flex justify-between items-center p-2 border-b border-neutral-200 dark:border-neutral-700">
        <div class="flex items-center gap-2">
            <button wire:click="newChat" class="text-sm text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-200">
                {{ __('New Chat') }}
            </button>
            <button wire:click="clearMessages" class="text-sm text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-200">
                {{ __('Clear Chat') }}
            </button>
        </div>
        <div class="flex items-center gap-2">
            <button 
                wire:click="toggleShare" 
                class="text-sm {{ $this->shareUrl ? 'text-green-600 dark:text-green-400' : 'text-neutral-600 dark:text-neutral-400' }} hover:text-neutral-900 dark:hover:text-neutral-200"
            >
                @if($this->shareUrl)
                    {{ $document->is_public ? __('Disable Sharing') : __('Enable Sharing') }}
                @else
                    {{ __('Share Chat') }}
                @endif
            </button>
            @if($this->shareUrl && $document->is_public)
                <button 
                    x-data="{ copied: false }"
                    x-on:click="
                        navigator.clipboard.writeText('{{ $this->shareUrl }}');
                        copied = true;
                        $dispatch('notify', {message: '{{ __('Share link copied!') }}'});
                        setTimeout(() => copied = false, 2000)
                    "
                    class="text-sm text-neutral-600 hover:text-neutral-900 dark:text-neutral-400 dark:hover:text-neutral-200 flex items-center gap-1"
                >
                    <span x-show="!copied">{{ __('Copy Link') }}</span>
                    <span x-show="copied" class="text-green-500">{{ __('Copied!') }}</span>
                    <svg x-show="!copied" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 7.5V6.108c0-1.135.845-2.098 1.976-2.192.373-.03.748-.057 1.123-.08M15.75 18H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08M15.75 18.75v-1.875a3.375 3.375 0 00-3.375-3.375h-1.5a1.125 1.125 0 01-1.125-1.125v-1.5A3.375 3.375 0 006.375 7.5H5.25m11.9-3.664A2.251 2.251 0 0015 2.25h-1.5a2.251 2.251 0 00-2.15 1.586m5.8 0c.065.21.1.433.1.664v.75h-6V4.5c0-.231.035-.454.1-.664M6.75 7.5H4.875c-.621 0-1.125.504-1.125 1.125v12c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V16.5a9 9 0 00-9-9z" />
                    </svg>
                    <svg x-show="copied" class="w-4 h-4 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                </button>
            @endif
        </div>
    </div>
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
                <div class="prose prose-sm dark:prose-invert max-w-none">
                    <x-markdown>{{ $message['content'] }}</x-markdown>
                </div>
            </div>
            @elseif ($message['role'] == 'assistant')
            <div class="message bg-neutral-300/25 dark:bg-neutral-500/25 p-4">
                <div class="font-semibold text-sm dark:text-neutral-200">
                    pdfpintar
                </div>
                <div class="prose prose-sm dark:prose-invert max-w-none">
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
                <x-primary-button wire:click="sendMessage">Send</x-primary-button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.4/purify.min.js" integrity="sha512-W5fT2qIB5mnnYGQpzMLesMO7UmqtR7o712igk1FUXP+ftlu94UYDAngTS83l+0s3MwRmtqGDyWncZfiUjsCNHw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js" integrity="sha512-xeUh+KxNyTufZOje++oQHstlMQ8/rpyzPuM+gjMFYK3z5ILJGE7l2NvYL+XfliKURMpBIKKp1XoPN/qswlSMFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
