<?php

namespace Tests\Feature;

use App\Http\Integrations\TelegramBot\Requests\SendMessageRequest;
use App\Http\Integrations\Txtpaper\Requests\CreateMobiDocumentRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Config;
use Sammyjo20\Saloon\Http\MockResponse;
use Sammyjo20\SaloonLaravel\Facades\Saloon;
use Tests\TestCase;

class TelegramBotTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_webhok_can_be_handled(): void
    {
        $telegramBotResponsePayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/send_message-ok.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        Saloon::fake([
            new MockResponse(['status' => 'success'], 200),
            new MockResponse($telegramBotResponsePayload, 200),
        ]);

        $webhookPayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/webhook-text-with-url.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $response = $this->postJson(
            route('telegram.webhook'),
            $webhookPayload
        );

        $response->assertStatus(200);

        Saloon::assertSent(CreateMobiDocumentRequest::class);
        Saloon::assertSent(SendMessageRequest::class);
    }

    public function test_wrong_telegram_user_is_rejected(): void
    {
        // Change the Telegram User Id
        Config::set('services.telegram.user.id', 987654321);

        $webhookPayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/webhook-text-with-url.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $response = $this->postJson(
            route('telegram.webhook'),
            $webhookPayload
        );

        $response->assertForbidden();
    }

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('services.telegram.user.id', 123456789);
    }
}
