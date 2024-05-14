<?php

namespace Database\Factories;

use App\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition()
    {
        return [
            'title' => $this->faker->sentence(),
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'document_id' => function () {
                return \App\Models\Document::factory()->create()->id;
            },
        ];
    }
}