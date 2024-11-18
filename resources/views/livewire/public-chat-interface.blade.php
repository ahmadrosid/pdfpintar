<div class="flex flex-col border border-neutral-200 bg-white dark:bg-neutral-800 dark:border-neutral-700 h-full max-h-[92vh]">
    <div class="flex justify-between items-center p-2 border-b border-neutral-200 dark:border-neutral-700">
        <div class="text-sm text-neutral-600 dark:text-neutral-400">
            {{ __('Shared Chat') }}
        </div>
    </div>

    <div class="flex-1 overflow-y-auto">
        @if(!$currentThread || count($messages) == 0)
            <div class="flex items-center justify-center w-full h-full text-xl dark:text-neutral-300">
                {{__('No messages in this chat.')}}
            </div>
        @endif
        <div class="chat-messages flex flex-col">
            @if($currentThread)
                @foreach($messages as $message)
                    @if($message->role == 'user')
                        <div class="message bg-white dark:bg-neutral-800 p-4">
                            <div class="font-semibold text-teal-400 dark:text-teal-300 text-sm">
                                {{ __('User') }}
                            </div>
                            <div class="prose prose-sm dark:prose-invert max-w-none">
                                <x-markdown>{{ $message->content }}</x-markdown>
                            </div>
                        </div>
                    @elseif($message->role == 'assistant')
                        <div class="message bg-neutral-300/25 dark:bg-neutral-500/25 p-4">
                            <div class="font-semibold text-sm dark:text-neutral-200">
                                pdfpintar
                            </div>
                            <div class="prose prose-sm dark:prose-invert max-w-none">
                                <x-markdown theme="github-dark">{!! $message->content !!}</x-markdown>
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    
    <div class="border-t dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-700">
        <div class="p-4">
            <div class="flex flex-col items-center gap-4">
                <div class="text-center">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                        {{ __('Want to try PDFPintar for yourself?') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('Sign up now to chat with your own PDF documents using AI.') }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-md transition-colors">
                        {{ __('Sign up for free') }}
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-neutral-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 transition-colors">
                        {{ __('Log in') }}
                    </a>
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                    {{ __('No credit card required • Start chatting in minutes') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/dompurify/3.1.4/purify.min.js" integrity="sha512-W5fT2qIB5mnnYGQpzMLesMO7UmqtR7o712igk1FUXP+ftlu94UYDAngTS83l+0s3MwRmtqGDyWncZfiUjsCNHw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.2/marked.min.js" integrity="sha512-xeUh+KxNyTufZOje++oQHstlMQ8/rpyzPuM+gjMFYK3z5ILJGE7l2NvYL+XfliKURMpBIKKp1XoPN/qswlSMFA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endpush
