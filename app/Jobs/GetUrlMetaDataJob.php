<?php

namespace App\Jobs;

use App\Models\Url;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use voku\helper\HtmlDomParser;

class GetUrlMetaDataJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(private Url $url)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $item = Http::get($this->url->uri);
        $dom = HtmlDomParser::str_get_html($item->body());

        $metaHtml = collect($dom->find('meta'))
            ->filter(fn($meta) => $meta->hasAttribute('content'))
            ->mapWithKeys(fn($meta) => [Str::lower($meta->getAttribute('name')) => $meta->getAttribute('content')])
            ->only(['description', 'keywords', 'author', 'copyright']);

        $title = $dom->find('title', 0)->innertext;

        $this->url->update(['title' => $title, 'meta_html' => $metaHtml->toArray()]);
    }

    public function uniqueId(): string
    {
        return md5(Url::class . $this->url->getKey());
    }
}
