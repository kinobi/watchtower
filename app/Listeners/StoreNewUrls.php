<?php

namespace App\Listeners;

use App\Events\UrlsAdded;
use App\Http\Integrations\TelegramBot\Requests\ReplyToAddUrlsRequest;
use App\Models\Url;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class StoreNewUrls implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UrlsAdded $event): void
    {
        $telegramUpdate = $event->telegramUpdate;

        /** @var Uri $uri */
        foreach ($telegramUpdate->getUris() as $uri) {
            /** @var Url $url */
            $url = Url::firstOrCreate(
                ['host' => $uri->getHost(), 'path' => $uri->getPath()],
                ['uri' => $uri]
            );

            $telegramUpdate->urls()->save($url);

            $botRequest = new ReplyToAddUrlsRequest($telegramUpdate, $url);
            $botResponse = $botRequest->send();

            $url->update(['message_id' => $botResponse->json('result.message_id')]);
        }
    }
}
