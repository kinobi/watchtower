<?php

namespace Tests\Feature\Jobs;

use App\Jobs\WorkflowBookmarkUrlJob;
use App\Models\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WorkflowBookmarkUrlJobTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function test_it_can_bookmark(): void
    {
        $this->markTestSkipped('For dev purpose, do a real HTTP call');

        $job = new WorkflowBookmarkUrlJob(
            Url::factory()->create(
                [
                    'host' => 'www.youtube.com',
                    'path' => '/watch',
                    'query' => 'v=127ng7botO4',
                    'title' => 'Laravel Origins: The Documentary',
                    'status' => ['read' => 1, 'not_annotated' => 1, 'not_on_kindle' => 1]
                ]
            )
        );

        $job->handle();

        $this->assertArrayHasKey(
            'bookmarked',
            Url::factory()->create(
                [
                    'host' => 'www.youtube.com',
                    'path' => '/watch',
                    'query' => 'v=127ng7botO4',
                    'title' => 'Laravel Origins: The Documentary',
                    'status' => ['read' => 1, 'not_annotated' => 1, 'not_on_kindle' => 1]
                ]
            )->refresh()->status
        );
    }
}
