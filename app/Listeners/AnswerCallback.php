<?php

namespace App\Listeners;

use App\Events\CallbackQueryReceived;
use App\Http\Integrations\TelegramBot\Requests\AnswerCallbackQueryRequest;
use App\Models\Url;
use App\Support\UrlTransition;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AnswerCallback implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CallbackQueryReceived $event): void
    {
        $data = json_decode($event->telegramUpdate->data('callback_query.data'), true, 512, JSON_THROW_ON_ERROR);
        $botRequest = new AnswerCallbackQueryRequest($event->telegramUpdate);
        Log::debug('Callback received', $data);

        /** @var Url $url */
        $url = Url::find((int)$data['url']);
        if (!$url) {
            Log::error('Callback does not match any Url', $data);
            $botRequest
                ->mergeData([
                    'text' => __('watchtower.url.unknown'),
                    'show_alert' => true,
                ])
                ->send();

            return;
        }

        $botRequest
            ->mergeData([
                'text' => match ($data['action']) {
                    UrlTransition::TO_READING->value => UrlTransition::TO_READING->answerCallback($url),
                    UrlTransition::TO_KINDLE->value => UrlTransition::TO_KINDLE->answerCallback($url),
                    UrlTransition::RESET->value => UrlTransition::RESET->answerCallback($url),
                    UrlTransition::TO_READ->value => UrlTransition::TO_READ->answerCallback($url),
                    default => $this->getFallbackAnswer($data),
                },
            ])
            ->send();
    }

    private function getFallbackAnswer(array $data): string
    {
        Log::error('Non handled action received', $data);

        return __('watchtower.fallback');
    }
}
