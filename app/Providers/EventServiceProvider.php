<?php

namespace App\Providers;

use App\Events\MailSentEvent;
use App\Events\PostHistoryEvent;
use App\Events\UserLoginAttemptedEvent;
use App\Listeners\WriteMailLogEventListener;
use App\Listeners\WriteLoginLogEventListener;
use App\Listeners\WritePostHistoryEventListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        UserLoginAttemptedEvent::class => [
            WriteLoginLogEventListener::class,
        ],
        MailSentEvent::class => [
            WriteMailLogEventListener::class,
        ],
        PostHistoryEvent::class => [
            WritePostHistoryEventListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
