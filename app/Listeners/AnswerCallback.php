<?php

namespace App\Listeners;

use App\Events\CallbackQueryReceived;
use App\Http\Integrations\TelegramBot\Requests\AnswerCallbackQueryRequest;
use App\Models\Url;
use App\Support\UrlTransition;
use Error;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class AnswerCallback implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CallbackQueryReceived $event): void
    {
        $botRequest = new AnswerCallbackQueryRequest($event->telegramUpdate);
        $data = $this->getCallbackData($event);

        Log::debug('Callback received', $data);

        /** @var Url $url */
        $url = Url::find((int)$data['url']);
        if (!$url) {
            Log::error('Callback does not match any Url', $data);
            $botRequest
                ->mergeData(['text' => __('watchtower.url.unknown'), 'show_alert' => true,])
                ->send();

            return;
        }

        $botRequest
            ->mergeData(['text' => $this->getCallbackAnswer($data, $url),])
            ->send();
    }

    private function getCallbackAnswer(array $data, Url $url): string
    {
        try {
            return UrlTransition::from($data['action'])->answerCallback($url);
        } catch (Error) {
            return $this->getCallbackFallbackAnswer($data);
        }
    }

    private function getCallbackFallbackAnswer(array $data): string
    {
        Log::error('Non handled action received', $data);
        return __('watchtower.fallback');
    }

    private function getCallbackData(CallbackQueryReceived $event): mixed
    {
        return json_decode($event->telegramUpdate->data('callback_query.data'), true, 512, JSON_THROW_ON_ERROR);
    }
}
