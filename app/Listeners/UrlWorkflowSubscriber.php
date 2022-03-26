<?php

namespace App\Listeners;

use App\Jobs\UnpinUrlJob;
use App\Models\Url;
use App\Support\UrlStatus;
use App\Support\UrlTransition;
use Illuminate\Events\Dispatcher;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;
use ZeroDaHero\LaravelWorkflow\Events\LeaveEvent;

class UrlWorkflowSubscriber
{
    public function onKindleSending(GuardEvent $event): void
    {
        /** @var Url $url */
        $url = $event->getSubject();

        $type = $url->metaData?->meta['type'] ?? null;
        if (in_array($type, ['video', 'image', 'audio'], true)) {
            $event->setBlocked(true, sprintf('Cannot send "%s" to Kindle', $type));
        }
    }

    public function onUnReading(LeaveEvent $event): void
    {
        /** @var Url $url */
        $url = $event->getSubject();

        UnpinUrlJob::dispatch($url);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'workflow.url_workflow.guard.' . UrlTransition::TO_KINDLE->value,
            [__CLASS__, 'onKindleSending']
        );

        $events->listen('workflow.url_workflow.leave.' . UrlStatus::READING->value, [__CLASS__, 'onUnReading']);
    }
}
