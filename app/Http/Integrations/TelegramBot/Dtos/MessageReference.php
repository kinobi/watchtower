<?php

namespace App\Http\Integrations\TelegramBot\Dtos;

class MessageReference
{
    public function __construct(public readonly int $chatId, public readonly int $messageId)
    {
    }
}
