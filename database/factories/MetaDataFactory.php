<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MetaData>
 */
class MetaDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'provider' => 'raindrop',
            'meta' => [
                'type' => 'article',
                'tags' => [
                    'youtube',
                    'laracasts',
                ],
                'excerpt' => $this->faker->paragraph(),
            ],
        ];
    }
}
