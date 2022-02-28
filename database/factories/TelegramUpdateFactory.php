<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TelegramUpdate>
 */
class TelegramUpdateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'payload' => file_get_contents(__DIR__ . '/../../tests/Fixtures/Telegram/webhook-text-with-url.json'),
        ];
    }
}
