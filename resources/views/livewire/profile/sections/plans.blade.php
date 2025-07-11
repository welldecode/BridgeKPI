<div>
    <div class="page-body">

        <div class="container-xl">
            <div class="card">
                <form action="" wire:submit.prevent="savePlan">
                    <div class="row g-0">
                        <div class="col-12 col-md-3 border-end">
                            @includeWhen('1', 'livewire.profile.sections.menu')
                        </div>
                        <div class="col-12 col-md-9 d-flex flex-column">
                            <div class="card-body">
                                <h2 class="mb-4">Meu Plano</h2>

                                <h3 class="card-title">Detalhes da Assinatura</h3>

                                <div class="row g-2 align-items-center">
                                    <div class="col-auto hidden">

                                        <span class="avatar avatar-lg hidden"
                                            style="background-image: url(./static/avatars/002m.jpg)"></span>

                                    </div>
                                    <div class="col">
                                        <h4 class="card-title m-0">
                                            <a href="#">{{ auth()->user()->activeSubscriptions->plan->name }}</a>
                                        </h4>
                                        <div class="text-secondary">
                                            {{ auth()->user()->activeSubscriptions->plan->description }}
                                        </div>
                                        @if (auth()->user()->activeSubscriptions())
                                            <div class="small mt-1">
                                                <span class="badge bg-green"></span> Ativo
                                            </div>
                                        @else
                                            <div class="small mt-1">
                                                <span class="badge bg-red"></span> Expirado
                                            </div>
                                        @endif

                                    </div>
                                    <div class="col-auto">
                                        <a href="#" class="btn" data-bs-toggle="modal"
                                            data-bs-target="#modal-plan">
                                            Alterar
                                        </a>
                                        <a href="#" class="btn btn-danger" data-bs-toggle="modal"
                                            data-bs-target="#modal-report">
                                            Cancelar Plano
                                        </a>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent mt-auto">
                            <div class="btn-list justify-content-end">
                                <a href="#" class="btn btn-1">
                                    Cancelar
                                </a>
                                <a href="#" class="btn btn-primary btn-2">
                                    Salvar Alterações
                                </a>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

    </div>

    <div x-data x-on:close-modal.window="bootstrap.Modal.getInstance($refs.modal).hide()" x-ref="modal"
        class="modal modal-blur fade" id="modal-plan" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <form wire:submit.prevent="changePlan" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Plano</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3 flex flex-col gap-3">
                        @foreach ($plans as $plan)
                            <label for="{{ $plan['name'] }}" class="flex items-center gap-3 p-3 border rounded-lg cursor-pointer">
                                <input type="radio" id="{{ $plan['name'] }}" name="planChange" wire:model.defer="selectedId" value="{{ $plan['id'] }}"
                                    class="accent-blue-600">
                                <div>
                                    <span class="font-medium">{{ $plan['name'] }}</span>
                                    <span class="text-sm text-gray-500 block">
                                        R$ {{ number_format($plan['monthly_prices'], 2, ',', '.') }} / mensal
                                    </span>
                                </div>
                            </label>
                        @endforeach  
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
                        Alterar Plano
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal modal-blur fade" id="modal-report" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <form wire:submit.prevent="cancelPlan" class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Cancelar Plano</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nome</label>
                        <input type="text" class="form-control" name="example-text-input"
                            value="{{ auth()->user()->activeSubscriptions->plan->name }}" disabled
                            placeholder="Your report name">
                    </div>
                </div>
                <div class="modal-body">

                    <div class="row">
                        <div class="col-lg-6">
                            <div class="mb-3">
                                <label class="form-label">E-mail</label>
                                <input type="text" class="form-control" value="{{ auth()->user()->email }}"
                                    disabled>
                            </div>
                        </div>
                        <div class="col-lg-6">

                            <div class="mb-3">
                                <label class="form-label">Valor do Plano</label>
                                <input type="text" class="form-control" value="@price(auth()->user()->activeSubscriptions->plan->monthly_prices)" disabled>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div>
                                <p class="form-label">
                                    Você está desativando a assinatura do
                                    {{ auth()->user()->activeSubscriptions->plan->name }}. Ao confirmar o cancelamento,
                                    seu acesso será encerrado, e você perderá os benefícios do plano.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-base">Digite sua senha </label>

                        <input type="password" class="form-control" name="password" id="password"
                            placeholder="Sua Senha">
                        @error('password')
                            <div class="alert alert-danger mt-2 mb-2" role="alert">
                                <div class="d-flex">
                                    <div>
                                        <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon">
                                            <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                            <path d="M12 8v4"></path>
                                            <path d="M12 16h.01"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        {{ $message }}
                                    </div>
                                </div>
                            </div>
                        @enderror
                        <p class="font-normal text-sm mt-2">Digite sua senha para confirmar o cancelamento do plano!
                        </p>
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
                        Cancelar Plano
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
