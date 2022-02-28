<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\UpdateUrlMessageRequest;
use App\Models\Url;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ReadUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Url $url)
    {
    }

    public function handle(): void
    {
//        $txtpaperRequest = new CreateMobiDocumentRequest($this->url->uri, config('services.txtpaper.mobi.email'));
//        $txtpaperResponse = $txtpaperRequest->send();
//
//        Log::debug($txtpaperResponse->body());
//
//        $success = $txtpaperResponse->json('status') === 'success';
//
//        $text = $success
//            ? __('watchtower.txtpaper.success')
//            : __('watchtower.txtpaper.failed');

        $this->url->workflow_apply(UrlTransition::READ->value);
        $this->url->save();

        $botRequest = new UpdateUrlMessageRequest($this->url, __('watchtower.url.read'));
        $botResponse = $botRequest->send();

        Log::debug($botResponse->body(), $this->url->getTelegramInlineKeyboard());
    }
}
