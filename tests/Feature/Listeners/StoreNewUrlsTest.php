<?php

namespace Tests\Feature\Listeners;

use App\Events\UrlsAdded;
use App\Listeners\StoreNewUrls;
use App\Models\TelegramUpdate;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreNewUrlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_create_new_url(): void
    {
        $listener = $this->app->get(StoreNewUrls::class);
        $urlsAdded = new UrlsAdded(TelegramUpdate::factory()->create());

        $listener->handle($urlsAdded);

        $this->assertDatabaseCount('urls', 1);
    }
}
