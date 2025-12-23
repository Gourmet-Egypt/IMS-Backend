<?php

namespace App\Providers;

use App\Events\PurchaseOrderCommitted;
use App\Listeners\GeneratePdfsListener;
use App\Listeners\PrintPurchaseOrderListener;
use App\Listeners\SendTransferEmailsListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        PurchaseOrderCommitted::class => [
            GeneratePdfsListener::class,
            SendTransferEmailsListener::class,
            PrintPurchaseOrderListener::class,
        ]
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
