<?php

namespace App\Jobs;

use App\Models\Url;
use App\Services\UrlMessageFormatter;
use App\Support\Jobs\WithUniqueUrl;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WorkflowResetUrlToDraftJob extends AbstractWorkflowTransitionJob implements ShouldQueue, ShouldBeUnique
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

    protected function execute(UrlMessageFormatter $urlMessageFormatter): void
    {
        $this->url->workflow_apply(UrlTransition::RESET->value);
        $this->url->save();

        $this->updateUrlMessage(
            $this->url,
            $urlMessageFormatter->formatHtmlMessage($this->url, __('watchtower.url.reset'))
        );
    }
}
