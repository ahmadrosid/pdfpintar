<?php

namespace App\Livewire;

use App\Lib\ExcelProcessor;
use App\Lib\PdfProcessor;
use App\Lib\WordProcessor;
use App\Models\Document;
use App\Models\Thread;
use App\Models\Message;
use Livewire\Attributes\Computed;
use Livewire\Component;
use OpenAI\Laravel\Facades\OpenAI;
use Livewire\Attributes\On;
use Throwable;
use Illuminate\Support\Str;

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
            'model' => 'gpt-4o-mini',
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
        try {
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
        } catch (Throwable $e) {
            logger()->error($e->getMessage());
            throw $e;
        }
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

    #[Computed]
    public function is_indexed()
    {
        return $this->document->created_at->diffInMinutes(now()) > 0.5;
    }

    public function downloadAsPdf($index)
    {
        if (isset($this->messages[$index])) {
            $html = Str::markdown($this->messages[$index]['content']);
            $fileName = now()->format('Y-m-d-H-i-s') . '.pdf';
            $filePath = PdfProcessor::generatePdf($html, $fileName);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }

    public function downloadAsExcel($index)
    {
        if (isset($this->messages[$index])) {
            $fileName = now()->format('Y-m-d-H-i-s') . '.xlsx';
            $filePath = ExcelProcessor::generateExcel($this->messages[$index]['content'], $fileName);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }

    public function downloadAsWord($index)
    {
        if (isset($this->messages[$index])) {
            $fileName = now()->format('Y-m-d-H-i-s') . '.docx';
            $filePath = WordProcessor::generateWord($this->messages[$index]['content'], $fileName);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }

    public function render()
    {
        if (!$this->is_indexed) {
            return <<<'HTML'
            <div class="grid place-content-center h-[80vh]" wire:poll.1000ms>
                <div class="flex gap-2 items-center">
                    <div class="h-6 w-6 border-[3px] border-dashed border-neutral-400 dark:border-neutral-300 rounded-full animate-spin"></div>
                    <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">
                        {{__('Please wait for the document to be indexed.')}}
                    </p>
                </div>
            </div>
            HTML;
        }

        return view('livewire.chat');
    }
}