<div>

    <x-slot name="header">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                            Demonstrações Financeiras
                        </div>
                        <h2 class="page-title">

                            Demonstração do resultado do exercício
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
    <div>
        <div class="card">

            <div class="mt-5 px-4 mb-5">
                <form action="" wire:submit.prevent="getDados">
                    <div class="flex  flex-col gap-4">
                        <!-- Tipo de Inserção -->
                        @if ($showTypeSelect)
                            <div>
                                <label for="tipo" class="block">Tipo de Inserção:</label>
                                <select wire:model="type" id="tipo" wire:change="onTypeChange"
                                    class="p-2 pe-5 border border-gray-300 rounded  ">
                                    <option value="">Selecione o tipo</option>
                                    <option value="mensal">Inserção Mensal</option>
                                    <option value="trimestral">Inserção Trimestral</option>
                                    <option value="anual">Inserção Anual</option>
                                </select>
                            </div>
                        @endif
                        <!-- Seleção de Ano -->
                        @if ($showAno)
                            <div>
                                <label for="selectedYear" class="block">Ano:</label>
                                <select wire:model="anos" multiple id="selectedYear"
                                    class="p-2 pe-8 border border-gray-300 rounded">
                                    @for ($i = 2025; $i >= 2001; $i--)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                        @endif

                        <!-- Mês (se tipo for mensal e ano tiver sido escolhido) -->

                        @if ($showMes)
                            @if ($type === 'mensal' && !empty($anos))
                                <div>
                                    <label for="selectedMonth" class="block">Mês:</label>
                                    <select wire:model="meses" multiple id="selectedMonth"
                                        class="p-2 pe-8 border border-gray-300 rounded w-fit">
                                        <option value="0">Janeiro</option>
                                        <option value="1">Fevereiro</option>
                                        <option value="2">Março</option>
                                        <option value="3">Abril</option>
                                        <option value="4">Maio</option>
                                        <option value="5">Junho</option>
                                        <option value="6">Julho</option>
                                        <option value="7">Agosto</option>
                                        <option value="8">Setembro</option>
                                        <option value="9">Outubro</option>
                                        <option value="10">Novembro</option>
                                        <option value="11">Dezembro</option>
                                    </select>
                                </div>
                            @endif

                        @endif
                        @if ($showTrimestre)
                            <!-- Trimestre (se tipo for trimestral e ano tiver sido escolhido) -->
                            @if ($type === 'trimestral' && !empty($anos))
                                <div>
                                    <label for="selectedQuarter" class="block">Trimestre:</label>
                                    <select wire:model="trimestres" multiple id="selectedQuarter"
                                        class="p-2 pe-8 border border-gray-300 rounded w-fit">
                                        <option value="1">1º Trimestre</option>
                                        <option value="2">2º Trimestre</option>
                                        <option value="3">3º Trimestre</option>
                                        <option value="4">4º Trimestre</option>
                                    </select>
                                </div>
                            @endif

                        @endif
                    </div>
                    @if ($showAno)
                        <!-- Botão de consulta -->
                        <div class="flex gap-2 mt-2">
                            <button type="submit" class="btn btn-primary">Consultar</button>
                        </div>
                    @endif
                </form>
            </div>
            <!-- #endregion -->


            <div class="overflow-x-auto">

                <div class="overflow-x-auto max-w-full">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th class="text-left p-2 bg-white sticky left-0 z-10 border-r border-gray-300">

                                    Demonstração do resultado do exercício
                                </th>
                                @foreach ($periodos as $periodo)
                                    <th class="border border-gray-300 px-4 py-2 whitespace-nowrap bg-white">
                                        {{ $periodo }}
                                        <button wire:click="apagarAno('{{ $periodo }}')"
                                            wire:loading.attr="disabled" wire:key="btn-apagar-ano-{{ $periodo }}"
                                            class="ml-2 text-red-500 hover:text-red-700"
                                            title="Apagar dados do ano {{ $periodo }}">
                                            🗑️
                                        </button>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dados as $item)
                                <tr class='categoria-row hover-highlight'>
                                    <td
                                        class='border border-b px-4 py-1 ps-{{ $item['categoria']->nivel }} font-{{ $item['categoria']->type }} bg-slate-100 sticky left-0 z-10 border-r border-gray-300 whitespace-nowrap'>
                                        {{ $item['categoria']->name }}
                                    </td>
                                    @foreach ($periodos as $periodo)
                                        <td class='py-1 px-2 text-right border border-b whitespace-nowrap'
                                            wire:loading.class="opacity-50 bg-slate-100">
                                            @if (isset($item['valores'][$periodo]))
                                                {{ number_format((float) str_replace(',', '.', str_replace('.', '', $item['valores'][$periodo])), 2, ',', '.') }}
                                            @else
                                                <span class="opacity-20">0</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('show-log', () => {});
</script>

@section('script')
    <script></script>
@endsection
