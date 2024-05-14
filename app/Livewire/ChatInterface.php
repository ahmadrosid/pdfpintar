<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI;
use Livewire\Attributes\On;

class ChatInterface extends Component
{
    public $messages = [];

    public $userInput = '';
    public $isWriting = false;

    public function sendMessage()
    {
        if (count($this->messages) == 0) {
            $this->messages[] = ['role' => 'system', 'content' => 'You are a helpful assistant.'];
        }
        $this->messages[] = [
            'role' => 'user',
            'content' => $this->userInput,
        ];
        $this->userInput = '';
        $this->isWriting = true;
        $this->dispatch('getAIStreamingResponse');
    }

    #[On('getAIStreamingResponse')]
    public function getAIStreamingResponse()
    {
        // if the last message role is not user then ignore
        if ($this->messages[count($this->messages) - 1]['role'] != 'user') {
            $this->isWriting = false;
            return;
        }

        $client = OpenAI::factory()->withApiKey(env('OPENAI_API_KEY'))->make();

        $response = $client->chat()->createStreamed([
            'model' => 'gpt-3.5-turbo',
            'messages' => $this->messages,
        ]);

        $aiResponse = '';

        foreach ($response as $chunk) {
            $text = $chunk->choices[0]->delta->content ?? '';
            $aiResponse .= $text;

            $this->stream(
                to: 'ai-response',
                content: $aiResponse,
                replace: true,
            );
        }

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $aiResponse,
        ];
        $this->isWriting = false;
    }

    public function render()
    {
        return <<<'HTML'
            <div class="flex flex-col p-2 border rounded-md h-full">
                <div class="flex-1 overflow-y-auto">
                    @if(count($messages) == 0)
                    <div class="flex items-center justify-center w-full h-full">
                        Ask any question about the document.
                    </div>
                    @endif
                    <div class="chat-messages flex flex-col gap-2">
                        @foreach ($messages as $message)
                        @if ($message['role'] == 'user' &&  $message['content'] != '')
                            <div class="message bg-gray-200 p-2 rounded-md {{ $message['role'] }}">
                                {{ $message['content'] }}
                            </div>
                        @elseif ($message['role'] == 'assistant')
                            <div class="message bg-gray-100 p-2 rounded-md">
                                {{ $message['content'] }}
                            </div>
                        @endif
                        @endforeach

                        <div class="message ai">
                            @if ($isWriting)
                                <span wire:stream="ai-response" class="p-2 block bg-gray-100 rounded-md">Thinking...</span>
                            @endif
                        </div>
                    </div>
                </div>

                <form wire:submit.prevent="sendMessage">
                    <div class="flex items-center gap-2">
                        <x-text-input class="flex-1" label="Message" wire:model="userInput" placeholder="Type your message here..."></x-text-input>
                        <x-primary-button wire:click="sendMessage">Send</x-primary-button>
                    </div>
                </form>
            </div>

            <script>
                document.addEventListener('livewire:load', function () {
                    window.addEventListener('user-message-added', function () {
                        const chatMessages = document.querySelector('.chat-messages');
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    });
                });
            </script>
        HTML;
    }
}
