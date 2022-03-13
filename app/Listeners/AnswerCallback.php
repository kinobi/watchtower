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
        $telegramUpdate = $event->telegramUpdate;
        $callbackData = $telegramUpdate->getCallbackData();

        $botRequest = new AnswerCallbackQueryRequest($telegramUpdate);

        Log::debug('Callback received', $callbackData);

        /** @var Url $url */
        $url = Url::find((int)$callbackData['url']);
        if (!$url) {
            Log::error('Callback does not match any Url', $callbackData);
            $botRequest
                ->mergeData(['text' => __('watchtower.url.unknown'), 'show_alert' => true])
                ->send();

            return;
        }

        $botRequest
            ->mergeData(['text' => $this->getCallbackAnswer($callbackData, $url)])
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
}
