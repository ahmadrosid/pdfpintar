<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ServerEvent;
use App\Http\Repository\DocumentRepository;
use App\Models\Chat;
use App\Models\Document;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ChatController extends Controller
{
    private DocumentRepository $repository;

    public function __construct(DocumentRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $chats = Chat::query()->where('user_id', $request->user()->id)->get()->map(function ($chat) {
            return [
                'id' => $chat->id,
                'title' => $chat->title,
                'created_at' => $chat->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('Documents/Chat/Index', [
            'chats' => $chats,
        ]);
    }

    public function show(Chat $chat)
    {
        $document = $chat->document;

        $data = [
            'id' => $document['id'],
            'path' => str_replace("public", "storage", asset($document['path'])),
            'title' => $document['title'],
        ];

        $messages = Message::query()->where('chat_id', $chat->id)->orderBy("created_at", "asc")->get()->map(function ($message) {
            return [
                'id' => $message->id,
                'chat_id' => $message->chat_id,
                'content' => $message->content,
                'role' => $message->role,
                'metadata' => $message->metadata,
                'created_at' => $message->created_at->diffForHumans(),
            ];
        });

        return Inertia::render('Documents/Chat/Show', [
            'document' => $data,
            'chat' => $chat,
            'message' => $messages
        ]);
    }

    public function streaming(Request $request)
    {
        $question = $request->query('question');
        $chat_id = $request->query('chat_id');
        $chat = Chat::findOrFail($chat_id);

        Message::create([
            'chat_id' => $chat->id,
            'metadata' => "",
            'content' => $question,
            'role' => 'user',
        ]);

        $query_embedding =  $this->repository->getQueryEmbedding($question);
        $embedding = $this->repository->findEmbedding($chat->document->path, $query_embedding);

        return response()->stream(
            function () use ($question, $embedding, $chat) {
                $stream = $this->repository->askQuestionStreamed($embedding['context'], $question);
                $result_text = "";
                $metadata = [
                    'user_id' => $chat->user_id,
                    'document_id' => $chat->document_id,
                    'page' => $embedding['metadata'],
                ];
                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content;
                    if (connection_aborted()) {
                        break;
                    }
                    $data = [
                        'chat_id' => $chat->id,
                        'user_id' => $chat->user_id,
                        'text' => $text,
                        'metadata' => $metadata,
                    ];
                    ServerEvent::send("update", json_encode($data));
                    ob_flush();
                    flush();
                    $result_text .= $text;
                }

                if ($chat->document->title === $chat->title) {
                    $chat->update([
                        'title' => $this->repository->generateTitleConversation($question, $result_text),
                    ]);
                }

                ServerEvent::send("update", "<END_STREAMING_SSE>");
                Message::create([
                    'chat_id' => $chat->id,
                    'metadata' => json_encode($metadata),
                    'content' => $result_text,
                    'role' => 'assistant',
                ]);
            },
            200,
            [
                'Cache-Control' => 'no-cache',
                'Connection' => 'keep-alive',
                'X-Accel-Buffering' => 'no',
                'Content-Type' => 'text/event-stream',
            ]
        );
    }

    public function create(Request $request, Document $document)
    {
        $chat = Chat::create([
            'user_id' => $request->user()->id,
            'document_id' => $document->id,
            'title' => $document->title,
        ]);

        return redirect(route('document.chat', $chat->id));
    }

    public function destroy(Chat $chat)
    {
        Message::query()->where('chat_id', $chat->id)->delete();
        $chat->delete();
        return back()->with("status", "Chat {$chat->title} deleted!");
    }
}
