<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Password;
use Livewire\Component;
use Masmerise\Toaster\Toaster;
use Illuminate\Validation\ValidationException;

class ForgotPassword extends Component
{

    #[Validate('required|string|max:32')] // 1MB Max 
    public $email;

    public function render()
    {
        return view('livewire.auth.forgot-password')->layout('layouts.guest');
    }

    public function storePassword()
    {
        $this->validate();
   
        // Enviar o email com o link para redefinir a senha
        $status = Password::sendResetLink(['email' => $this->email]);
        
        if ($status == Password::RESET_LINK_SENT) {
            session()->flash('message', 'Link de redefinição de senha enviado!'); 
        } else {
            throw ValidationException::withMessages(['email' => [trans($status)]]);
        }
    }
}
