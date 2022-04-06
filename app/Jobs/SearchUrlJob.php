<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use App\Http\Integrations\TelegramBot\Requests\SendMessageRequest;
use App\Models\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class SearchUrlJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly MessageReference $messageReference, public readonly string $payload)
    {
    }

    public function handle(): void
    {
        $search = Url::search($this->payload)->get();

        CleanChatJob::dispatch($this->messageReference);

        (new SendMessageRequest(
            $this->messageReference->chatId,
            View::make('telegram.search_results', [
                'search' => $this->payload,
                'results' => $search,
            ])->render()
        ))->send();
    }
}
