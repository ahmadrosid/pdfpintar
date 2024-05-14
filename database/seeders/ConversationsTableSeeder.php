<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Conversation;

class ConversationsTableSeeder extends Seeder
{
    public function run()
    {
        Conversation::factory()->count(50)->create([
            'user_id' => 1,
        ]);
    }
}