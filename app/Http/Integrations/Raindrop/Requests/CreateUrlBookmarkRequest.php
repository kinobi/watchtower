<?php

namespace App\Http\Integrations\Raindrop\Requests;

use App\Http\Integrations\Raindrop\RaindropConnector;
use App\Models\Url;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class CreateUrlBookmarkRequest extends SaloonRequest
{
    use HasJsonBody;

    /**
     * Define the method that the request will use.
     *
     * @var string|null
     */
    protected ?string $method = Saloon::POST;

    /**
     * The connector.
     *
     * @var string|null
     */
    protected ?string $connector = RaindropConnector::class;

    public function __construct(public readonly Url $url)
    {
    }

    /**
     * Define the endpoint for the request.
     *
     * @return string
     */
    public function defineEndpoint(): string
    {
        return '/raindrop';
    }

    public function defaultData(): array
    {
        return [
            'link' => (string)$this->url->uri,
            'title' => (string)$this->url->title,
            'tags' => __('watchtower.raindrop.tags'),
            'pleaseParse' => (object)[],
        ];
    }
}
