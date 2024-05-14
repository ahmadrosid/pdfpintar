<?php

namespace Database\Factories;

use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class DocumentFactory extends Factory
{
    protected $model = Document::class;

    public function definition()
    {
        return [
            'file_id' => $this->faker->unique()->uuid,
            'file_name' => $this->faker->file($sourceDir = '/tmp', $category = null, $fullPath = true),
            'file_size' => $this->faker->randomNumber(6),
            'user_id' => function () {
                return factory(App\Models\User::class)->create()->id;
            },
        ];
    }
}