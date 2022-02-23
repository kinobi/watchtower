<?php

namespace App\Http\Integrations\Txtpaper\Requests;

use App\Http\Integrations\Txtpaper\TxtpaperConnector;
use JetBrains\PhpStorm\ArrayShape;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;

class CreateMobiDocumentRequest extends SaloonRequest
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
    protected ?string $connector = TxtpaperConnector::class;

    public function __construct(public string $url, public string $kindleEmail)
    {
    }

    /**
     * Define the endpoint for the request.
     *
     * @return string
     */
    public function defineEndpoint(): string
    {
        return '/';
    }

    #[ArrayShape(['url' => "string", 'format' => "string", 'email' => "string"])]
    public function defaultQuery(): array
    {
        return [
            'url' => $this->url,
            'format' => 'mobi',
            'email' => $this->kindleEmail,
        ];
    }
}
