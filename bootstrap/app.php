<?php

use App\Console\Commands\ChargeSubscriptions;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->use([
            \Illuminate\Foundation\Http\Middleware\InvokeDeferredCallbacks::class,
            // \Illuminate\Http\Middleware\TrustHosts::class,
            \Illuminate\Http\Middleware\TrustProxies::class,
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance::class,
          
            \Illuminate\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,

            
        ]);
        $middleware->validateCsrfTokens(except: [ 
            'https://bridges.devstep.com.br/webhooks/mercadopago', 
            '/webhooks/mercadopago', 
        ]);
        $middleware->alias([
            /**** OTHER MIDDLEWARE ALIASES ****/ 
            
            'plan_subscription' => \App\Http\Middleware\PlanVerify::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->withEvents(discover: [
        \App\Events\MercadoPagoWebhook::class, 
    ])->withCommands([
        ChargeSubscriptions::class
    ])->create();
