<?php

namespace App\Livewire\Profile;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Validate;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Plans extends Component
{

    public array $plans = [];
    public int $currentId;        // plano atual do usuário
    public int $selectedId;       // escolha no formulário

    #[Validate('required|string|max:12')] // 1MB Max 
    public $password;


    protected $messages = [
        'password' => 'O campo de senha é obrigatorio.',
    ];


    public function render()
    {
        return view('livewire.profile.sections.plans')->layout('layouts.admin');
    }

    public function mount()
    {
        $this->plans = Plan::orderBy('monthly_prices')->get()->toArray();
        $this->currentId = auth()->user()->subscriptions->id_plan;
        $this->selectedId = $this->currentId;
    }

    public function cancelPlan()
    {

        $this->validate([
            'password' => 'required|string',
        ]);
        dd('passou aq');
        $user = Auth::user();
        if (Hash::check($this->password, $user->password)) {

            $plan = auth()->user()->subscriptions;

            $plan::update([
                'active' => 'expired'
            ]);
            LivewireAlert::title('Sucesso!')
                ->text('Seu plano foi cancelado com sucesso!')
                ->position('center')
                ->timer(3000)
                ->toast()
                ->show();

        }
    }

    public function changePlan(bool $immediately = true): void
    {
    
            dd('passou aq');
        DB::transaction(function () use ($immediately) {

            $subscription = auth()->user()->subscriptions; // garante consistência
            $newPlan = Plan::find($this->selectedId); 
            // === 2. Atualize sua tabela 'subscriptions' ====================
            if ($immediately) {
                // cobrança já feita ou agendada — altere agora
                $subscription->update([
                    'id_plan' => $newPlan->id,
                    'end_date' => null, // zero porque agora depende do novo plano
                    'next_billing_date' => Carbon::now()->addDays($newPlan->interval_days),
                    'trial_end_date' => null,
                ]);
            } else {
                // troca futura: grave num campo extra  
                $subscription->update([
                    'pending_plan_id' => $newPlan->id,
                ]);
            }
        });

    }

}
