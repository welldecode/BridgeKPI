<div>
    <div class="page-body">

        <div class="container-xl">
            <div class="card">
                <form wire:submit.prevent="saveUser">
                    <div class="row g-0">
                        <div class="col-12 col-md-3 border-end">
                            @includeWhen('1', 'livewire.profile.sections.menu')
                        </div>
                        <div class="col-12 col-md-9 d-flex flex-column">
                            <div class="card-body">
                                <h2 class="mb-4">Minha Empresa</h2>
                                <div class="row align-items-center ">
                                    <div class="col-auto hidden">
                                        <span class="avatar avatar-xl"
                                            style="background-image: url(./static/avatars/000m.jpg)"></span>
                                    </div>
                                    <div class="col-auto hidden">
                                        <a href="#" class="btn btn-info">
                                            Alterar Avatar
                                        </a>
                                    </div>
                                    <div class="col-auto hidden">
                                        <a href="#" class="btn btn-ghost-danger btn-3">
                                            Deletar Avatar
                                        </a>
                                    </div>
                                </div>

                                <div class="">
                                    <h3 class="card-title mt-4">CNPJ Da Empresa</h3>
                                    <p class="card-subtitle">Altere se tiver certeza o CNPJ da sua empresa.</p>

                                    <div>
                                                  <x-text-input id="cnpj" wire:change="checkCNPJ($event.target.value)"
                                        data-mask="00.000.000/0000-00" data-mask-visible="true" wire:model="cnpj"
                                        class="block mt-1 w-full" @class(['mt-1 ', 'is-invalid' => $errors->has('cnpj')]) type="text"
                                        name="cnpj" required placeholder="CNPJ da empresa" />
                                                                            @if (session()->has('success'))
                                        <div class="alert mt-2 alert alert-success alert-dismissible">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    @if (session()->has('error'))
                                        <div class="alert mt-2 alert alert-danger alert-dismissible">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                    </div>
                                </div>

                                <div class="">
                                    <h3 class="card-title mt-4">Cargo</h3>
                                    <p class="card-subtitle">Você pode alterar o seu cargo da empresa.</p>

                                    <div>
                                          <select type="text" wire:model="cargo" name="cargo" id="cargo"
                                        @class([
                                            'mt-1 form-select',
                                            'form-select is-invalid' => $errors->has('cargo'),
                                        ])>
                                        <option value="" hidden selected>Selecione seu cargo</option>
                                        <option value="CEO">CEO</option>
                                        <option value="Diretor">Diretor</option>
                                        <option value="Gerente">Gerente</option>
                                        <option value="Supervisor">Supervisor</option>
                                        <option value="Coordenador">Coordenador</option>
                                        <option value="Analista">Analista</option>
                                        <option value="Assistente">Assistente</option>
                                        <option value="Estagiário">Estagiário</option>
                                        <option value="Outro">Outro</option>
                                    </select>

                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="card-footer bg-transparent mt-auto">
                            <div class="btn-list justify-content-end">
                                <div>

                                </div>
                                <button type="submit" class="btn btn-primary btn-2">
                                    Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
<div 
    x-data
    x-on:close-modal.window="bootstrap.Modal.getInstance($refs.modal).hide()"
    x-ref="modal"
    class="modal modal-blur fade" 
    id="modal-report" 
    tabindex="-1"
    aria-hidden="true"
> 
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <form wire:submit.prevent="changePassword" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="block font-medium">Senha Atual</label>
                        <input type="password" class="form-control" wire:model.defer="current_password"
                            class="w-full border p-2 rounded" />
                        @error('current_password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror

                        <p class="font-normal text-sm mt-2">Digite sua senha para confirmar a nova senha da conta!</p>
                    </div>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-base">Digite sua nova Senha </label> 
                        <input type="password" class="form-control" wire:model.defer="new_password"
                            class="w-full border p-2 rounded" />
                        @error('new_password')
                            <span class="text-red-600 text-sm">{{ $message }}</span>
                        @enderror 
                    </div>
                          <div class="mb-3">
                        <label class="form-label text-base">Confirmar Nova Senha</label> 
                        <input type="password" class="form-control" wire:model.defer="new_password_confirmation"
                            class="w-full border p-2 rounded" /> 
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="#" class="btn btn-danger  btn-3" data-bs-dismiss="modal">
                        Fechar
                    </a>
                    <button type="submit" class="btn btn-primary btn-5 ms-auto">
                        <!-- Download SVG icon from http://tabler.io/icons/icon/plus -->
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-2">
                            <path d="M12 5l0 14"></path>
                            <path d="M5 12l14 0"></path>
                        </svg>
                        Salvar Alteração
                    </button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
<script>
document.addEventListener('close-modal', () => {
    const modalEl = document.getElementById('modal-report');
    const modal   = bootstrap.Modal.getInstance(modalEl);
    if (modal) {
        modal.hide();          // fecha o diálogo
    }
});
</script>
@endpush
</div>
