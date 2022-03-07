<?php

namespace App\Http\Integrations\Raindrop\Requests;

use App\Http\Integrations\Raindrop\RaindropConnector;
use App\Models\Url;
use Illuminate\Support\Collection;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class CheckUrlBookmarkedRequest extends SaloonRequest
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

    public readonly Collection $urlList;

    public function __construct(Url ...$urlList)
    {
        $this->urlList = new Collection($urlList);
    }

    /**
     * Define the endpoint for the request.
     *
     * @return string
     */
    public function defineEndpoint(): string
    {
        return '/import/url/exists';
    }

    public function defaultData(): array
    {
        return ['urls' => $this->urlList->map(fn(Url $url) => (string)$url->uri)->toArray()];
    }
}
