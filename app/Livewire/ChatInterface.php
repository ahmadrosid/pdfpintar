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
    public $userInput = '';
    public $isWriting = false;
    public $threadId;
    public $openaiThreadId;
    public $assistant_id;

    public function mount()
    {
        $this->loadMessages();
    }

    private function createAssistant()
    {
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
        return $assistant;
    }

    private function initializeThread()
    {
        if (!$this->assistant_id) {
            $assistant = $this->createAssistant();
            $this->assistant_id = $assistant->id;
        }

        $existingThread = Thread::where('document_id', $this->document->id)->orderBy('created_at', 'desc')->first();

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
        $existingThread = Thread::where('document_id', $this->document->id)->orderBy('created_at', 'desc')->first();
        if (!$existingThread) {
            return;
        }

        $this->threadId = $existingThread->id;
        $this->openaiThreadId = $existingThread->openai_thread_id;
        $this->assistant_id = $existingThread->assistant_id;
        $dbMessages = Message::where('thread_id', $this->threadId)->orderBy('id', 'asc')->get();
        
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
                    $pattern = '/【\d+:\d+†.*?】/';
                    $text = preg_replace($pattern, '', $item['text']['value']);
                    $this->stream(
                        to: 'ai-response',
                        content: $text,
                        replace: $message_content === '',
                    );
                    $message_content .= $text;
                }
            }
        }

        $this->isWriting = false;
        if ($message_content === '') {
            return;
        }

        $lastMessage = end($this->messages);

        Message::insert([
            [
                'thread_id' => $this->threadId,
                'role' => 'user',
                'content' => $lastMessage['content'],
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'thread_id' => $this->threadId,
                'role' => 'assistant',
                'content' => $message_content,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);

        $this->messages[] = [
            'role' => 'assistant',
            'content' => $message_content,
        ];
    }

    private function createMessage()
    {
        $this->initializeThread();

        OpenAI::threads()->messages()->create($this->openaiThreadId, [
            'role' => 'user',
            'content' => $this->messages[count($this->messages) - 1]['content'],
        ]);
    }

    public function clearMessages()
    {
        Message::where('thread_id', $this->threadId)->delete();
        $this->messages = [];
        $this->dispatch('settingsActionCompleted');
    }

    public function newChat() {
        $this->threadId = null;
        $this->messages = [];
        $this->assistant_id = null;

        $assistant = $this->createAssistant();
        $this->assistant_id = $assistant->id;
        
        $openaiThread = OpenAI::threads()->create([]);
        $existingThread = Thread::create([
            'openai_thread_id' => $openaiThread->id,
            'assistant_id' => $this->assistant_id,
            'document_id' => $this->document->id,
        ]);

        $this->threadId = $existingThread->id;
        $this->openaiThreadId = $existingThread->openai_thread_id;
        $this->assistant_id = $existingThread->assistant_id;
        $this->dispatch('settingsActionCompleted');
    }

    public function render()
    {
        return view('livewire.chat');
    }
}