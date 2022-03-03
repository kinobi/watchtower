<?php

namespace App\Models;

use App\Support\UrlTransition;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Symfony\Component\Workflow\Transition;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

class Url extends Model
{
    use HasFactory;
    use WorkflowTrait;

    protected $attributes = [
        'status' => '{"draft":1}',
    ];

    protected $casts = [
        'status' => 'array',
    ];

    protected $fillable = [
        'message_id',
        'uri',
    ];

    public function getTelegramInlineKeyboard(): array
    {
        $buttons = collect($this->workflow_transitions())
            ->map(function (Transition $transition) {
                $transitionCode = UrlTransition::from($transition->getName());

                return [
                    'text' => $transitionCode->icon(),
                    'callback_data' => json_encode(
                        ['action' => $transitionCode->value, 'url' => $this->id],
                        JSON_THROW_ON_ERROR
                    ),
                ];
            });

        return [$buttons->toArray()];
    }

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

    public function telegramUpdate(): BelongsTo
    {
        return $this->belongsTo(TelegramUpdate::class);
    }
}