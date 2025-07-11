<div> 
    <div class="row g-0 flex-fill">
        <div class="col-12 col-lg-8 col-xl-5 border-top-wide border-primary d-flex flex-column justify-content-center">
            <div class="container container-tight my-5 px-lg-5">
                <div class="text-center mb-4">
                    <a href="{{ route('admin.index') }}" class="navbar-brand navbar-brand-autodark"><img src="https://i.imgur.com/hgXV0Wu.png"
                            height="70" width="200" alt=""></a>
                </div> 
                <form wire:submit.prevent="login" novalidate>
                    <div class="mb-3">
                        <x-input-label for="email" value="E-mail" />
                        <x-text-input id="email" wire:model="email" @class([' mt-1 ', 'is-invalid' => $errors->has('email')]) type="email"
                            placeholder="Digite seu E-mail" name="email" />
       <div class="invalid-feedback">@error('email') {{ $message }} @enderror</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">
                            Senha
                            <span class="form-label-description">
                                <a href="{{ route('password.request') }}">Esqueci minha senha</a>
                            </span>
                        </label>
                        <div class="input-group input-group-flat">
                            <x-text-input id="password" wire:model="password" @class(['  ', 'is-invalid' => $errors->has('password')])
                                type="password" placeholder="Digite sua senha" name="password" />
 
      <div class="invalid-feedback">@error('password') {{ $message }} @enderror</div>
                        </div>  
                    </div>
                    <div class="mb-2">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input" wire:model="remember_me" name="remember_me" id="remember_me" />
                            <span class="form-check-label">Me mantenha conectado</span>
                        </label>
                    </div>
                    <div class="form-footer">

                        <x-primary-button type="submit" class="btn w-100">Entrar</x-primary-button>
                    </div>
                </form>
                <div class="text-center text-secondary mt-3">
                    Ainda não fez uma conta? <a href="{{ route('register', ['id' => 1]) }}" tabindex="-1">Registre-se</a>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 col-xl-7 d-none d-lg-block">
            <!-- Photo -->
            <div class="bg-cover h-100 min-vh-100" style="background-image: url(https://i.imgur.com/CJJuO5Z.jpeg)">
            </div>
        </div>
    </div> 
</div>
