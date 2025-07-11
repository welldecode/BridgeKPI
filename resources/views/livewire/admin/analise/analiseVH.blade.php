<div>

    <x-slot name="header">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                            Visão geral
                        </div>
                        <h2 class="page-title">
                            Análise Vertical e Horizontal
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
    <div>
        <div class="card">
            <div class="mt-5 px-4 mb-4">
                <div class=" flex items-center   space-x-4 ">
                    <div> <label for="anos" class="block">Ano:</label>
                        <select id="anos" wire:model="anos" class="border px-2 py-1 rounded">
                            @for ($y = date('Y'); $y >= date('Y') - 25; $y--)
                                <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label for="type" class="block">Tipo de Inserção:</label>
                        <select wire:model="type" id="type" class="border px-2 py-1 rounded">
                            <option value="mensal">Mensal</option>
                            <option value="trimestral">Trimestral</option>
                            <option value="anual">Anual</option>
                        </select>

                    </div>
{{-- Se for MENSAL, mostrar select de mês --}}
@if ($type === 'mensal')
    <div>
        <label for="mesSelecionado" class="block">Mês:</label>
        <select wire:model="mesSelecionado" id="mesSelecionado" class="border px-2 py-1 rounded">
            <option value="1">Janeiro</option>
            <option value="2">Fevereiro</option>
            <option value="3">Março</option>
            <option value="4">Abril</option>
            <option value="5">Maio</option>
            <option value="6">Junho</option>
            <option value="7">Julho</option>
            <option value="8">Agosto</option>
            <option value="9">Setembro</option>
            <option value="10">Outubro</option>
            <option value="11">Novembro</option>
            <option value="12">Dezembro</option>
        </select>
    </div>
@endif

{{-- Se for TRIMESTRAL, mostrar select de trimestre --}}
@if ($type === 'trimestral')
    <div>
        <label for="trimestreSelecionado" class="block">Trimestre:</label>
        <select wire:model="trimestreSelecionado" id="trimestreSelecionado" class="border px-2 py-1 rounded">
            <option value="1">1º Trimestre (Jan-Mar)</option>
            <option value="2">2º Trimestre (Abr-Jun)</option>
            <option value="3">3º Trimestre (Jul-Set)</option>
            <option value="4">4º Trimestre (Out-Dez)</option>
        </select>
    </div>
@endif
                    <div>
                        <label for="order" class="block">Tipo de Analise:</label>
                        <select id="order" wire:model="order" class="border px-2 py-1 rounded">
                            <option value="bp">Balanço Patrimonial</option>
                            <option value="dre">DRE</option>
                        </select>
                    </div>
                </div>
                <button wire:click="getDados" type="submit" class="mt-2 btn btn-primary">Calcular</button>

                <div wire:loading class="text-blue-600 mt-2">Calculando...</div>
            </div>
        </div>

        <table class="table-auto w-full text-sm border">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="px-2 py-1">Categoria</th>
                    <th class="px-2 py-1">{{ $anos}}</th>
                    <th class="px-2 py-1">{{ $anos - 1}}</th>
                    <th class="px-2 py-1">AV (%)</th>
                    <th class="px-2 py-1">AH (%)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($dados as $item)
                    <tr class="border-b">
                        <td class="border px-4 py-1 ps-{{ $item['nivel'] }} font-{{ $item['type'] }} bg-slate-100">
                            {{ $item['name'] }}
                        </td>

                        {{-- Valor atual (BP ou DRE) --}}
                        <td class="px-2 py-1">
                            {{ number_format($item['bp']['value'] ?? $item['dre']['value'], 2, ',', '.') }}
                        </td>

                        {{-- Valor anterior --}}
                        <td class="px-2 py-1">
                            {{ number_format($item['anterior'] ?? 0, 2, ',', '.') }}
                        </td>

                        {{-- Análise Vertical --}}
                        <td class="px-2 py-1">
                            @if (!is_numeric($item['av']) || is_nan($item['av']) || is_infinite($item['av']))
                                N/A
                            @else
                                {{ number_format($item['av'], 2, ',', '.') }}%
                            @endif
                        </td>

                        {{-- Análise Horizontal --}}
                        <td class="px-2 py-1">
                            @if (is_null($item['ah']))
                                N/A
                            @else
                                {{ number_format($item['ah'], 2, ',', '.') }}%
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>

@section('script')
@endsection
