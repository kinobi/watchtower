<?php

namespace App\Jobs;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\TransitionBlocker;

abstract class AbstractWorkflowTransitionJob
{
    abstract protected function execute();

    public function handle(): void
    {
        try {
            $this->execute();
        } catch (NotEnabledTransitionException $e) {
            Log::error($e->getMessage(), ['subject' => $e->getSubject()]);

            $transitionBlockerList = $e->getTransitionBlockerList();
            if ($transitionBlockerList->isEmpty()) {
                return;
            }

            /** @var TransitionBlocker $blocker */
            foreach ($transitionBlockerList->getIterator() as $blocker) {
                Log::debug(
                    sprintf("%s blocked by: %s", $e->getTransitionName(), $blocker->getMessage()),
                    ['blocker_params' => $blocker->getParameters(), 'blocker_' => $blocker->getCode()]
                );
            }
        }
    }
}
