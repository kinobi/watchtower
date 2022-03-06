<?php

namespace App\Http\Integrations\TelegramBot\Requests;

use App\Http\Integrations\TelegramBot\TelegramBotConnector;
use App\Models\Url;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class CreateUrlMessageRequest extends SaloonRequest
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

    public function __construct(public readonly Url $url, public readonly bool $wasRecentlyCreated = true)
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

    public function defaultData(): array
    {
        $this->url->refresh();

        $text = sprintf(
            "<strong><a href=\"%s\">%s</a></strong>\n%s",
            $this->url->uri,
            $this->url->title,
            $this->wasRecentlyCreated ? __('watchtower.url.created') : __('watchtower.url.duplicated')
        );

        return [
            'chat_id' => $this->url->chat_id,
            'text' => $text,
            'parse_mode' => 'HTML',
            'reply_markup' => [
                'inline_keyboard' => $this->url->getTelegramInlineKeyboard(),
            ]
        ];
    }
}
