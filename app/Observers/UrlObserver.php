<?php

namespace App\Observers;

use App\Models\Url;
use Symfony\Component\Workflow\Workflow;

class UrlObserver
{
    public function creating(Url $url): void
    {
        /** @var Workflow $workflow */
        $workflow = $url->workflow_get();
        $initialPlaces = $workflow->getDefinition()->getInitialPlaces();
        foreach ($initialPlaces as $initialPlace) {
            $workflow->getMarking($url)->mark($initialPlace);
        }
    }
}
