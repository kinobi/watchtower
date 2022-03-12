<?php

namespace App\Providers;

use App\Events\CallbackQueryReceived;
use App\Events\ReplyReceived;
use App\Events\UrlsAdded;
use App\Listeners\AnnotationWorkflowSubscriber;
use App\Listeners\AnswerCallback;
use App\Listeners\StoreAnnotation;
use App\Listeners\StoreNewUrls;
use App\Listeners\UrlWorkflowSubscriber;
use App\Models\Annotation;
use App\Models\Url;
use App\Observers\AnnotationObserver;
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
        CallbackQueryReceived::class => [
            AnswerCallback::class,
        ],
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ReplyReceived::class => [
            StoreAnnotation::class,
        ],
        UrlsAdded::class => [
            StoreNewUrls::class,
        ],
    ];

    protected $subscribe = [
        AnnotationWorkflowSubscriber::class,
        UrlWorkflowSubscriber::class,
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot(): void
    {
        Annotation::observe(AnnotationObserver::class);
        Url::observe(UrlObserver::class);
    }
}
