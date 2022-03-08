<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\UpdateUrlMessageRequest;
use App\Http\Integrations\Txtpaper\Requests\CreateMobiDocumentRequest;
use App\Models\Url;
use App\Support\Job\WithUniqueUrl;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Workflow\Exception\NotEnabledTransitionException;
use Symfony\Component\Workflow\TransitionBlocker;

class WorkflowSendUrlToKindleJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use WithUniqueUrl;

    public function __construct(public readonly Url $url)
    {
    }

    public function handle(): void
    {
        try {
            $this->url->workflow_apply(UrlTransition::TO_KINDLE->value);

            $text = __('watchtower.txtpaper.failed');
            $kindleEmail = config('services.txtpaper.mobi.email');

            if ($this->sendToKindle($kindleEmail)) {
                $text = __('watchtower.txtpaper.success');
                $this->url->save();
            }

            (new UpdateUrlMessageRequest($this->url->refresh(), $text))->send();
        } catch (NotEnabledTransitionException $e) {
            Log::error($e->getMessage(), ['url' => $this->url]);

            $transitionBlockerList = $e->getTransitionBlockerList();
            if ($transitionBlockerList->isEmpty()) {
                return;
            }

            /** @var TransitionBlocker $blocker */
            foreach ($transitionBlockerList->getIterator() as $blocker) {
                Log::debug(
                    UrlTransition::TO_KINDLE->value . ' blocked by: ' . $blocker->getMessage(),
                    ['blocker' => $blocker->getParameters()]
                );
            }
        }
    }

    private function sendToKindle(mixed $kindleEmail): bool
    {
        $txtpaperResponse = (new CreateMobiDocumentRequest($this->url->uri, $kindleEmail))->send();
        if ($txtpaperResponse->json('status') === 'success') {
            return true;
        }

        Log::error(
            'Failed to send to Kindle',
            ['url' => $this->url, 'raindrop_response' => $txtpaperResponse->json()]
        );

        return false;
    }
}
