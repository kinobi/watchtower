<?php

namespace App\Jobs;

use App\Http\Integrations\TelegramBot\Requests\CreateUrlMessageRequest;
use App\Models\Url;

trait WithUrlMessageUpdate
{
    public function updateUrlMessage(Url $url, string $text): void
    {
        CleanChatJob::dispatch($url->getTelegramMessageReference());

        $botResponseReply = (new CreateUrlMessageRequest($url, $text))->send();

        $url->update(['message_id' => $botResponseReply->json('result.message_id')]);
    }
}
