<?php

namespace App\Support;

use App\Http\Integrations\TelegramBot\Dtos\MessageReference;
use App\Jobs\GetUrlJob;
use App\Jobs\SearchUrlJob;

enum Command: string
{
    case GET = 'get';
    case SEARCH = 'search';

    public function execute(MessageReference $messageReference, string $payload): void
    {
        match ($this) {
            self::GET => GetUrlJob::dispatch($messageReference, $payload),
            self::SEARCH => SearchUrlJob::dispatch($messageReference, $payload),
        };
    }
}
