<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Document;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;
use Inertia\Inertia;
use Illuminate\Support\Facades\Schema;

class DocumentChatController extends Controller
{
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

    public function update(Request $request, Chat $chat)
    {
        if (!Schema::hasTable('langchain_pg_collection')) {
            return abort(404);
        }

        $question = $request->get('question');
        $document = $chat->document;
        $langchain_pg_collection = DB::table('langchain_pg_collection')->where('name',  $document->path)->first();

        $result = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $question,
        ]);

        $query_embedding =  $result['data'][0]['embedding'];

        $query = <<<EOT
        SELECT langchain_pg_embedding.collection_id, langchain_pg_embedding.embedding, langchain_pg_embedding.document, langchain_pg_embedding.cmetadata, langchain_pg_embedding.custom_id, langchain_pg_embedding.uuid, langchain_pg_embedding.embedding <=> '%s'::vector AS distance 
        FROM langchain_pg_embedding JOIN langchain_pg_collection ON langchain_pg_embedding.collection_id = langchain_pg_collection.uuid 
        WHERE langchain_pg_embedding.collection_id = '{collection_id}'::UUID ORDER BY distance ASC 
        LIMIT 4
        EOT;
        $query = str_replace("%s", json_encode($query_embedding), $query);
        $query = str_replace("{collection_id}", $langchain_pg_collection->uuid, $query);
        $records = DB::cursor($query);

        $context = "";
        $metadata = [];

        foreach ($records as $record) {
            $context .= $record->document;
            $meta = json_decode($record->cmetadata);
            $metadata[] = ['page' => $meta->page];
        }

        $system_template = <<<EOT
        Use the following pieces of context to answer the users question. 
        If you don't know the answer, just say that you don't know, don't try to make up an answer.
        ----------------
        {context}
        EOT;

        $system_prompt = str_replace("{context}", $context, $system_template);
        $response = Openai::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.7,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $question],
            ],
        ]);

        return back()->with("message", [
            "content" => $response->choices[0]->message->content,
            "role" => "bot",
            "metadata" => $metadata
        ]);
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