<?php

namespace App\Http\Integrations\Raindrop\Requests;

use App\Http\Integrations\Raindrop\RaindropConnector;
use App\Models\Url;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class ParseUrlRequest extends SaloonRequest
{
    /**
     * Define the method that the request will use.
     *
     * @var string|null
     */
    protected ?string $method = Saloon::GET;

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
        return '/import/url/parse';
    }

    public function defaultQuery(): array
    {
        return [
            'url' => (string)$this->url->uri,
        ];
    }
}
