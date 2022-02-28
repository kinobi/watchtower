<?php

namespace App\Listeners;

use App\Events\CallbackQueryReceived;
use App\Http\Integrations\TelegramBot\Requests\AnswerCallbackQueryRequest;
use App\Http\Integrations\Txtpaper\Requests\CreateMobiDocumentRequest;
use App\Models\Url;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AnswerCallback implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(CallbackQueryReceived $event): void
    {
        $data = json_decode($event->telegramUpdate->data('callback_query.data'), true, 512, JSON_THROW_ON_ERROR);

        /** @var Url $url */
        $url = Url::find((int)$data['url']);

        $text = match ($data['action']) {
            'read' => $this->readUrl($url),
            default => 'No action',
        };

        $botRequest = new AnswerCallbackQueryRequest($event->telegramUpdate, $text);
        $botRequest->send();
    }

    public function readUrl(Url $url): string
    {
        $txtpaperRequest = new CreateMobiDocumentRequest($url->uri, config('services.txtpaper.mobi.email'));
        $txtpaperResponse = $txtpaperRequest->send();

        return $txtpaperResponse->json('status') === 'success'
            ? __('watchtower.txtpaper.success')
            : __('watchtower.txtpaper.failed');
    }
}
