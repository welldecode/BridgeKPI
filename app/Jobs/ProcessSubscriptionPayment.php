<?php

namespace App\Jobs;

use App\Models\PlanRecurringSubscriptions;
use App\Models\PlanSubscriptions;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessSubscriptionPayment  
{
    use Queueable;

    public $subscription;

    /**
     * Create a new job instance.
     */
    public function __construct(PlanSubscriptions $subscription)
    {
        $this->subscription = $subscription;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        //
        $subscription = $this->subscription;

        // Verifica se a data final já foi alcançada
        if ($subscription->end_date && now()->greaterThanOrEqualTo($subscription->end_date)) {
            $subscription->update(['stats' => 'expired']);
            Log::info("Assinatura ID {$subscription->id} expirou.");
            return;
        }

        // Simulação de pagamento (substitua por integração real)
        $paymentSuccessful = true;

        if ($paymentSuccessful) {
            $subscription->update([
                'next_billing_date' => $subscription->next_billing_date->addMonth(),
            ]);
            Log::info("Cobrança bem-sucedida para a assinatura ID {$subscription->id}");
        } else {
            $subscription->update(['stats' => 'pending']);
            Log::error("Falha na cobrança para a assinatura ID {$subscription->id}");
        }
    }
}
