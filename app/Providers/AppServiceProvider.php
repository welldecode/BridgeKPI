<?php

namespace App\Providers;

use App\Events\MercadoPagoWebhook;
use App\Listeners\MercadoPagoWebhookListener;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 
      
        Blade::directive('price', function ($expression) {
            return "<?php echo config('shop.currency_sign') . number_format($expression, 2, ',', '.'); ?> ";
        });
    }
}
