<div>  
    <div class="d-flex flex-column mt-10">
        <div class="page page-center">
            <div class="container container-tight py-4">
                <div class="text-center mb-4">
                    <a href="{{ route('admin.index') }}" class="navbar-brand navbar-brand-autodark"><img src="https://i.imgur.com/hgXV0Wu.png"
                        height="70" width="200" alt=""></a>
                </div>
                <form class="card card-md" id="multiStepForm" wire:submit.prevent="register">
                    @csrf
                    {{-- STEP 1 --}}

                    <div class="card-body">

                        {{-- STEP 1 --}}

                        @if ($currentStep == 1)
                            <h2 class="card-title text-center mb-4">Criar uma conta</h2>
                            <!-- Barra de progresso -->

                            <ul class="steps steps-blue steps-counter my-4">
                                <li class="step-item active"></li>
                                <li class="step-item  "></li>
                                <li class="step-item"></li>
                                <li class="step-item"></li>
                            </ul>

                            <div class="form-step form-step-active" id="step-1">
                                <div class="mb-3 form_g">
                                    <x-input-label for="first_name" value="Nome" />
                                    <x-text-input id="first_name" wire:model="first_name" @class([' mt-1 ', 'is-invalid' => $errors->has('first_name')])
                                        type="text" placeholder="Digite seu Nome" name="first_name" />

                                </div>
                                <div class="mb-3">
                                    <x-input-label for="last_name" value="Sobrenome" />
                                    <x-text-input id="last_name" wire:model="last_name" @class(['mt-1', 'is-invalid' => $errors->has('last_name')])
                                        type="text" name="last_name" placeholder="Digite seu Sobrenome" required
                                        autocomplete="last_name" />

                                </div>
                                <div class="mb-3">
                                    <x-input-label for="cpf" value="CPF" />
                                    <x-text-input id="cpf" wire:model="cpf" class="block mt-1 w-full"
                                        data-mask="000.000.000-00" data-mask-visible="true" @class(['mt-1 ', 'is-invalid' => $errors->has('cpf')])
                                        type="text" name="cpf" required placeholder="Digite seu CPF" />
                                    @error('cpf')
                                        <div class="alert alert-danger mt-2 mb-2" role="alert">
                                            <div class="d-flex">
                                                <div>
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon alert-icon">
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
                                </div>
                                <div class="mb-3">
                                    <x-input-label for="niver" value="Data de Nascimento" />
                                    <x-text-input id="niver" wire:model="niver" class="block mt-1 w-full"
                                        @class(['mt-1', 'is-invalid' => $errors->has('niver')]) type="date" name="niver" required
                                        placeholder="Data de Nascimento" />
                                </div>
                                <div class="mb-3">
                                    <x-input-label for="phone" value="Telefone" />
                                    <x-text-input id="phone" wire:model="phone" class="block mt-1 w-full"
                                        data-mask="(00) 00000-0000" data-mask-visible="true"
                                        @class(['mt-1', 'is-invalid' => $errors->has('phone')]) type="text" name="phone" required
                                        placeholder="(00) 0000-0000" />
                                  
                                </div>

                            </div>
                        @endif
                        <!-- Etapa 2: Informações da Conta -->
                        {{-- STEP 2 --}}

                        @if ($currentStep == 2)
                            <h2 class="card-title text-center mb-4">Criar uma conta</h2>
                            <ul class="steps steps-blue steps-counter my-4">
                                <li class="step-item  "></li>
                                <li class="step-item active "></li>
                                <li class="step-item"></li>
                                <li class="step-item"></li>
                            </ul>
                            <div class="step form-step" id="step-2">
                                <div class="mb-3">
                                    <x-input-label for="email" value="E-mail" />
                                    <x-text-input id="email" wire:model="email" class="block mt-1 w-full"
                                        @class(['mt-1 ', 'is-invalid' => $errors->has('email')]) type="email" name="email" required
                                        placeholder="Digite seu e-mail" />
                                </div>
                                <div class="mb-3">
                                    <x-input-label for="password" value="Senha" />
                                    <x-text-input id="password" wire:model="password" class="block mt-1 w-full"
                                        @class(['mt-1 ', 'is-invalid' => $errors->has('password')]) type="password" name="password" required
                                        placeholder="Digite sua senha" />
                                        @error('password')
                                        <div class="alert alert-danger mt-2 mb-2" role="alert">
                                            <div class="d-flex">
                                                <div>
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon alert-icon">
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
                                </div>
                                <div class="mb-3">

                                    <x-input-label for="password_confirmation" value="Digite a senha novamente" />
                                    <x-text-input id="password_confirmation" wire:model="password_confirmation"
                                        class="block mt-1 w-full" @class([
                                            'mt-1 ',
                                            'is-invalid' => $errors->has('password_confirmation'),
                                        ]) type="password"
                                        name="password_confirmation" required
                                        placeholder="Digite sua senha novamente" />
                                        @error('password_confirmation')
                                        <div class="alert alert-danger mt-2 mb-2" role="alert">
                                            <div class="d-flex">
                                                <div>
                                                    <!-- Download SVG icon from http://tabler-icons.io/i/alert-circle -->
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                        class="icon alert-icon">
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
                                </div>


                            </div>
                        @endif
                        <!-- Etapa 3: Empresa -->
                        @if ($currentStep == 3)
                            <h2 class="card-title text-center mb-4">Criar uma conta</h2>
                            <!-- Barra de progresso -->
                            <ul class="steps steps-blue steps-counter my-4">
                                <li class="step-item  "></li>
                                <li class="step-item   "></li>
                                <li class="step-item active"></li>
                                <li class="step-item"></li>
                            </ul>
                            <div class="step form-step" id="step-3">
                                <div class="mb-3">

                                    <x-input-label for="cnpj" value="CNPJ da Empresa" />
                                    <x-text-input id="cnpj" wire:change="checkCNPJ($event.target.value)"
                                        data-mask="00.000.000/0000-00" data-mask-visible="true"
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
                                <div class="mb-3">
                                    <label class="form-label">Cargo</label>
                                    <select type="text" wire:model="cargo" name="cargo" id="cargo"
                                        @class([
                                            'mt-1 form-select',
                                            'form-select is-invalid' => $errors->has('cargo'),
                                        ])>
                                        <option value="">Selecione seu cargo</option>
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
                        @endif
                        <!-- Etapa 3: Empresa -->
                        @if ($currentStep == 4)
                            <!-- Barra de progresso -->
                            <ul class="steps steps-blue steps-counter my-4">
                                <li class="step-item  "></li>
                                <li class="step-item   "></li>
                                <li class="step-item  "></li>
                                <li class="step-item active"></li>
                            </ul>
                            <div class="step form-step" id="step-3">

                                <h1 class="card-title ">{{ $plan->name }}</h1>

                                <p class="text-secondary mb-4">
                                    {{ $plan->description }}
                                </p>
                                <ul class="list-unstyled space-y">
                                    <li class="row g-2">
                                        <span
                                            class="col-auto"><!-- Download SVG icon from http://tabler-icons.io/i/check -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon me-1 text-success">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M5 12l5 5l10 -10"></path>
                                            </svg>
                                        </span>
                                        <span class="col">
                                            <strong class="d-block">1 Colaborador</strong>
                                            <span class="d-block text-secondary">Tenha acesso a um colaborador.</span>
                                        </span>
                                    </li>
                                    <li class="row g-2">
                                        <span
                                            class="col-auto"><!-- Download SVG icon from http://tabler-icons.io/i/check -->
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                class="icon me-1 text-success">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M5 12l5 5l10 -10"></path>
                                            </svg>
                                        </span>
                                        <span class="col">
                                            <strong class="d-block">Todas Funções</strong>
                                            <span class="d-block text-secondary">Acesso a todas as funções
                                                administrativa.</span>
                                        </span>
                                    </li>
                                </ul>

                                <div class="my-4">
                                    <x-primary-button class="btn btn-primary w-100"
                                        type="submit">Cadastrar</x-primary-button>
                                </div>
                                <p class="text-secondary">
                                    Clicando em cadastrar irá direto para a tela de pagamento</a>.
                                </p>
                                </p>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between">

                            @if ($currentStep == 1)
                                <div></div>
                                <x-primary-button class="btn" type="button"
                                    wire:click="increaseStep()">Continuar</x-primary-button>
                            @endif

                            @if ($currentStep == 2 || $currentStep == 3)
                                <x-primary-button class="btn btn-secondary" type="button"
                                    wire:click="decreaseStep()">Voltar</x-primary-button>
                            @endif

                            @if ($currentStep == 2 || $currentStep == 3)
                                <x-primary-button type="button" class="btn  btn-primary"
                                    wire:click="increaseStep()">Continuar</x-primary-button>
                            @endif

                        </div>
                    </div>
                </form>
                <div class="text-center text-secondary mt-3">
                    Já possui uma conta? <a href="{{ route('login') }}" tabindex="-1">Entrar</a>
                </div>
            </div>
        </div>
    </div>
</div>


@section('script')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var el;
            window.TomSelect && (new TomSelect(el = document.getElementById('cargo'), {
                copyClassesToDropdown: false,
                dropdownParent: 'body',
                controlInput: '<input>',
                render: {
                    item: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                    option: function(data, escape) {
                        if (data.customProperties) {
                            return '<div><span class="dropdown-item-indicator">' + data
                                .customProperties + '</span>' + escape(data.text) + '</div>';
                        }
                        return '<div>' + escape(data.text) + '</div>';
                    },
                },
            }));
        });
    </script>
@endsection
