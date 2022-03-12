<?php

namespace App\Listeners;

use App\Jobs\WorkflowAnnotateUrlJob;
use App\Models\Annotation;
use App\Models\Url;
use App\Support\AnnotationStatus;
use App\Support\AnnotationTransition;
use App\Support\UrlTransition;
use Illuminate\Events\Dispatcher;
use ZeroDaHero\LaravelWorkflow\Events\EnteredEvent;
use ZeroDaHero\LaravelWorkflow\Events\GuardEvent;

class AnnotationWorkflowSubscriber
{
    public function onAnnotationWriting(GuardEvent $event): void
    {
        /** @var Annotation $annotation */
        $annotation = $event->getSubject();

        /** @var Url $url */
        $url = $annotation->url;

        if (!$url->workflow_can(UrlTransition::ANNOTATE->value)) {
            $event->setBlocked(true, 'The Url is already annotated');
        }
    }

    public function onCreated(EnteredEvent $event): void
    {
        /** @var Annotation $annotation */
        $annotation = $event->getSubject();

        /** @var Url $url */
        $url = $annotation->url;

        WorkflowAnnotateUrlJob::dispatchSync($url);
    }

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(
            'workflow.url_workflow.guard.' . AnnotationTransition::WRITE->value,
            [__CLASS__, 'onAnnotationWriting']
        );

        $events->listen('workflow.annotation_workflow.entered.' . AnnotationStatus::CREATED->value, [__CLASS__, 'onCreated']);
    }
}
