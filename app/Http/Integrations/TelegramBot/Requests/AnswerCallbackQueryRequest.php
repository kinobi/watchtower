<?php

namespace App\Http\Integrations\TelegramBot\Requests;

use App\Http\Integrations\TelegramBot\TelegramBotConnector;
use App\Models\TelegramUpdate;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class AnswerCallbackQueryRequest extends SaloonRequest
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
        public readonly string $text,
    ) {
    }

    /**
     * Define the endpoint for the request.
     *
     * @return string
     */
    public function defineEndpoint(): string
    {
        return '/answerCallbackQuery';
    }

    public function defaultData(): array
    {
        return [
            'callback_query_id' => $this->telegramUpdate->data('callback_query.id'),
            'text' => $this->text,
            'show_alert' => true,
        ];
    }
}
