<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\CreateAnnotationMessageRequest;
use App\Models\Url;
use App\Support\Jobs\WithUniqueUrl;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateAnnotationJob implements ShouldQueue, ShouldBeUnique
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
        $botResponseReply = (new CreateAnnotationMessageRequest($this->url))->send();

        $this->url->annotation()->firstOrCreate([
            'chat_id' => $botResponseReply->json('result.chat.id'),
            'message_id' => $botResponseReply->json('result.message_id'),
        ]);
    }
}
