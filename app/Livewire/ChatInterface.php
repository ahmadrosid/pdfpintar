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
    public $showShareModal = false;

    public function mount()
    {
        if (!$this->document->is_public) {
            $this->authorize('view', $this->document);
        }
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
        $this->authorize('view', $this->document);
        Message::where('thread_id', $this->threadId)->delete();
        $this->messages = [];
        $this->dispatch('settingsActionCompleted');
    }

    public function newChat() {
        $this->authorize('view', $this->document);
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
        // Check if file_id exists and document has been processed by OpenAI
        if (!$this->document->file_id) {
            return false;
        }

        // If we already have an assistant_id, the document is indexed
        if ($this->assistant_id) {
            return true;
        }

        // Check if enough time has passed for indexing (30 seconds)
        $hasPassedIndexingTime = $this->document->created_at->diffInSeconds(now()) > 30;
        
        if ($hasPassedIndexingTime) {
            try {
                // Try to create assistant to verify indexing is complete
                $assistant = $this->createAssistant();
                $this->assistant_id = $assistant->id;
                return true;
            } catch (\Throwable $e) {
                logger()->error('Document indexing check failed: ' . $e->getMessage());
                return false;
            }
        }

        return false;
    }

    #[Computed]
    public function indexing_progress()
    {
        if ($this->is_indexed) {
            return 100;
        }

        // Calculate a percentage based on time passed (30 seconds total)
        $secondsPassed = $this->document->created_at->diffInSeconds(now());
        return min(95, round(($secondsPassed / 30) * 100)); // Max 95% until confirmed indexed
    }

    public function toggleShare()
    {
        $this->authorize('manageSharing', $this->document);

        if (!$this->document->sharing_token) {
            $this->document->update([
                'sharing_token' => Str::random(32),
                'is_public' => true,
            ]);
        } else {
            $this->document->update([
                'is_public' => !$this->document->is_public
            ]);
        }
        
        $this->document->refresh();
    }

    public function copyShareLink()
    {
        if (!$this->document->is_public) {
            $this->authorize('view', $this->document);
        }
        return route('documents.public', $this->document->sharing_token);
    }

    #[Computed]
    public function shareUrl()
    {
        if ($this->document->is_public && $this->document->sharing_token) {
            return route('documents.public', $this->document->sharing_token);
        }
        return null;
    }

    public function render()
    {
        if (!$this->is_indexed) {
            return <<<HTML
            <div class="grid place-content-center h-[80vh]" wire:poll.2500ms>
                <div class="flex flex-col items-center gap-4">
                    <div class="flex gap-2 items-center">
                        <div class="h-5 w-5 border-[3px] border-dashed border-neutral-400 dark:border-neutral-300 rounded-full animate-spin"></div>
                        <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">
                            {{__('Indexing document')}} ({{ $this->indexing_progress }}%)
                        </p>
                    </div>
                    <p class="text-xs text-neutral-400 dark:text-neutral-500">
                        {{__('This may take up to 30 seconds')}}
                    </p>
                </div>
            </div>
            HTML;
        }

        return view('livewire.chat');
    }

    public function downloadAsPdf($index)
    {
        if (!$this->document->is_public) {
            $this->authorize('view', $this->document);
        }
        
        if (isset($this->messages[$index])) {
            $html = Str::markdown($this->messages[$index]['content']);
            $fileName = now()->format('Y-m-d-H-i-s') . '.pdf';
            $filePath = PdfProcessor::generatePdf($html, $fileName);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }

    public function downloadAsExcel($index)
    {
        if (!$this->document->is_public) {
            $this->authorize('view', $this->document);
        }
        
        if (isset($this->messages[$index])) {
            $fileName = now()->format('Y-m-d-H-i-s') . '.xlsx';
            $filePath = ExcelProcessor::generateExcel($this->messages[$index]['content'], $fileName);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }

    public function downloadAsWord($index)
    {
        if (!$this->document->is_public) {
            $this->authorize('view', $this->document);
        }
        
        if (isset($this->messages[$index])) {
            $fileName = now()->format('Y-m-d-H-i-s') . '.docx';
            $filePath = WordProcessor::generateWord($this->messages[$index]['content'], $fileName);
            
            return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
        }
    }
}