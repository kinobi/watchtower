<?php

namespace App\Jobs;

use App\Http\Integrations\Raindrop\Requests\CheckUrlBookmarkedRequest;
use App\Http\Integrations\Raindrop\Requests\CreateUrlBookmarkRequest;
use App\Http\Integrations\TelegramBot\Requests\UpdateUrlMessageRequest;
use App\Models\Url;
use App\Support\Job\WithUniqueUrl;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;

class WorkflowBookmarkUrlJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use WithUniqueUrl;

    public function __construct(public readonly Url $url)
    {
    }

    public function handle(): void
    {
        try {
            $this->url->workflow_apply(UrlTransition::BOOKMARK->value);

            $text = __('watchtower.raindrop.failed');

            if ($this->isAlreadyBookmarked() || $this->createBookmark()) {
                $text = __('watchtower.raindrop.success');
                $this->url->save();
            }

            (new UpdateUrlMessageRequest($this->url, $text))->send();
        } catch (NotEnabledTransitionException $e) {
            Log::error($e->getMessage(), ['url' => $this->url]);
        }
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