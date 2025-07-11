<?php

namespace App\Listeners;

use App\Events\MercadoPagoWebhook;
use App\Models\Webhook;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\InteractsWithQueue;

class MercadoPagoWebhookListener
{


    use InteractsWithQueue;

    public $afterCommit = false;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MercadoPagoWebhook $event): void
    {
        //   
        Webhook::create([
            'event_type' => 'payment',
            'data' => 'asd',
        ]);
        
    }
}
