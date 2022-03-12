<?php

namespace App\Listeners;

use App\Events\UrlsAdded;
use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use App\Jobs\CleanChatJob;
use App\Jobs\CreateUrlMessageJob;
use App\Jobs\GetUrlMetaDataJob;
use App\Models\TelegramUpdate;
use App\Models\Url;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\PendingChain;
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

            $this->prepareUrlJobChain($url, $telegramUpdate)
                ->dispatch();
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

    /**
     * Prepare the jobs chain for the new Url
     *
     * @param Url $url
     * @param TelegramUpdate $telegramUpdate
     * @return PendingChain
     */
    protected function prepareUrlJobChain(Url $url, TelegramUpdate $telegramUpdate): PendingChain
    {
        $jobs = [
            new CleanChatJob(...$this->getMessageToClean($url, $telegramUpdate)),
            new CreateUrlMessageJob($url, $url->wasRecentlyCreated),
        ];

        if ($url->wasRecentlyCreated || !$url->title) {
            array_unshift($jobs, new GetUrlMetaDataJob($url));
        }

        return Bus::chain($jobs);
    }

    /**
     * Create the list of Messages to delete from the Chat
     *
     * @param Url $url
     * @param TelegramUpdate $telegramUpdate
     * @return MessageReference[]
     */
    protected function getMessageToClean(Url $url, TelegramUpdate $telegramUpdate): array
    {
        $toClean = [$telegramUpdate->getTelegramMessageReference()];

        if (!$url->wasRecentlyCreated && $url->message_id) {
            $toClean[] = $url->getTelegramMessageReference();
        }

        return $toClean;
    }
}
