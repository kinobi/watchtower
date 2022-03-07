<?php

namespace App\Listeners;

use App\Events\UrlsAdded;
use App\Jobs\CleanChatJob;
use App\Jobs\CreateUrlMessageJob;
use App\Jobs\GetUrlMetaDataJob;
use App\Models\TelegramUpdate;
use App\Models\Url;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class StoreNewUrls implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(UrlsAdded $event): void
    {
        $telegramUpdate = $event->telegramUpdate;

        /** @var Uri $uri */
        foreach ($telegramUpdate->getUris() as $uri) {
            $url = $this->getUrl($uri, $telegramUpdate);

            $jobs = [];

            if ($url->wasRecentlyCreated || !$url->title) {
                $jobs[] = new GetUrlMetaDataJob($url);
            }

            $jobs[] = new CleanChatJob($url, $telegramUpdate);
            $jobs[] = new CreateUrlMessageJob($url, $url->wasRecentlyCreated);

            Bus::chain($jobs)->dispatch();
        }
    }

    /**
     * Create or retrieve the Url
     */
    private function getUrl(Uri $uri, TelegramUpdate $telegramUpdate): Url
    {
        /** @var Url $url */
        $url = Url::firstOrCreate(
            ['host' => $uri->getHost(), 'path' => $uri->getPath(), 'query' => $uri->getQuery()],
            ['uri' => $uri, 'chat_id' => (int)$telegramUpdate->data('message.chat.id')]
        );

        $telegramUpdate->urls()->save($url);

        return $url;
    }
}
