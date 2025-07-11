<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class User extends Component
{

    public $name, $email;

    public string $current_password = '';
    public string $new_password = '';
    public string $new_password_confirmation = '';

    protected $messages = [
        'name' => 'O nome da conta é obrigatório.',
        'email' => 'O email da conta é obrigatório.',
    ];

    public array $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'max:255'],
    ];

    public function render()
    {
        return view('livewire.profile.sections.user')->layout('layouts.admin');
    }

    public function mount()
    {

        $this->name = Auth()->user()->first_name;
        $this->email = Auth()->user()->email;
    }

    public function saveUser()
    {
        $this->validate();

        $user = Auth::user();
        $user->update([
            'first_name' => $this->name,
            'email' => $this->email,
        ]);


        return LivewireAlert::title('Dados alterado com sucesso')
            ->position('center')
            ->success()
            ->timer(6000)
            ->show();
    }

    public function changePassword()
    {
        $this->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'A senha atual está incorreta.');
            return;
        }

        $user->password = Hash::make($this->new_password);
        $user->save();

        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

        /** 🔑 fecha o modal no browser */
        $this->dispatch('close-modal');   // Livewire 3
        return LivewireAlert::title('Senha alterada com sucesso')
            ->position('center')
            ->info()
            ->timer(6000)
            ->withCancelButton('Cancelar')
            ->cancelButtonColor('#d33')
            ->show();

    }

}
