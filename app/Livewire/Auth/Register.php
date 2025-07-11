<?php

namespace App\Livewire\Auth;

use App\Facades\Cart;
use App\Models\Plan;
use App\Models\User;
use BrasilApi\Exceptions\BrasilApiException;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use BrasilApi\Client;
use Livewire\Attributes\Url;

class Register extends Component
{
    public $totalSteps = 4;

    public $currentStep = 1;

    public $first_name, $last_name, $cpf, $niver, $phone, $email, $password, $password_confirmation, $cnpj, $cargo;

    #[Url]
    public $id;

    public $plan;
    
    protected $messages = [
        'phone.required' => 'O campo telefone é obrigatório..',
        'password' => 'O campo de senha deve corresponder à confirmação da senha.',
        'password_confirmation' => 'O campo de confirmação da senha deve ter pelo menos 8 caracteres.'
    ];

    public function render()
    {
        return view('livewire.auth.register')->layout('layouts.guest');
    }

    public function mount()
    {
        $this->plan = Plan::where('id', $this->id)->firstOrFail();
    }

    public function register()
    {
        $this->validateData();

        if ($this->checkCNPJ($this->cnpj) != 'invalid') {

            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'cpf' => $this->cpf,
                'cnpj' => $this->cnpj,
                'birthday' => $this->niver,
                'phone' => $this->phone,
                'role' => $this->cargo,
                'password' => Hash::make($this->password),
            ]);
            event(new Registered($user));
            Cart::addToCart($this->plan->id);
            Auth::login($user);

            return redirect(route('cart.index'));
        } else {
            $this->resetErrorBag();
        }
    } 

    public function increaseStep()
    {
        $this->resetErrorBag();
        $this->validateData();
        $this->currentStep++;
        if ($this->currentStep > $this->totalSteps) {
            $this->currentStep = $this->totalSteps;
        }
    }

    public function decreaseStep()
    {
        $this->resetErrorBag();
        $this->currentStep--;
        if ($this->currentStep < 1) {
            $this->currentStep = 1;
        }
    }

    public function validateData()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'cpf' => 'required|cpf',
                'niver' => 'required|date|date_format:Y-m-d|before:' . now()->subYears(18)->toDateString(),
                'phone' => 'required|celular_com_ddd',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'email' => ['required', 'email'],
                'password' => 'min:8|required_with:password_confirmation|same:password_confirmation',
                'password_confirmation' => 'min:8'
            ]);
        } elseif ($this->currentStep == 3) {
            $this->validate([
                'cargo' => 'required',
            ]);
        } elseif ($this->currentStep == 4) {

        }
    }


    public function checkCNPJ($value)
    {

        $cnpj = preg_replace('/[^0-9]/', '', $value);
        $brasilApi = new Client();
        if (isset($cnpj)) {
            try {
                $address = $brasilApi->cnpj()->get($cnpj);
                if (isset($address['descricao_situacao_cadastral'])) {
                    session()->flash('success', 'Seu CNPJ é válido para cadastro.');
                    return $this->cnpj = $value;
                } else {
                    session()->flash('success', 'Seu CNPJ não válido para cadastro.');
                    return $this->cnpj = 'invalid';
                }
            } catch (BrasilApiException $e) {
                if ($e->getMessage() == 'An error occurred') {
                    session()->flash('error', 'Verifique o campo CNPJ.');
                    return $this->cnpj = 'invalid';
                } else if ($e->getMessage() == 'CNPJ ' . $value . ' inválido.') {
                    session()->flash('error', $e->getMessage());
                }
            }
        }

    }
}
