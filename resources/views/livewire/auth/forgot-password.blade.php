<div>
    <div class="container container-tight py-4">
      <div class="text-center mb-4">
        <a href="#" class="navbar-brand navbar-brand-autodark"><img src="https://i.imgur.com/hgXV0Wu.png"
                height="70" width="200" alt=""></a>
    </div> 
        <form class="card card-md"wire:submit.prevent="storePassword">
          <div class="card-body">
            <h2 class="card-title text-center mb-4">
              Esqueceu sua senha</h2>
            <p class="text-secondary mb-4">
              Digite seu endereço de e-mail e sua senha será redefinida e enviada para você por e-mail.</p>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" wire:model="email" class="form-control" placeholder="Digite seu e-mail">
              @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
    
            </div>
            @if (session()->has('message'))
            <div style="color: green;">{{ session('message') }}</div>
        @endif
            <div class="form-footer">
          
            
              <x-primary-button type="submit" class="btn w-100">      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="icon"><path stroke="none" d="M0 0h24v24H0z" fill="none"></path><path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z"></path><path d="M3 7l9 6l9 -6"></path></svg>
                Envie-me nova senha</x-primary-button>
            
            </div>
          </div>
        </form>
        <div class="text-center text-secondary mt-3">
          Esqueça, <a href="{{ route('login') }}">mande-me de volta</a> para a tela de login.
        </div>
      </div>
</div>