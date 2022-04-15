<?php

namespace App\Http\Integrations\TelegramBot\Requests;

use App\Http\Integrations\TelegramBot\TelegramBotConnector;
use JetBrains\PhpStorm\ArrayShape;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class SendMessageRequest extends SaloonRequest
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
    protected ?string $connector = TelegramBotConnector::class;

    public function __construct(public readonly int $chatId, public readonly string $text)
    {
    }

    /**
     * Define the endpoint for the request.
     *
     * @return string
     */
    public function defineEndpoint(): string
    {
        return '/sendMessage';
    }

    #[ArrayShape(['chat_id' => "int", 'text' => "string"])]
    public function defaultData(): array
    {
        return [
            'chat_id' => $this->chatId,
            'text' => $this->text,
            'parse_mode' => 'HTML',
        ];
    }
}
