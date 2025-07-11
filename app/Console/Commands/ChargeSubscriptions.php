<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSubscriptionPayment;
use App\Mail\Subscription;
use App\Models\PlanSubscriptions;
use App\Models\User;
use App\Notifications\SubscriptionExpiryWarning;
use Illuminate\Console\Command;
use App\Models\PlanRecurringSubscriptions;
use Illuminate\Support\Facades\Mail;

class ChargeSubscriptions extends Command
{
    protected $signature = 'subscriptions:handle';
    protected $description = 'Gerencia assinaturas: notifica quando faltam 7 dias e processa cobranças.';


    public function handle()
    { 
        $now = now();
        $sevenDaysFromNow = $now->addDays( 7);

        // Notificar assinaturas faltando 7 dias para o final
        $subscriptionsToNotify = PlanSubscriptions::where('stats', 'active')
            ->whereDate('end_date', '=', $sevenDaysFromNow)
            ->get();

        foreach ($subscriptionsToNotify as $subscription) {
            $subscription->user->notify(new SubscriptionExpiryWarning($subscription));
        }

        // Processar assinaturas ativas com próxima cobrança vencida ou no dia
        $subscriptionsToCharge = PlanSubscriptions::where('stats', 'active')
            ->whereDate('next_billing_date', '<=', $now)
            ->get();

        foreach ($subscriptionsToCharge as $subscription) {
            ProcessSubscriptionPayment::dispatch($subscription);
        }

        $this->info('Gerenciamento de assinaturas concluído.');
    }
}
