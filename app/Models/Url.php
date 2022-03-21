<?php

namespace App\Models;

use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use App\Support\UrlTransition;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Symfony\Component\Workflow\Transition;
use ZeroDaHero\LaravelWorkflow\Traits\WorkflowTrait;

class Url extends Model
{
    use HasFactory;
    use WorkflowTrait;

    protected $casts = [
        'meta_html' => 'array',
        'read_at' => 'immutable_datetime',
        'status' => 'array',
    ];

    protected $fillable = [
        'chat_id',
        'meta_html',
        'message_id',
        'read_at',
        'title',
        'uri',
    ];

    public function getTelegramMessageReference(): MessageReference
    {
        return new MessageReference($this->chat_id, $this->message_id);
    }

    public function getTelegramInlineKeyboard(int $rowLength = 2): array
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

        return $buttons->chunk($rowLength)->map(fn($row) => $row->values())->toArray();
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

    public function annotation(): HasOne
    {
        return $this->hasOne(Annotation::class);
    }

    public function telegramUpdate(): BelongsTo
    {
        return $this->belongsTo(TelegramUpdate::class);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status->draft', 1);
    }

    public function scopeRead(Builder $query): Builder
    {
        return $query->where('status->read', 1);
    }

    public function scopeReading(Builder $query): Builder
    {
        return $query->where('status->reading', 1);
    }
}
