<div>
    <div class="col-md-12  mt-5">


        <form wire:submit.prevent="apply"> 
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Aplicar Cupom</h3>
                    <p class="card-subtitle">Aplique um cupom para conseguir desconto.</p>
               

                    <div class="input-icon"> 
                        <x-text-input id="code" wire:model="code" @class([' mt-1 form-control', 'is-invalid' => session()->has('error')])  type="text"
                            placeholder="Código do Cupom" name="code" /> 
                    </div>
                    <div class="mt-2">
                        @if (session()->has('error'))
                        <div style="color: red;">{{ session('error') }}</div>
                    @endif

                    @if (session()->has('success'))
                        <div style="color: green;">{{ session('success') }}</div>
                    @endif
                    </div>
                    <div class="row align-items-center mt-4">
                        
                        <div class="col">
 
                            @if ($discountedTotal)
                                <p>Total com desconto: {{ $discountedTotal }}</p>
                            @endif
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary">
                                Aplicar Cupom
                            </button>
                        </div>
                    </div>
                </div>
              
            </div>

        </form>
    </div>
</div>
