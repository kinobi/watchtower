<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\UpdateUrlMessageRequest;
use App\Models\Url;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ReadingUrlJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public readonly Url $url)
    {
    }

    public function handle(): void
    {
        $this->url->workflow_apply(UrlTransition::TO_READING->value);
        $this->url->save();

        $botRequest = new UpdateUrlMessageRequest($this->url, __('watchtower.url.read'));
        $botRequest->send();
    }
}
