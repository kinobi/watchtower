<?php

namespace App\Http\Integrations\TelegramBot\Requests;

use App\Http\Integrations\TelegramBot\TelegramBotConnector;
use App\Models\Url;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class UpdateUrlMessageRequest extends SaloonRequest
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

    public function __construct(public readonly Url $url, public readonly string $text)
    {
    }

    /**
     * Define the endpoint for the request.
     *
     * @return string
     */
    public function defineEndpoint(): string
    {
        return '/editMessageText';
    }

    public function defaultData(): array
    {
        return [
            'chat_id' => $this->url->chat_id,
            'message_id' => $this->url->message_id,
            'text' => $this->text,
            'parse_mode' => 'HTML',
            'reply_markup' => [
                'inline_keyboard' => $this->url->getTelegramInlineKeyboard(),
            ]
        ];
    }
}
