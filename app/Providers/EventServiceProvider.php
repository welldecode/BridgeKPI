<?php

namespace App\Providers;

use App\Events\MercadoPagoWebhook;
use App\Listeners\MercadoPagoWebhookListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

use App\Events\PaypalWebhookEvent;
use App\Events\StripeWebhookEvent;
use App\Events\YokassaWebhookEvent;
use App\Events\TwoCheckoutWebhookEvent;
use App\Events\IyzicoWebhookEvent;

use App\Listeners\PaypalWebhookListener;
use App\Listeners\StripeWebhookListener;
use App\Listeners\YokassaWebhookListener;
use App\Listeners\TwoCheckoutWebhookListener;
use App\Listeners\IyzicoWebhookListener;


class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
      
        MercadoPagoWebhook::class => [
            MercadoPagoWebhookListener::class,
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
