<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Url>
 */
class UrlFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'scheme' => 'https',
            'user_info' => '',
            'host' => 'example.com',
            'path' => '/',
            'query' => '',
            'fragment' => '',
        ];
    }
}
