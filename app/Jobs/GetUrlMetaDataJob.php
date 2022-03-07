<?php

namespace App\Jobs;

use App\Http\Integrations\Raindrop\Requests\ParseUrlRequest;
use App\Models\Url;
use App\Support\Job\WithUniqueUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GetUrlMetaDataJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use WithUniqueUrl;

    public function __construct(private Url $url)
    {
    }

    public function handle(): void
    {
        $parseRequest = new ParseUrlRequest($this->url);
        $parsedUrl = $parseRequest->send()->json();

        if (!data_get($parsedUrl, 'result', false)) {
            Log::error('Failed to parse the Url with Raindrop', ['url' => $this->url, 'raindrop' => $parsedUrl]);
            return;
        }

        $metaHtml = collect($parsedUrl['item'])->only(['excerpt', 'type', 'meta']);
        $title = data_get($parsedUrl, 'item.title');

        $this->url->update(['title' => $title, 'meta_html' => $metaHtml->toArray()]);
    }
}
