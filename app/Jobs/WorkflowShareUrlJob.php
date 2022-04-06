<?php

namespace App\Jobs;

use App\Http\Integrations\Bitly\Requests\CreateBitlinkRequest;
use App\Models\Url;
use App\Support\Jobs\WithUniqueUrl;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class WorkflowShareUrlJob extends AbstractWorkflowTransitionJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use WithUniqueUrl;
    use WithUrlMessageUpdate;

    public function __construct(public readonly Url $url)
    {
    }

    protected function execute(): void
    {
        $this->url->workflow_apply(UrlTransition::SHARE->value);

        $this->createShortUrl();

        $this->updateUrlMessage(
            $this->url,
            View::make('telegram.url_message', [
                'url' => $this->url,
                'text' => __('watchtower.url.shared'),
            ])->render()
        );
    }

    protected function createShortUrl(): void
    {
        $bitlyResponse = (new CreateBitlinkRequest($this->url))->send();
        if ($bitlyResponse->failed()) {
            Log::error(
                'Failed to create Short URL',
                ['url' => $this->url, 'bitly_response' => $bitlyResponse->json()]
            );
        }

        $this->url->shortUrl()->create([
            'provider' => 'bitly',
            'url' => $bitlyResponse->json('link'),
        ]);
    }
}
