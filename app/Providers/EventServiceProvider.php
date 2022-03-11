<?php

namespace App\Providers;

use App\Events\CallbackQueryReceived;
use App\Events\UrlsAdded;
use App\Listeners\AnswerCallback;
use App\Listeners\StoreNewUrls;
use App\Listeners\UrlWorkflowSubscriber;
use App\Models\Url;
use App\Observers\UrlObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UrlsAdded::class => [
            StoreNewUrls::class,
        ],
        CallbackQueryReceived::class => [
            AnswerCallback::class,
        ]
    ];

    protected $subscribe = [
        UrlWorkflowSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        Url::observe(UrlObserver::class);
    }
}
