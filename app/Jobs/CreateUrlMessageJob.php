<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\CreateUrlMessageRequest;
use App\Models\Url;
use App\Services\UrlMessageFormatter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateUrlMessageJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Url $url, private bool $wasRecentlyCreated = true)
    {
    }

    /**
     * Create the WatchTower keyboard for this Url
     */
    public function handle(UrlMessageFormatter $urlMessageFormatter): void
    {
        $text = $this->wasRecentlyCreated ? __('watchtower.url.created') : __('watchtower.url.duplicated');
        $text .= PHP_EOL . __('watchtower.url.stats.queue', [
                'count_draft' => Url::draft()->count(),
                'count_reading' => Url::reading()->count(),
                'count_read' => Url::read()->count(),
            ]);

        $botResponseReply = (new CreateUrlMessageRequest(
            $this->url->refresh(),
            $urlMessageFormatter->formatHtmlMessage($this->url, $text)
        ))->send();

        $this->url->update(['message_id' => $botResponseReply->json('result.message_id')]);
    }
}
