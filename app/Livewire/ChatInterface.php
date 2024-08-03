<?php

namespace App\Livewire;

use App\Models\Document;
use App\Models\Thread;
use App\Models\Message;
use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Livewire\Attributes\On;

class ChatInterface extends Component
{
    public Document $document;
    public $messages = [];
    public $userInput = 'Please summarize the following document in a few sentences.';
    public $isWriting = false;
    public $threadId;
    public $openaiThreadId;
    public $assistant_id;

    public function mount()
    {
        $this->loadMessages();
    }

    private function initializeThread()
    {
        if (!$this->assistant_id) {
            $assistant = OpenAI::assistants()->create([
                'name' => $this->document->file_name,
                'tools' => [
                    [
                        'type' => 'file_search',
                    ],
                ],
                'tool_resources' => [
                    'file_search' => [
                        'vector_stores' => [
                            [
                                'file_ids' => [
                                    $this->document->file_id,
                                ]
                            ]
                        ]
                    ]
                ],
                'instructions' => 'Your are a helpful assistant. Use the provided document to answer the user\'s question.',
                'model' => 'gpt-4o',
            ]);
            $this->assistant_id = $assistant->id;
        }

        $existingThread = Thread::where('document_id', $this->document->id)->first();

        if (!$existingThread) {
            $openaiThread = OpenAI::threads()->create([]);
            $existingThread = Thread::create([
                'openai_thread_id' => $openaiThread->id,
                'assistant_id' => $this->assistant_id,
                'document_id' => $this->document->id,
            ]);
        }

        $this->threadId = $existingThread->id;
        $this->openaiThreadId = $existingThread->openai_thread_id;
        $this->assistant_id = $existingThread->assistant_id;
    }

    private function loadMessages()
    {
        $existingThread = Thread::where('document_id', $this->document->id)->first();
        if (!$existingThread) {
            return;
        }

        $dbMessages = Message::where('thread_id', $existingThread->id)->orderBy('created_at', 'asc')->get();
        
        foreach ($dbMessages as $message) {
            $this->messages[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }
    }

    public function sendMessage()
    {
        if ($this->userInput === '') {
            return;
        }

        $this->messages[] = [
            'role' => 'user',
            'content' => $this->userInput,
        ];

        $this->userInput = '';
        $this->isWriting = true;
        $this->dispatch('getAssistantResponse');
    }

    #[On('getAssistantResponse')]
    public function getAssistantResponse()
    {
        $this->createMessage();

        $run = OpenAI::threads()->runs()->createStreamed($this->openaiThreadId, [
            'assistant_id' => $this->assistant_id,
        ]);

        $message_content = '';
        foreach ($run as $message) {
            $response = $message->response->toArray();
            $delta = $response['delta'] ?? [];
            $content = $delta['content'] ?? [];

            foreach ($content as $item) {
                if (isset($item['type']) && $item['type'] === 'text') {
                    $text = $item['text']['value'];
                    $this->stream(
                        to: 'ai-response',
                        content: $text,
                        replace: $message_content === '',
                    );
                    $message_content .= $text;
                }
            }
        }

        Message::create([
            'thread_id' => $this->threadId,
            'role' => 'user',
            'content' => $this->messages[count($this->messages) - 1]['content'],
        ]);
        Message::create([
            'thread_id' => $this->threadId,
            'role' => 'assistant',
            'content' => $message_content,
        ]);

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $message_content,
        ];

        $this->isWriting = false;
    }

    private function createMessage()
    {
        $this->initializeThread();

        OpenAI::threads()->messages()->create($this->openaiThreadId, [
            'role' => 'user',
            'content' => $this->messages[count($this->messages) - 1]['content'],
        ]);
    }

    public function render()
    {
        return view('livewire.chat');
    }
}