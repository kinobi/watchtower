<?php

namespace Tests\Feature;

use App\Http\Integrations\TelegramBot\Requests\ReplyToAddUrlsRequest;
use App\Jobs\ReadUrlJob;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Config;
use Sammyjo20\Saloon\Http\MockResponse;
use Sammyjo20\SaloonLaravel\Facades\Saloon;
use Tests\TestCase;

class TelegramBotTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_webhook_callback_can_be_handled(): void
    {
        Bus::fake([ReadUrlJob::class]);
        Url::factory()->create();

        $telegramBotResponsePayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/answer_callback_query-ok.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        Saloon::fake([
            new MockResponse($telegramBotResponsePayload, 200),
            new MockResponse(['status' => 'success'], 200),
        ]);

        $webhookPayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/webhook-callback_query-read.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $response = $this->postJson(
            route('telegram.webhook'),
            $webhookPayload
        );

        $response->assertStatus(200);

        $this->assertDatabaseCount('telegram_updates', 1);
    }

    public function test_webhook_can_be_handled(): void
    {
        $telegramBotResponsePayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/send_message-ok.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        Saloon::fake([
//            new MockResponse(['status' => 'success'], 200),
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

        $this->assertDatabaseCount('telegram_updates', 1);

//        Saloon::assertSent(CreateMobiDocumentRequest::class);
        Saloon::assertSent(ReplyToAddUrlsRequest::class);
    }

    public function test_webhook_with_many_urls_can_be_handled(): void
    {
        $telegramBotResponsePayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/send_message-ok.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        Saloon::fake([
//            new MockResponse(['status' => 'success'], 200),
            new MockResponse($telegramBotResponsePayload, 200),
//            new MockResponse(['status' => 'success'], 200),
            new MockResponse($telegramBotResponsePayload, 200),
        ]);

        $webhookPayload = json_decode(
            file_get_contents(__DIR__ . '/../Fixtures/Telegram/webhook-text-with-many-urls.json'),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        $response = $this->postJson(
            route('telegram.webhook'),
            $webhookPayload
        );

        $response->assertStatus(200);


        $this->assertDatabaseCount('telegram_updates', 1);

//        Saloon::assertSent(CreateMobiDocumentRequest::class);
        Saloon::assertSent(ReplyToAddUrlsRequest::class);
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
