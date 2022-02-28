<?php

namespace App\Http\Integrations\TelegramBot\Requests;

use App\Http\Integrations\TelegramBot\TelegramBotConnector;
use App\Models\TelegramUpdate;
use App\Models\Url;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class ReplyToAddUrlsRequest extends SaloonRequest
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

    public function __construct(
        public readonly TelegramUpdate $telegramUpdate,
        public readonly Url $url,
    ) {
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
        $chatId = (int)$this->telegramUpdate->data('message.chat.id');
        $messageId = (int)$this->telegramUpdate->data('message.message_id');
        $text = $this->url->wasRecentlyCreated ? __('watchtower.url.created') : __('watchtower.url.duplicated');

        return [
            'chat_id' => $chatId,
            'text' => $text,
            'reply_to_message_id' => $messageId,
            'allow_sending_without_reply' => true,
            'reply_markup' => [
                'inline_keyboard' => [
                    [
                        [
                            'text' => '📖',
                            'callback_data' => json_encode(
                                ['action' => 'read', 'url' => $this->url->id],
                                JSON_THROW_ON_ERROR
                            ),
                        ],
                        [
                            'text' => '🔖',
                            'callback_data' => json_encode(
                                ['action' => 'bookmark', 'url' => $this->url->id],
                                JSON_THROW_ON_ERROR
                            ),
                        ],
                        [
                            'text' => '💬',
                            'callback_data' => json_encode(
                                ['action' => 'share', 'url' => $this->url->id],
                                JSON_THROW_ON_ERROR
                            ),
                        ],
                    ]
                ]
            ]
        ];
    }
}
