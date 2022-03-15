<?php

namespace App\Jobs;

use App\Models\Annotation;
use App\Models\TelegramUpdate;
use App\Services\UrlMessageFormatter;
use App\Support\AnnotationTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WorkflowWriteAnnotationJob extends AbstractWorkflowTransitionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public readonly Annotation $annotation, public readonly TelegramUpdate $telegramUpdate)
    {
    }

    public function execute(UrlMessageFormatter $urlMessageFormatter): void
    {
        $this->annotation->workflow_apply(AnnotationTransition::WRITE->value);
        $this->annotation->update(['note' => $this->telegramUpdate->data('message.text')]);
    }
}
