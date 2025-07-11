<?php

namespace App\Livewire\Profile;

use BrasilApi\Client;
use BrasilApi\Exceptions\BrasilApiException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Business extends Component
{

    public $cnpj, $cargo;
 
    protected $messages = [
        'cnpj' => 'O cnpj da empresa é obrigatório.',
        'cargo' => 'O cargo da empresa é obrigatório.',
    ];

    public array $rules = [
        'cnpj' => ['required', 'string', 'max:255'],
        'cargo' => ['required', 'string', 'max:255'],
    ];

    public function render()
    {
        return view('livewire.profile.sections.business')->layout('layouts.admin');
    }

    public function mount()
    {

        $this->cnpj = Auth()->user()->cnpj;
        $this->cargo = Auth()->user()->cargo;
    }

    public function saveUser()
    {
        $this->validate();

        $user = Auth::user();
        $user->update([
            'cnpj' => $this->cnpj,
            'role' => $this->cargo,
        ]);


        return LivewireAlert::title('Dados alterado com sucesso')
            ->position('center')
            ->success()
            ->timer(6000)
            ->show();
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
