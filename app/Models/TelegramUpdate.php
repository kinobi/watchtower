<?php

namespace App\Models;

use GuzzleHttp\Psr7\Uri;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        return collect($this->data('message.entities', []))->contains('type', 'url');
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
            ->where('type', 'url')
            ->map(fn(array $urlEntity) => new Uri(mb_substr($text, $urlEntity['offset'], $urlEntity['length'])));
    }

    public function isCallbackQuery(): bool
    {
        return $this->data('callback_query') !== null;
    }
}
