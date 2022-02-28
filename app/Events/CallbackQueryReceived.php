<?php

namespace App\Events;

use App\Models\TelegramUpdate;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CallbackQueryReceived
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly TelegramUpdate $telegramUpdate)
    {
    }
}
