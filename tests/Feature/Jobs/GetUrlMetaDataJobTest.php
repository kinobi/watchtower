<?php

namespace Tests\Feature\Jobs;

use App\Jobs\GetUrlMetaDataJob;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUrlMetaDataJobTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_can_retrieve_url_meta_data(): void
    {
        $this->markTestSkipped('For dev purpose, do a real HTTP call');

        $url = Url::factory()->create();
        $job = new GetUrlMetaDataJob($url);

        $job->handle();

        $this->assertSame('Example Domain', $url->refresh()->title);
    }
}
