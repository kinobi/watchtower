<?php

namespace App\Listeners;

use App\Jobs\PinUrlJob;
use App\Jobs\UnpinUrlJob;
use App\Models\Url;
use App\Support\UrlStatus;
use Illuminate\Events\Dispatcher;
use ZeroDaHero\LaravelWorkflow\Events\EnteredEvent;
use ZeroDaHero\LaravelWorkflow\Events\LeaveEvent;

class UrlWorkflowSubscriber
{
    public function onReading(EnteredEvent $event): void
    {
        /** @var Url $url */
        $url = $event->getSubject();

        PinUrlJob::dispatch($url);
    }

    public function onUnReading(LeaveEvent $event): void
    {
        /** @var Url $url */
        $url = $event->getSubject();

        UnpinUrlJob::dispatch($url);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen('workflow.url_workflow.entered.' . UrlStatus::READING->value, [__CLASS__, 'onReading']);
        $events->listen('workflow.url_workflow.leave.' . UrlStatus::READING->value, [__CLASS__, 'onUnReading']);
    }
}
