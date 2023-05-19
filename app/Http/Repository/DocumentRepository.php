<?php

namespace App\Http\Repository;

use Illuminate\Support\Facades\DB;
use OpenAI\Laravel\Facades\OpenAI;

class DocumentRepository
{
    public function getQueryEmbedding($question): array
    {
        $result = OpenAI::embeddings()->create([
            'model' => 'text-embedding-ada-002',
            'input' => $question,
        ]);

        if (count($result['data']) == 0) {
            return [];
        }

        return $result['data'][0]['embedding'];
    }

    public function findEmbedding($document_path, $query_embedding): array
    {
        $query = <<<EOT
        SELECT langchain_pg_embedding.collection_id, langchain_pg_embedding.embedding, langchain_pg_embedding.document, langchain_pg_embedding.cmetadata, langchain_pg_embedding.custom_id, langchain_pg_embedding.uuid, langchain_pg_embedding.embedding <=> '%s'::vector AS distance 
        FROM langchain_pg_embedding JOIN langchain_pg_collection ON langchain_pg_embedding.collection_id = langchain_pg_collection.uuid 
        WHERE langchain_pg_embedding.collection_id = '{collection_id}'::UUID ORDER BY distance ASC 
        LIMIT 4
        EOT;

        $langchain_pg_collection = DB::table('langchain_pg_collection')->where('name',  $document_path)->first();
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

        return ['context' => $context, 'metadata' => $metadata];
    }

    public function askQuestion($context, $question)
    {
        $system_template = <<<EOT
        Use the following pieces of context to answer the users question. 
        If you don't know the answer, just say that you don't know, don't try to make up an answer.
        ----------------
        {context}
        EOT;
        $system_prompt = str_replace("{context}", $context, $system_template);

        return Openai::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.7,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $question],
            ],
        ]);
    }

    public function askQuestionStreamed($context, $question)
    {
        $system_template = <<<EOT
        Use the following pieces of context to answer the users question. 
        If you don't know the answer, just say that you don't know, don't try to make up an answer.
        ----------------
        {context}
        EOT;
        $system_prompt = str_replace("{context}", $context, $system_template);

        return Openai::chat()->createStreamed([
            'model' => 'gpt-3.5-turbo',
            'temperature' => 0.7,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $question],
            ],
        ]);
    }
}
