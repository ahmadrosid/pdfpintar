<?php

namespace App\Support;

use OpenAI\Laravel\Facades\OpenAI;

class TitleGenerator
{
    public static function generate(array $messages): string
    {
        $summaryMessages = collect($messages)->map(function ($message) {
            return "from: {$message['role']}\n\n{$message['content']}\n\n";
        })->join("\n\n");
        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'stop' => [
                '</title>',
            ],
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You will be given a chat conversation. Your task is to generate a short and concise title that summarizes the main topic or theme of the conversation.

Guidelines for creating the title:
1. Keep the title brief, ideally 3-7 words.
2. Capture the main topic or theme of the conversation.
3. Use clear and simple language.
4. Avoid using specific names unless they are crucial to the main topic.
5. Do not include unnecessary details or tangential information.

Generate the title and output it within <title></title> tags. Do not include any explanation or additional text outside the tags.',
                ],
                [
                    'role' => 'user',
                    'content' => "Please generate a title for the following conversation:\n\n$summaryMessages",
                ],
                [
                    'role' => 'assistant',
                    'content' => '<title>',
                ]
            ],
        ]);

        logger()->info('response', [$response->choices]);

        $title = $response->choices[0]->message->content;
        // if title starts with <title>, remove it
        if (str_starts_with($title, '<title>')) {
            $title = substr($title, 7);
        }

        return $title;
    }
}
