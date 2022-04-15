<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use App\Models\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class GetUrlJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use WithUrlMessageUpdate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public readonly MessageReference $messageReference, public readonly string $payload)
    {
    }

    public function handle(): void
    {
        $url = Url::find((int)$this->payload);
        CleanChatJob::dispatch($this->messageReference);

        $this->updateUrlMessage(
            $url->refresh(),
            View::make('telegram.url_message', [
                'url' => $url,
            ])->render()
        );
    }
}
