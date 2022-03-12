<?php

namespace App\Models;

use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\ParameterBag;

class TelegramUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'payload',
    ];

    private ?ParameterBag $json;

    /**
     * Does the update has Urls
     *
     * @return bool
     * @throws \JsonException
     */
    public function hasUrl(): bool
    {
        return collect($this->data('message.entities', []))->contains(
            fn($value) => in_array($value['type'], ['url', 'text_link'], true)
        );
    }

    /**
     * Retrieve a data from the payload by key
     *
     * @param $key
     * @param $default
     * @return mixed
     * @throws \JsonException
     */
    public function data($key = null, $default = null): mixed
    {
        if (!isset($this->json)) {
            $this->json = new ParameterBag((array)json_decode($this->payload, true, 512, JSON_THROW_ON_ERROR));
        }

        if (is_null($key)) {
            return $this->json;
        }

        return data_get($this->json->all(), $key, $default);
    }

    /**
     * Get the update Uris
     *
     * @return Collection
     * @throws \JsonException
     */
    public function getUris(): Collection
    {
        $text = (string)$this->data('message.text', '');
        $entities = collect($this->data('message.entities', []));

        return $entities
            ->whereIn('type', ['url', 'text_link'])
            ->map(
                fn(array $urlEntity) => $urlEntity['type'] === 'url'
                    ? new Uri(mb_substr($text, $urlEntity['offset'], $urlEntity['length']))
                    : new Uri($urlEntity['url'])
            );
    }

    /**
     * Get the data in the callback
     *
     * @return array
     * @throws \JsonException
     */
    public function getCallbackData(): array
    {
        return json_decode($this->data('callback_query.data'), true, 512, JSON_THROW_ON_ERROR);
    }

    public function getTelegramMessageReference(): MessageReference
    {
        return new MessageReference(
            (int)$this->data('message.chat.id'),
            (int)$this->data('message.message_id')
        );
    }

    /**
     * Does the update is a callback query
     *
     * @return bool
     * @throws \JsonException
     */
    public function isCallbackQuery(): bool
    {
        return $this->data('callback_query') !== null;
    }

    /**
     * Does the update is a reply
     *
     * @return bool
     * @throws \JsonException
     */
    public function isReply(): bool
    {
        return $this->data('message.reply_to_message') !== null;
    }

    public function urls(): HasMany
    {
        return $this->hasMany(Url::class);
    }
}
