<?php

namespace App\Jobs;

use App\Models\Url;
use App\Support\Jobs\WithUniqueUrl;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\View;

class WorkflowTrashNoteJob extends AbstractWorkflowTransitionJob implements ShouldQueue, ShouldBeUnique
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
        $this->url->workflow_apply(UrlTransition::TRASH_NOTE->value);

        $text = __('watchtower.url.note_trashed.failed');

        if ($this->url->annotation()->delete()) {
            $text = __('watchtower.url.note_trashed.success');
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
}
