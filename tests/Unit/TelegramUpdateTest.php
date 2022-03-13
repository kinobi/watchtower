<?php

namespace Tests\Unit;

use App\Models\TelegramUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TelegramUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_have_telegram_message_reference(): void
    {
        /** @var TelegramUpdate $tgu */
        $tgu = TelegramUpdate::factory()->create();
        $messageReference = $tgu->getTelegramMessageReference();

        $this->assertSame(123456789, $messageReference->chatId);
        $this->assertSame(21, $messageReference->messageId);
    }
}
