<?php

namespace App\Http\Integrations\TelegramBot;

use App\Http\Integrations\TelegramBot\Requests\AnswerCallbackQueryRequest;
use App\Http\Integrations\TelegramBot\Requests\CreateAnnotationMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\DeleteMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\PinUrlMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\CreateUrlMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\SendMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\UnpinUrlMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\UpdateUrlMessageRequest;
use Sammyjo20\Saloon\Http\SaloonConnector;
use Sammyjo20\Saloon\Traits\Features\AcceptsJson;

class TelegramBotConnector extends SaloonConnector
{
    use AcceptsJson;

    /**
     * Register Saloon requests that will become methods on the connector.
     * For example, GetUserRequest would become $this->getUserRequest(...$args)
     *
     * @var array
     */
    protected array $requests = [
        AnswerCallbackQueryRequest::class,
        CreateAnnotationMessageRequest::class,
        CreateUrlMessageRequest::class,
        DeleteMessageRequest::class,
        PinUrlMessageRequest::class,
        SendMessageRequest::class,
        UnpinUrlMessageRequest::class,
    ];

    /**
     * Define the base url of the api.
     *
     * @return string
     */
    public function defineBaseUrl(): string
    {
        return 'https://api.telegram.org/bot' . config('services.telegram.bot.token');
    }

    /**
     * Define the base headers that will be applied in every request.
     *
     * @return string[]
     */
    public function defaultHeaders(): array
    {
        return [];
    }
}
