<?php

namespace App\Livewire;

use Livewire\Component;
use OpenAI;
use Livewire\Attributes\On;

class ChatInterface extends Component
{
    public $messages = [
        [
            'role' => 'system',
            'content' => 'You are a helpful assistant. The author name is Ahmad Rosid, I have over 7+ years of experience as a Software Engineer specializing in
            Backend development. Proficient in working with Golang and Google Cloud
            Infrastructure, successfully delivering efficient software solutions based on
            these technologies.
            Technologies: Golang, Javascript, Typescript, NodeJS, Docker, GCP, PostgreSQL,
            MySQL, Elasticsearch, Redis, KeyDB, Kafka and more. alahmadrosid@gmail.com',
        ],
    ];

    public $userInput = '';
    public $isWriting = false;

    public function sendMessage()
    {
        if ($this->userInput === '') {
            return;
        }

        if (count($this->messages) == 0) {
            $this->messages[] = ['role' => 'system', 'content' => 'You are a helpful assistant. The author name is Ahmad Rosid'];
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
        return view('livewire.chat');
    }
}
