<?php

namespace App\Http\Repository;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;

class DocumentRepository
{
    private string $chat_model = 'gpt-3.5-turbo-16k';
    // private string $chat_model = 'gpt-4';
    private string $embedding_model = 'text-embedding-ada-002';

    public function getQueryEmbedding($question): array
    {
        $result = OpenAI::embeddings()->create([
            'model' => $this->embedding_model,
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
        SELECT embeddings.collection_id, embeddings.embedding, embeddings.document, embeddings.cmetadata, embeddings.custom_id, embeddings.uuid, embeddings.embedding <=> '%s'::vector AS distance 
        FROM embeddings JOIN embedding_collections ON embeddings.collection_id = embedding_collections.uuid 
        WHERE embeddings.collection_id = '{collection_id}'::UUID ORDER BY distance ASC 
        LIMIT 4
        EOT;

        $embedding_collections = DB::table('embedding_collections')->where('name',  $document_path)->first();
        $query = str_replace("%s", json_encode($query_embedding), $query);
        $query = str_replace("{collection_id}", $embedding_collections->uuid, $query);
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

    public function askQuestion(array $messages)
    {
        return Openai::chat()->create([
            'model' => $this->chat_model,
            'temperature' => 0.8,
            'messages' => $messages,
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

        // Log::info($system_prompt);

        return Openai::chat()->createStreamed([
            'model' => $this->chat_model,
            'temperature' => 0.7,
            'messages' => [
                ['role' => 'system', 'content' => $system_prompt],
                ['role' => 'user', 'content' => $question],
            ],
        ]);
    }


    public function generateTitleConversation(string $question, string $answer)
    {
        $messages = [
            ['role' => 'user', 'content' => $question],
            ['role' => 'assistant', 'content' => $answer],
            ['role' => 'user', 'content' => 'Please generate title for this QA. Do not wrap in quotes. Please set max length to 15 words.'],
        ];

        $response = $this->askQuestion($messages);

        $title = "";
        foreach ($response->choices as $result) {
            $title = $result->message->content;
        }

        return $title;
    }
}
