<?php

namespace App\Listeners;

use App\Events\CallbackQueryReceived;
use App\Http\Integrations\TelegramBot\Requests\AnswerCallbackQueryRequest;
use App\Jobs\ReadingUrlJob;
use App\Jobs\SendUrlToKindleJob;
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
        $botRequest->send();

        /** @var Url $url */
        $url = Url::find((int)$data['url']);
        if (!$url) {
            return;
        }

        match ($data['action']) {
            UrlTransition::TO_READING->value => ReadingUrlJob::dispatch($url),
            UrlTransition::TO_KINDLE->value => SendUrlToKindleJob::dispatch($url),
            default => Log::error('Non handled action received', $data),
        };
    }
}
