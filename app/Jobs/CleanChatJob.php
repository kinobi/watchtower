<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use App\Http\Integrations\TelegramBot\Requests\DeleteMessageRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CleanChatJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** @var MessageReference[] */
    private array $messageReferences;

    public function __construct(MessageReference ...$messageReferences)
    {
        $this->messageReferences = $messageReferences;
    }

    public function handle(): void
    {
        collect($this->messageReferences)
            ->each(
                fn(MessageReference $messageReference) => (new DeleteMessageRequest($messageReference))->send()
            );
    }
}
