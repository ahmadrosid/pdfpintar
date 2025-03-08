<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Thread;
use App\Models\Message;
use App\Http\ChatEvent;
use OpenAI\Laravel\Facades\OpenAI;

class ChatController extends Controller
{

    private function createAssistant(Document $document)
    {
        $assistant = OpenAI::assistants()->create([
            'name' => $document->file_name,
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
                                $document->file_id,
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

    public function stream(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'documentId' => 'present|numeric|nullable',
            'threadId' => 'nullable|numeric',
        ]);

        $document = Document::find($validated['documentId']);
        if ($document->user_id != auth()->user()->id) {
            return response('You are not authorized to view this document', 401);
        }

        $thread = Thread::find($validated['threadId']);
        if (!$thread) {
            $openaiThread = OpenAI::threads()->create([]);
            $assistant_id = $this->createAssistant($document)->id;
            $thread = Thread::create([
                'openai_thread_id' => $openaiThread->id,
                'assistant_id' => $assistant_id,
                'document_id' => $document->id,
            ]);
        }

        $text = $validated['text'];

        return response()->stream(function () use (
            $thread, $text
        ) {
            // Prevent output buffering
            if (ob_get_level()) {
                ob_end_clean();
            }

            try {
                $dbMessages = Message::where('thread_id', $thread->id)->orderBy('id', 'asc')->get();
                $messages = [];
                foreach ($dbMessages as $message) {
                    $messages[] = [
                        'role' => $message->role,
                        'content' => $message->content,
                    ];
                }

                $messages[] = [
                    'role' => 'user',
                    'content' => $text,
                ];

                $run = OpenAI::threads()->runs()->createStreamed($thread->openai_thread_id, [
                    'assistant_id' => $thread->assistant_id,
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
                            ChatEvent::token($text)->emit();
                            $message_content .= $text;
                        }
                    }
                }

                $lastMessage = end($messages);

                Message::insert([
                    [
                        'thread_id' => $thread->id,
                        'role' => 'user',
                        'content' => $lastMessage['content'],
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                    [
                        'thread_id' => $thread->id,
                        'role' => 'assistant',
                        'content' => $message_content,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                ]);
            } catch (\Throwable $e) {
                report($e);
                ChatEvent::notice('error', 'Unexpected error')->emit();
            }
        }, 200, [
            'Cache-Control' => 'no-cache',
            'Content-Type' => 'text/event-stream',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
