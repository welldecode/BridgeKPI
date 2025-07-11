<?php

namespace App\Livewire\Auth;
 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Login extends Component
{

    #[Validate('required|string|max:32')] // 1MB Max 
    public $email;

    #[Validate('required|string|max:8')] // 1MB Max 
    public $password;


    public $remember_me;

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }

    public function login()
    {

        $this->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = ($this->remember_me) ? true : false;
        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $remember)) {
 
            Toaster::success('Você foi logado com sucesso!');

            return redirect()->route('admin.index');
            // Authentication was successful  
        } else {

            throw ValidationException::withMessages([
                'password' =>  'Senha digitada errado ou está incorreta',
                'email' =>  'Email digitado está Incorreto',
            ]); 
        }
    }

    
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('auth/login'); 
    }
}
