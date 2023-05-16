<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class DocumentSearchController extends Controller
{
    public function __invoke()
    {
        $question = 'What is Makefile?';
        $result = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $question,
        ]);

        $query_embedding =  $result['data'][0]['embedding'];

        $query = <<<EOT
        SELECT langchain_pg_embedding.collection_id, langchain_pg_embedding.embedding, langchain_pg_embedding.document, langchain_pg_embedding.cmetadata, langchain_pg_embedding.custom_id, langchain_pg_embedding.uuid, langchain_pg_embedding.embedding <=> '%s'::vector AS distance 
        FROM langchain_pg_embedding JOIN langchain_pg_collection ON langchain_pg_embedding.collection_id = langchain_pg_collection.uuid 
        WHERE langchain_pg_embedding.collection_id = '534c0b78-732a-4873-ae44-f6bd72361acb'::UUID ORDER BY distance ASC 
        LIMIT 4
        EOT;
        $query = str_replace("%s", json_encode($query_embedding), $query);
        $records = DB::cursor($query);

        $context = "";
        $metadata = [];

        foreach ($records as $record) {
            $context .= $record->document . "\n";
            $metadata[] = json_decode($record->cmetadata);
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
            'temperature' => 0.8,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $question],
            ],
        ]);

        return [
            "chat" => $response->toArray(),
            "metadata" => $metadata
        ];
    }
}
