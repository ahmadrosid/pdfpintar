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

        return Inertia::render('Documents/Chat/Show', [
            'document' => $data,
            'chat' => $chat,
            'message' => session("message")
        ]);
    }

    public function streaming(Request $request)
    {
        $question = $request->query('question');
        $chat_id = $request->query('chat_id');
        $chat = Chat::findOrFail($chat_id);

        $query_embedding =  $this->repository->getQueryEmbedding($question);
        $embedding = $this->repository->findEmbedding($chat->document->path, $query_embedding);
        return response()->stream(
            function () use ($question, $embedding, $chat) {
                $stream = $this->repository->askQuestionStreamed($embedding['context'], $question);
                $result_text = "";
                foreach ($stream as $response) {
                    $text = $response->choices[0]->delta->content;
                    if (connection_aborted()) {
                        break;
                    }
                    ServerEvent::send("update", $text);
                    $result_text .= $text;
                }

                if ($chat->document->title === $chat->title) {
                    $chat->update([
                        'title' => $this->repository->generateTitleConversation($question, $result_text),
                    ]);
                }

                ServerEvent::send("update", "<END_STREAMING_SSE>");
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
