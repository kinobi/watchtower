<?php

namespace App\Http\Integrations\Raindrop;

use App\Http\Integrations\Raindrop\Requests\CheckUrlBookmarkedRequest;
use App\Http\Integrations\Raindrop\Requests\CreateUrlBookmarkRequest;
use App\Http\Integrations\Raindrop\Requests\ParseUrlRequest;
use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\Features\AcceptsJson;

class RaindropConnector extends SaloonConnector
{
    use AcceptsJson;

    /**
     * Register Saloon requests that will become methods on the connector.
     * For example, GetUserRequest would become $this->getUserRequest(...$args)
     *
     * @var array
     */
    protected array $requests = [
        CheckUrlBookmarkedRequest::class,
        CreateUrlBookmarkRequest::class,
        ParseUrlRequest::class,
    ];

    /**
     * Define the base url of the api.
     *
     * @return string
     */
    public function defineBaseUrl(): string
    {
        return 'https://api.raindrop.io/rest/v1';
    }

    /**
     * Define the base headers that will be applied in every request.
     *
     * @return string[]
     */
    public function defaultHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . config('services.raindrop.token'),
        ];
    }
}
