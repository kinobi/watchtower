<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\CreateUrlMessageRequest;
use App\Http\Integrations\TelegramBot\Requests\PinUrlMessageRequest;
use App\Models\Url;
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
    public function handle(): void
    {
        $botRequestReply = new CreateUrlMessageRequest($this->url, $this->wasRecentlyCreated);
        $botResponseReply = $botRequestReply->send();

        $this->url->update(['message_id' => $botResponseReply->json('result.message_id')]);

        $botRequestPin = new PinUrlMessageRequest($this->url);
        $botRequestPin->send();
    }
}
