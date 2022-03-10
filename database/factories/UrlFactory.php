<?php

namespace Database\Factories;

use GuzzleHttp\Psr7\Uri;
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
    public function definition(): array
    {
        $uri = new Uri($this->faker->url());

        return [
            'scheme' => $uri->getScheme(),
            'user_info' => $uri->getUserInfo(),
            'host' => $uri->getHost(),
            'path' => $uri->getPath(),
            'query' => $uri->getQuery(),
            'port' => $uri->getPort(),
            'fragment' => $uri->getFragment(),
        ];
    }
}
