<?php

namespace App\Listeners;

use App\Events\UrlsAdded;
use App\Http\Integrations\TelegramBot\Requests\ReplyToAddUrlsRequest;
use App\Models\Url;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreNewUrls implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UrlsAdded $event): void
    {
        /** @var Uri $uri */
        foreach ($event->telegramUpdate->getUris() as $uri) {
            /** @var Url $url */
            $url = Url::firstOrCreate(
                ['host' => $uri->getHost(), 'path' => $uri->getPath()],
                ['uri' => $uri]
            );

            $botRequest = new ReplyToAddUrlsRequest($event->telegramUpdate, $url);
            $botRequest->send();
        }
    }
}
