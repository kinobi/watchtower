<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\DeleteMessageRequest;
use App\Models\TelegramUpdate;
use App\Models\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanChatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Url $url, private TelegramUpdate $telegramUpdate)
    {
    }

    /**
     * Keep only one WatchTower keyboard for this Url
     */
    public function handle(): void
    {
        (new DeleteMessageRequest(
            (int)$this->telegramUpdate->data('message.chat.id'),
            (int)$this->telegramUpdate->data('message.message_id')
        ))->send();

        if ($this->url->wasRecentlyCreated || !$this->url->message_id) {
            return;
        }

        (new DeleteMessageRequest($this->url->chat_id, $this->url->message_id))->send();
    }
}
