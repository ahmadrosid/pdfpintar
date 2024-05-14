<?php

namespace Database\Factories;

use App\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition()
    {
        return [
            'role' => $this->faker->randomElement(['user', 'assistant']),
            'content' => $this->faker->paragraph,
            'conversation_id' => function () {
                return factory(\App\Models\Conversation::class)->create()->id;
            },
            'created_at' => $this->faker->dateTime,
            'updated_at' => $this->faker->dateTime,
        ];
    }
}