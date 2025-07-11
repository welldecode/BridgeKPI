<div>
    <div class="container container-tight py-4">
        <div class="text-center mb-4">
          <a href="#" class="navbar-brand navbar-brand-autodark"><img src="https://i.imgur.com/hgXV0Wu.png"
            height="70" width="200" alt=""></a>
        </div>
        <form class="card card-md"wire:submit.prevent="resetPassword">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">
                    Esqueceu sua senha</h2>
                <p class="text-secondary mb-4">
                    Digite seu endereço de e-mail e a sua senha junto com a confirmação e ela será redefinada.</p>
                <div class="mb-3">

                    <x-input-label for="email" value="E-mail" />
                    <x-text-input id="email" wire:model="email" @class([' mt-1 ', 'is-invalid' => $errors->has('email')]) type="email"
                        placeholder="Digite seu E-mail" name="email" />
                    @error('email')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror

                </div>
                <div class="mb-3">
                    <x-input-label for="password" value="Nova Senha" />
                    <x-text-input id="password" wire:model="password" @class([' mt-1 ', 'is-invalid' => $errors->has('password')]) type="password"
                        placeholder="Digite sua senha" name="password" />
                    @error('password')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-3">
                    <x-input-label for="password_confirmation" value="Confirmar Senha" />
                    <x-text-input id="password_confirmation" wire:model="password_confirmation"
                        @class([
                            ' mt-1 ',
                            'is-invalid' => $errors->has('password_confirmation'),
                        ]) type="password" placeholder="Digite sua senha novamente"
                        name="password_confirmation" />
                    @error('password_confirmation')
                        <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-footer">
                    @if (session()->has('message'))
                        <div class="alert alert-success">{{ session('message') }}</div>
                    @endif
                    <x-primary-button type="submit" class="btn w-100">
                        Redefinir Senha</x-primary-button>

                </div>
            </div>
        </form>

    </div>
</div>
