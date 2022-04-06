<?php

namespace App\Jobs;

use App\Http\Integrations\Raindrop\Requests\CheckUrlBookmarkedRequest;
use App\Http\Integrations\Raindrop\Requests\CreateUrlBookmarkRequest;
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

class WorkflowBookmarkUrlJob extends AbstractWorkflowTransitionJob implements ShouldQueue, ShouldBeUnique
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
        $this->url->workflow_apply(UrlTransition::BOOKMARK->value);

        $text = __('watchtower.raindrop.failed');

        if ($this->isAlreadyBookmarked() || $this->createBookmark()) {
            $text = __('watchtower.raindrop.success');
            $this->url->save();
        }

        $this->updateUrlMessage(
            $this->url->refresh(),
            View::make('telegram.url_message', [
                'url' => $this->url,
                'text' => $text,
            ])->render()
        );
    }


    private function isAlreadyBookmarked(): bool
    {
        return (bool)(new CheckUrlBookmarkedRequest($this->url))->send()->json('result', false);
    }

    private function createBookmark(): bool
    {
        $raindropResponse = (new CreateUrlBookmarkRequest($this->url))->send();
        if ($raindropResponse->json('result', false) === true) {
            return true;
        }

        Log::error(
            'Failed to create Bookmark',
            ['url' => $this->url, 'raindrop_response' => $raindropResponse->json()]
        );

        return false;
    }
}
