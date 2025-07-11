<div class="">
    <x-slot name="header">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                            Analise
                        </div>
                        <h2 class="page-title"> Relatório de Indicadores Econômico-Financeiros
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="card">
            <div class="card-header items-end justify-end">

                <button wire:click="gerarPdf" class="btn btn-primary">
                    Baixar PDF
                </button>
            </div>
            @foreach ($indicadoresPorGrupo as $grupo => $indicadores)
                @if (!empty($indicadores))
                    <table class="w-full rounded-lg text-sm">
                        <thead class="text-left text-sm font-bold w-full">
                            <tr>
                                <th class="px-2">{{ $grupo }}</th>
                                @foreach ($anos as $ano)
                                    <th class="p-2 text-center" style="width: 160px;">{{ $ano }}</th>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody class="w-full">
                            @foreach ($indicadores as $nome => $dados)
                                @php
                                    $linhaEspecial = false; // coloque sua regra
                                  $formatarComoPorcentagem = in_array($nome, [
                                                            'margem_líquida',
                                                            'depreciação_como_porcentagem_da_ROL',
                                                            'margem_contribuicao_percent_rol',
                                                            'margem_ebitda',
                                                            'margem_ebit',
                                                            'margem_nopat',
                                                            'custos_e_despesas_fixas_porcental_do_rol',
                                                            'custos_e_despesas_variáveis_porcental',
                                                            'retorno_sobre_patrimonio_liquido_roe',
                                                            'retorno_sobre_o_ativo_ROA_conceito_NOPAT',
                                                            'retorno_sobre_o_capital_investido_ROIC_conceito_NOPAT',
                                                            'margem_bruta',
                                                            'margem_operacional_nopat',
                                                            'roic',
                                                            'rentabilidade_roe',
                                                            'roe_retorno_sobre_o_pl',
                                                            'margem_líquida_ll_rol',
                                                            'ganho_pela_alavancagem_financeira',
                                                            'taxa_de_juros_sobre_empréstimos_e_financiamentos',
                                                        ]);
                                    $nomeFormatado = $this->formatarNomeIndicador($nome);
                                @endphp

                                <tr
                                    class="{{ $loop->first ? 'border-t-4 border-blue-700' : 'border-t border-gray-200' }}">
                                    <td class="p-2 {{ $linhaEspecial ? 'font-bold' : '' }}">
                                        <div class="text-base mb-1 font-semibold text-zinc-700">
                                            {{ $nomeFormatado }}
                                        </div>
                                    </td>

                                    @foreach ($anos as $ano)
                                        @php $valor = $dados['valores'][$ano] ?? null; @endphp
                                        <td class="text-center align-middle py-2">
                                            <div class="text-base font-bold">
                                                {{ $valor !== null && $valor !== 'N/A'
                                                    ? ($formatarComoPorcentagem
                                                        ? number_format((float) $valor, 2, ',', '.') . '%'
                                                        : number_format((float) $valor, 2, ',', '.'))
                                                    : '' }}
                                            </div>
                                            <div class="text-2xl mt-1 font-bold">{{ $setas[$nome] ?? '' }}</div>
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Comentário individual --}}
                                @if (!empty($comentariosPorIndicador[$nome]))
                                    <tr class="bg-[#f4f4f4] text-sm text-gray-600">
                                        <td colspan="{{ count($anos) + 1 }}" class="p-2.5 whitespace-normal">
                                            <strong>Análise de {{ $anoAtual }}:</strong>
                                            <span>{{ $comentariosPorIndicador[$nome] }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>

                        {{-- Comentário agregado por grupo (se existir) --}}

                        @if (!empty($comentariosPorGrupo[$grupo]) && is_string($comentariosPorGrupo[$grupo]))
                            <tfoot>
                                <tr class="bg-[#f4f4f4] text-sm text-gray-600">
                                    <td colspan="{{ count($anos) + 1 }}" class="p-2.5 whitespace-normal">
                                        <strong>Análise de {{ $anoAtual }}:</strong>
                                        <span>{{ $comentariosPorGrupo[$grupo] }}</span>
                                    </td>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                @endif
            @endforeach
        </div>
    </div>
</div>
