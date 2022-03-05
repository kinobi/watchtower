<?php

namespace App\Listeners;

use App\Events\UrlsAdded;
use App\Http\Integrations\TelegramBot\Requests\DeleteUrlMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\ReplyToAddUrlsRequest;
use App\Models\TelegramUpdate;
use App\Models\Url;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreNewUrls implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UrlsAdded $event): void
    {
        $telegramUpdate = $event->telegramUpdate;

        /** @var Uri $uri */
        foreach ($telegramUpdate->getUris() as $uri) {
            $url = $this->getUrl($uri, $telegramUpdate);

            $this->cleanChat($url, $telegramUpdate);

            $this->createUrlMessage($telegramUpdate, $url);
        }
    }

    /**
     * Create or retrieve the Url
     */
    private function getUrl(Uri $uri, TelegramUpdate $telegramUpdate): Url
    {
        /** @var Url $url */
        $url = Url::firstOrCreate(
            ['host' => $uri->getHost(), 'path' => $uri->getPath()],
            ['uri' => $uri]
        );

        $telegramUpdate->urls()->save($url);

        return $url;
    }

    /**
     * Keep only one WatchTower keyboard for this Url
     */
    private function cleanChat(Url $url, TelegramUpdate $telegramUpdate): void
    {
        if ($url->wasRecentlyCreated || !$url->message_id) {
            return;
        }

        $botRequestDelete = new DeleteUrlMessageRequest($telegramUpdate, $url);
        $botRequestDelete->send();
    }

    /**
     * Create the WatchTower keyboard for this Url
     */
    private function createUrlMessage(TelegramUpdate $telegramUpdate, Url $url): void
    {
        $botRequestReply = new ReplyToAddUrlsRequest($telegramUpdate, $url);
        $botResponseReply = $botRequestReply->send();

        $url->update(['message_id' => $botResponseReply->json('result.message_id')]);
    }
}
