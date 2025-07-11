<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Indicadores</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: center;
        }
        th {
            background-color: #eee;
            font-weight: bold;
        }
        .grupo-title {
            text-align: left;
            font-size: 16px;
            font-weight: bold;
            padding: 10px 0;
        }
        .comentario {
            background-color: #f4f4f4;
            text-align: left;
            padding: 6px;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h1 style="text-align: center;">Relatório de Indicadores Econômico-Financeiros</h1>

    @foreach ($indicadoresPorGrupo as $grupo => $indicadores)
        @if (!empty($indicadores))
            <div class="grupo-title">{{ $grupo }}</div>

            <table>
                <thead>
                    <tr>
                        <th>Indicador</th>
                        @foreach ($anos as $ano)
                            <th>{{ $ano }}</th>
                        @endforeach
                    </tr>
                </thead>

                <tbody>
                    @foreach ($indicadores as $nome => $dados)
                        @php
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
                            $nomeFormatado = \Str::title(str_replace('_', ' ', $nome)); // simples fallback
                        @endphp
                        <tr>
                            <td style="text-align: left;">{{ $nomeFormatado }}</td>
                            @foreach ($anos as $ano)
                                @php $valor = $dados['valores'][$ano] ?? null; @endphp
                                <td>
                                    {{ $valor !== null && $valor !== 'N/A'
                                        ? ($formatarComoPorcentagem
                                            ? number_format((float) $valor, 2, ',', '.') . '%'
                                            : number_format((float) $valor, 2, ',', '.'))
                                        : '-' }}
                                    <br>
                                    {{ $setas[$nome] ?? '' }}
                                </td>
                            @endforeach
                        </tr>

                        @if (!empty($comentariosPorIndicador[$nome]))
                            <tr>
                                <td class="comentario" colspan="{{ count($anos) + 1 }}">
                                    <strong>Análise de {{ $anoAtual }}:</strong>
                                    {{ $comentariosPorIndicador[$nome] }}
                                </td>
                            </tr>
                        @endif
                    @endforeach
                </tbody>

                @if (!empty($comentariosPorGrupo[$grupo]) && is_string($comentariosPorGrupo[$grupo]))
                    <tfoot>
                        <tr>
                            <td class="comentario" colspan="{{ count($anos) + 1 }}">
                                <strong>Análise de {{ $anoAtual }}:</strong>
                                {{ $comentariosPorGrupo[$grupo] }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        @endif
    @endforeach

</body>
</html>