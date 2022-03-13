<?php

namespace App\Jobs;

use App\Services\UrlMessageFormatter;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\TransitionBlocker;

abstract class AbstractWorkflowTransitionJob
{
    abstract protected function execute(UrlMessageFormatter $urlMessageFormatter);

    public function handle(UrlMessageFormatter $urlMessageFormatter): void
    {
        try {
            $this->execute($urlMessageFormatter);
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
