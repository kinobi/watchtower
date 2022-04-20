<?php

namespace App\Http\Integrations\TelegramBot\Requests;

use App\Http\Integrations\TelegramBot\TelegramBotConnector;
use App\Models\Url;
use Sammyjo20\Saloon\Constants\Saloon;
use Sammyjo20\Saloon\Http\SaloonRequest;
use Sammyjo20\Saloon\Traits\Features\HasJsonBody;

class CreateAnnotationMessageRequest extends SaloonRequest
{
    use HasJsonBody;

    /**
     * Define the method that the request will use.
     *
     * @var string|null
     */
    protected ?string $method = Saloon::POST;

    public function __construct(public readonly Url $url)
    {
    }

    /**
     * The connector.
     *
     * @var string|null
     */
    protected ?string $connector = TelegramBotConnector::class;

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
        return [
            'chat_id' => $this->url->chat_id,
            'text' => __('watchtower.annotation.create', ['title' => $this->url->title]),
            'disable_web_page_preview' => true,
            'protect_content' => true,
            'reply_markup' => [
                'force_reply' => true,
                'input_field_placeholder' => $this->getPreviousNote(),
            ],
        ];
    }

    protected function getPreviousNote(): string
    {
        $note = $this->url->annotation()->withTrashed()->latest()->first()?->note ?? '';

        return mb_substr($note, 0, 64);
    }
}
