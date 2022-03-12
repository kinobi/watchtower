<?php

namespace App\Observers;

use App\Models\Annotation;
use App\Models\Url;
use App\Support\UrlStatus;
use Symfony\Component\Workflow\Workflow;

class AnnotationObserver
{
    public function creating(Annotation $annotation): void
    {
        /** @var Workflow $workflow */
        $workflow = $annotation->workflow_get();
        $initialPlaces = $workflow->getDefinition()->getInitialPlaces();
        foreach ($initialPlaces as $initialPlace) {
            $workflow->getMarking($annotation)->mark($initialPlace);
        }
    }

    /**
     * Prevent deletion of a shared note
     *
     * @param Annotation $annotation
     * @return bool
     */
    public function deleting(Annotation $annotation): bool
    {
        /** @var Url $url */
        $url = $annotation->url;
        /** @var Workflow $urlWorkflow */
        $urlWorkflow = $url->workflow_get();

        if ($urlWorkflow->getMarking($url)->has(UrlStatus::SHARED->value)) {
            return false;
        }

        return true;
    }
}
