<?php

namespace App\Models;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Url extends Model
{
    use HasFactory;

    protected $fillable = [
        'uri',
    ];

    public function uri(): Attribute
    {
        return Attribute::make(
            get: fn($value, $attributes) => Uri::fromParts([
                'scheme' => $attributes['scheme'],
                'user' => $attributes['user_info'],
                'host' => $attributes['host'],
                'port' => $attributes['port'],
                'path' => $attributes['path'],
                'query' => $attributes['query'],
                'fragment' => $attributes['fragment'],
            ]),
            set: fn(Uri $uri) => [
                'scheme' => $uri->getScheme(),
                'user_info' => $uri->getUserInfo(),
                'host' => $uri->getHost(),
                'port' => $uri->getPort(),
                'path' => $uri->getPath(),
                'query' => $uri->getQuery(),
                'fragment' => $uri->getFragment(),
            ]
        );
    }
}
