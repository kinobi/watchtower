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

            $txtpaperResponse = (new CreateMobiDocumentRequest($this->url->uri, $kindleEmail))->send();
            if ($txtpaperResponse->json('status') === 'success') {
                $text = __('watchtower.txtpaper.success');
                $this->url->save();
            }

            (new UpdateUrlMessageRequest($this->url, $text))->send();
        } catch (NotEnabledTransitionException $e) {
            Log::error($e->getMessage(), ['url' => $this->url]);
        }
    }
}
