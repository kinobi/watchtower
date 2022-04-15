<?php

namespace App\Events;

use App\Models\TelegramUpdate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BotCommandReceived
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(public readonly TelegramUpdate $telegramUpdate)
    {
    }
}
