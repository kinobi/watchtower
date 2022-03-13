<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\UpdateUrlMessageRequest;
use App\Http\Integrations\Txtpaper\Requests\CreateMobiDocumentRequest;
use App\Models\Url;
use App\Services\UrlMessageFormatter;
use App\Support\Jobs\WithUniqueUrl;
use App\Support\UrlTransition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class WorkflowSendUrlToKindleJob extends AbstractWorkflowTransitionJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;
    use WithUniqueUrl;

    public function __construct(public readonly Url $url)
    {
    }

    protected function execute(UrlMessageFormatter $urlMessageFormatter): void
    {
        $this->url->workflow_apply(UrlTransition::TO_KINDLE->value);

        $text = __('watchtower.txtpaper.failed');
        $kindleEmail = config('services.txtpaper.mobi.email');

        if ($this->sendToKindle($kindleEmail)) {
            $text = __('watchtower.txtpaper.success');
            $this->url->save();
        }

        (new UpdateUrlMessageRequest(
            $this->url->refresh(),
            $urlMessageFormatter->formatHtmlMessage($this->url, $text)
        ))->send();
    }


    private function sendToKindle(mixed $kindleEmail): bool
    {
        $txtpaperResponse = (new CreateMobiDocumentRequest($this->url->uri, $kindleEmail))->send();
        if ($txtpaperResponse->json('status') === 'success') {
            return true;
        }

        Log::error(
            'Failed to send to Kindle',
            ['url' => $this->url, 'txtpaper_response' => $txtpaperResponse->json()]
        );

        return false;
    }
}
