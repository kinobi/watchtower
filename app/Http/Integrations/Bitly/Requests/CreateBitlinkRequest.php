<?php

namespace App\Http\Integrations\Bitly\Requests;

use App\Http\Integrations\Bitly\BitlyConnector;
use App\Models\Url;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class CreateBitlinkRequest extends SaloonRequest
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
    protected ?string $connector = BitlyConnector::class;

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
        return '/v4/bitlinks';
    }

    public function defaultData(): array
    {
        return [
            'long_url' => (string)$this->url->uri,
            'title' => (string)$this->url->title,
            'tags' => __('watchtower.bitly.tags'),
        ];
    }
}
