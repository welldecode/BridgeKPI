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
                                <h2 class="mb-4">Minha Conta</h2>
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
                                    <h3 class="card-title mt-4">Nome da Conta</h3>
                                    <p class="card-subtitle">Você pode alterar o nome da sua conta.</p>

                                    <div>
                                        <x-text-input placeholder="Digite o novo nome da sua empresa" name="name"
                                            id="name" wire:model="name" />
                                    </div>
                                </div>

                                <div class="">
                                    <h3 class="card-title mt-4">Email</h3>
                                    <p class="card-subtitle">Você pode alterar o email da sua conta.</p>

                                    <div>
                                        <x-text-input placeholder="Digite o novo email da sua empresa" name="email"
                                            id="email" wire:model="email" />
                                    </div>
                                </div>
                                <h3 class="card-title mt-4">Senha</h3>
                                <p class="card-subtitle">Certifique-se de que sua conta esteja usando uma senha longa e
                                    aleatória para permanecer segura!
                                </p>
                                <div>
                                    <div>
                                        <div class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#modal-report">Mudar Senha</div>
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
