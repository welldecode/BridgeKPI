<?php

namespace App\Livewire\Admin\Analise;

use Illuminate\Support\Facades\Cache;
use Livewire\Component;
use App\Models\Indicator;
use Barryvdh\DomPDF\Facade\Pdf;


class Relatorio extends Component
{

    protected function regraFlecha(string $nomeIndicador): string
    {
        return match ($nomeIndicador) {
            'custos_e_despesas_fixas_porcental_do_rol',
            'custos_e_despesas_variaveis_porcental',
            'ponto_de_equilíbrio_contábil',
            'prazo_medio_estocagem',
            'grau_de_imobilização_de_recursos_permanentes' => 'inverso',

            'receita_operacional_bruta_(ROB)' => 'especial_rob',

            // Indicadores sem flecha
            'grau_alavancagem_operacional' => 'sem',
            'grau_alavancagem_total' => 'sem',
            'depreciação_como_porcentagem_da_ROL' => 'sem',
            'deduções_da_receita_bruta_(ROB)' => 'sem',

            // Definindo as regras normais: ↑ aumento, ↓ queda
            'receita_operacional_líquida_(ROL)' => 'normal',
            'lucro_líquido_do_exercício' => 'normal',
            'margem_líquida' => 'normal',
            'margem_líquida_ll_rol' => 'normal',
            'patrimônio_líquido' => 'normal',
            'retorno_sobre_patrimonio_liquido_roe' => 'normal',
            'capital_circulante_líquido' => 'normal',
            'liquidez_corrente' => 'normal',
            'liquidez_seca' => 'normal',
            'liquidez_imediata' => 'normal',
            'liquidez_geral' => 'normal',
            'giro_estoque' => 'normal',
            'prazo_medio_pagamento' => 'normal',
            'giro_contas_pagar' => 'normal',
            'prazo_medio_cobranca' => 'normal',
            'giro_valores_receber' => 'normal',
            'giro_ativo' => 'normal',
            'giro_investimento' => 'normal',
            'giro_patrimonio_liquido' => 'normal',
            'ciclo_operacional' => 'normal',
            'ciclo_financeiro' => 'normal',
            'retorno_sobre_o_ativo_ROA_conceito_NOPAT' => 'normal',
            'retorno_sobre_o_capital_investido_ROIC_conceito_NOPAT' => 'normal',
            'margem_bruta' => 'normal',
            'margem_de_contribuição' => 'normal',
            'margem_contribuicao_percent_rob' => 'normal',
            'margem_contribuicao_percent_rol' => 'normal',
            'margem_operacional_nopat' => 'normal',
            'EBITDA' => 'normal',
            'EBIT_Lucratividade' => 'normal',
            'nopat' => 'normal',
            'margem_ebitda' => 'normal',
            'margem_nopat' => 'normal',
            'gaf' => 'normal',

            // Caso algum indicador não esteja mapeado
            default => 'normal',
        };
    }
    protected function gruposIndicadores(): array
    {
        $definicao = [
            [
                'nome' => 'Principais Indicadores',
                'itens' => ['receita_operacional_líquida_(ROL)', 'receita_operacional_bruta_(ROB)', 'deduções_da_receita_bruta_(ROB)', 'resultado_de_Op_desc_Resultado_Não_Operacionais', 'ponto_de_equilíbrio_contábil', 'patrimônio_líquido', 'retorno_sobre_patrimonio_liquido_roe'],
                'comentarioTipo' => 'individual',
                'tipoDeValor' => 'atual', // aqui você indica qual valor quer usar

            ],
            [
                'nome' => 'Principais Indicadores',
                'itens' => ['custos_e_despesas_fixas', 'custos_e_despesas_fixas_porcental_do_rol'],
                'comentarioTipo' => 'agregado',
            ],
            [
                'nome' => 'Principais Indicadores',
                'itens' => ['custos_e_despesas_variaveis', 'custos_e_despesas_variaveis_porcental'],
                'comentarioTipo' => 'agregado',
            ],
            [
                'nome' => 'Principais Indicadores',
                'itens' => ['lucro_líquido_do_exercício', 'margem_líquida'],
                'comentarioTipo' => 'agregado',
            ],
            [
                'nome' => 'Análise do Grau de Alavancagem Financeira (GAF)',
                'itens' => ['gaf', 'roe_retorno_sobre_o_pl', 'roic', 'ganho_pela_alavancagem_financeira'],
                'comentarioTipo' => 'agregado',
                'tipoDeValor' => 'percentual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $valores) {
                    $roe = $valores['roe_retorno_sobre_o_pl'] ?? 0;
                    $roic = $valores['roic'] ?? 0;
                    $ganho_pela_alavancagem_financeira = $valores['ganho_pela_alavancagem_financeira'] ?? 0;
                    return 'O Retorno total do acionista (ROE) foi de ' . number_format($roe, 2, ',', '.') . '% no período. O Ganho do acionista sobre o capital próprio investido no negócio (ROIC) foi de ' . number_format($roic, 2, ',', '.') . '%. . O Ganho do acionista pela alavancagem financeira favorável, ou seja, tomar emprestado a um custo inferior do retorno da aplicação dos recursos foi de ' . number_format($ganho_pela_alavancagem_financeira, 2, ',', '.') . '%';
                }
            ],
            [
                'nome' => 'Fluxo de Caixa Livre da Firma (FCFF)',
                'itens' => ['EBIT', 'ir_e_csll', 'no_pat', 'depreciacao_e_amortização', 'fluxo_de_caixa_operacional', 'investimentos_fixos', 'investimento_em_capital_de_giro'],
                'comentarioTipo' => 'agregado',
                'tipoDeValor' => 'percentual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $valores) {
                    $investimento_em_capital_de_giro = $valores['investimento_em_capital_de_giro'] ?? 0;
                    return 'O Fluxo de Caixa Livre da Firma (FCFF), valor da geração de caixa deduzido de todos os investimentos da firma foi de R$  ' . number_format($investimento_em_capital_de_giro, 2, ',', '.') . ' no período. Este valor também é usado para fins de avaliação da empresa (VALUATION), para este fim, o FCFF deverá ser projetado para vários períodos e descontado a um custo médio ponderado de capital (WACC).';
                }
            ],
            [
                'nome' => 'Indicadores de Liquidez',
                'itens' => ['capital_circulante_líquido', 'liquidez_corrente', 'liquidez_seca', 'liquidez_imediata', 'liquidez_geral'],
                'comentarioTipo' => 'individual',
                'tipoDeValor' => 'atual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $comentarios) {
                    return 'Em resumo, os dois principais indicadores mostram tendência positiva.';
                }
            ],
            [
                'nome' => 'Indicadores de Atividade',
                'itens' => ['prazo_medio_estocagem', 'giro_estoque', 'prazo_medio_pagamento', 'giro_contas_pagar', 'prazo_medio_cobranca', 'giro_valores_receber', 'giro_ativo', 'giro_investimento', 'giro_patrimonio_liquido', 'ciclo_operacional', 'ciclo_financeiro'],
                'comentarioTipo' => 'individual',
                'tipoDeValor' => 'atual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $comentarios) {
                    return 'Em resumo, os dois principais indicadores mostram tendência positiva.';
                }
            ],
            [
                'nome' => 'Indicadores de Endividamento e Estrutura',
                'itens' => ['cap_de_terceiros_capital_proprio', 'cap_de_terceiros_passivo_total', 'grau_de_imobilização_de_recursos_permanentes', 'alococao_de_recursos_permanentes_no_cp', 'indicador_de_cobertura_de_juros_conceito_ebit', 'indicador_de_cobertura_de_juros_conceito_EBITDA', 'taxa_de_juros_sobre_empréstimos_e_financiamentos', 'depreciação_como_porcentagem_da_ROL', 'CAPEX_Depreciação'],
                'comentarioTipo' => 'individual',
                'tipoDeValor' => 'atual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $comentarios) {
                    return 'Em resumo, os dois principais indicadores mostram tendência positiva.';
                }
            ],
            [
                'nome' => 'Indicadores de Rentabilidade e Lucratividade',
                'itens' => ['retorno_sobre_o_ativo_ROA_conceito_NOPAT', 'retorno_sobre_o_capital_investido_ROIC_conceito_NOPAT', 'ROI_Publicidade', 'margem_bruta', 'margem_de_contribuição', 'margem_contribuicao_percent_rob', 'margem_contribuicao_percent_rol', 'margem_operacional_nopat', 'EBITDA', 'EBIT_Lucratividade', 'nopat', 'margem_ebitda', 'margem_ebit', 'margem_nopat'],
                'comentarioTipo' => 'individual',
                'tipoDeValor' => 'atual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $comentarios) {
                    return 'Em resumo, os dois principais indicadores mostram tendência positiva.';
                }
            ],
            [
                'nome' => 'Graus de Alavancagem',
                'itens' => ['grau_alavancagem_operacional', 'grau_alavancagem_financeira', 'grau_alavancagem_total'],
                'comentarioTipo' => 'individual',
                'tipoDeValor' => 'atual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $comentarios) {
                    return 'Em resumo, os dois principais indicadores mostram tendência positiva.';
                }
            ],
            [
                'nome' => 'Identidade DuPont',
                'itens' => ['margem_líquida_ll_rol', 'giro_do_ativo_total', 'multiplicadorPl', 'rentabilidade_roe'],
                'comentarioTipo' => 'agregado',
                'tipoDeValor' => 'percentual', // aqui você indica qual valor quer usar
                'fnAgregado' => function (array $valores) {
                    $roe = $valores['rentabilidade_roe'] ?? 0;
                    return 'O ROE foi de ' . number_format($roe, 2, ',', '.') . '%  no período. A Rentabilidade ROE é composta de Margem Líquida, Giro do Ativo Total e Multiplicador do Patrimônio Líquido. ";"Quanto maior a margem (eficiência operacional), maior o giro (eficiência no uso dos ativos) e menor o capital próprio empregado (alavancagem financeira), maior será a rentabilidade. ";"Importante verificar se o GAF é superior a 1 para que a maior alavancagem financeira não tenha impacto suficientemente negativo sobre a margem líquida reduzindo o ROE.';
                }
            ],
        ];

        /* -------------------------------------------------------------
         * Inverte para: indicador => ['grupo' => …, 'subgrupo' => …]
         * ----------------------------------------------------------- */
        $map = [];
        foreach ($definicao as $grupo) {
            foreach ($grupo['itens'] as $indicador) {
                $map[$indicador] = [
                    'grupo' => $grupo['nome'],
                ];
            }
        }

        return [
            'definicao' => $definicao,
            'map' => $map,
        ];
    }

    /* =============================================================== */
    /*  Comentários por indicador / grupo                              */
    /* =============================================================== */
    protected function comentariosIndicadores(): array
    {
        return [
            'resultado_de_Op_desc_Resultado_Não_Operacionais' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return ' O resultado de operações descontinuadas mais resultado de outras receitas não operacionais foi de ' . $unit . number_format($atual, 2, ',', '.') . ' no período.';
            },
            'receita_operacional_bruta_(ROB)' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A empresa auferiu ' . $unit . number_format($atual, 2, ',', '.') . ' de receita operacional bruta (ROB) no período.';
            },
            'deduções_da_receita_bruta_(ROB)' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return ' A empresa teve ' . $unit . number_format($atual, 2, ',', '.') . ' de Deducões da Receita Bruta no período. As DEDUÇÕES DA RECEITA BRUTA correspondem basicamente a Devoluções de Vendas, Descontos, e Impostos e Contribuições Incidentes sobre Vendas.';
            },
            'receita_operacional_líquida_(ROL)' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A empresa auferiu ' . number_format($atual, 2, ',', '.') . $unit . ' de receita operacional líquida (ROL) no período.';
            },
            'custos_e_despesas_fixas_porcental_do_rol' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "Os custos e despesas fixas do período foram de {$unit} " . number_format((float) $atual, 2, ',', '.');
                $comentario .= ". E " . number_format((float) $percentual, 2, ',', '.') . "% da receita do período se refere a custos e despesas fixas.";
                return $comentario;
            },
            'custos_e_despesas_variaveis_porcental' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "Os custos e despesas variáveis do período foram de {$unit} " . number_format((float) $atual, 2, ',', '.');
                $comentario .= ". E " . number_format((float) $percentual, 2, ',', '.') . "% da receita do período se refere a custos e despesas variáveis.";
                return $comentario;
            },
            'ponto_de_equilíbrio_contábil' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "A empresa precisa ter uma receita líquida no período superior a {$unit} " . number_format((float) $atual, 2, ',', '.') . 'mais o valor de Impostos e Contribuições Incidentes sobre Vendas para a partir de então começar a ter lucro líquido. Importante: Basicamente o valor da conta Impostos e Contribuições Incidentes sobre Vendas precisa ser acrescentado para que se chegue efetivamente na Receita Bruta de equilíbrio da empresa.';
                return $comentario;
            },
            'margem_líquida' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "O lucro líquido do período foi de  {$unit} " . number_format((float) $atual, 2, ',', '.') . ' Para cada R$ 100 de receita operacional líquida ' . number_format((float) $percentual, 2, ',', '.') . ' se converteu em lucro líquido.';
                return $comentario;
            },
            'patrimônio_líquido' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "O Patrimônio Líquido no período é de {$unit} " . number_format((float) $atual, 2, ',', '.') . '.';
                return $comentario;
            },
            'retorno_sobre_patrimonio_liquido_roe' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "Para cada R$ 100,00 de patrimônio líquido investidos na empresa são gerados {$unit} " . number_format((float) $atual, 2, ',', '.') . ' em lucro líquido do período.';
                return $comentario;
            },
            'capital_circulante_líquido' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = " Os bens e direitos da empresa que se convertem em dinheiro no prazo de até um ano são maiores que as obrigações que vencem em até um ano no montante de {$unit} " . number_format((float) $atual, 2, ',', '.') . '.Sendo positivo, este valor pode significar certo conforto na posição de liquidez da empresa.';
                return $comentario;
            },
            'liquidez_corrente' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "Para cada R$ 100,00 de dívidas que vencem em até um ano, a empresa terá no prazo de até um ano {$unit} " . number_format((float) $atual, 2, ',', '.') . ' em recursos para honrar esses compromissos. Sendo este indicador menor do que 1 existe um certo desconforto na posição de liquidez da empresa.';
                return $comentario;
            },
            'liquidez_seca' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "Para cada R$ 100,00 de dívidas que vencem em até um ano, a empresa terá no prazo de até um ano {$unit} " . number_format((float) $atual, 2, ',', '.') . ' em recursos para honrar esses compromissos sem que realize novas vendas.';
                return $comentario;
            },
            'liquidez_imediata' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "Para cada R$ 100,00 de dívidas que vencem em até um ano, a empresa terá no prazo de até um ano {$unit} " . number_format((float) $atual, 2, ',', '.') . ' em recursos para honrar esses compromissos apenas dispondo do que possui em caixa, bancos e aplicações financeiras de liquidez imediata, sem que realize novas vendas. Normalmente este valor é inferior a R$ 100,00, uma vez que é atípico uma empresa estar numa situação de liquidez suficiente para que possa parar de vender e também ficar sem receber as vendas já realizadas por um ano';
                return $comentario;
            },
            'liquidez_geral' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                $comentario = "No caso de a empresa converter em caixa todos seus bens e direitos, exceto os permanentes, para cada R$ 100,00 de dívidas, a empresa terá {$unit} " . number_format((float) $atual, 2, ',', '.') . ' em recursos para honrar todos os seus compromissos com terceiros. Importante estar atendo ao fato de que todos os índicadores de liquidez revelam a capacidade da empresa em pagar apenas as dívidas existentes até o fechamento do balanço.';
                return $comentario;
            },
            'prazo_medio_estocagem' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Os produtos e mercadorias ficam estocados cerca de {$unit} " . number_format((float) $atual, 2, ',', '.') . ' dias até serem vendidos.';
            },
            'giro_estoque' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Os estoques giraram {$unit} " . number_format((float) $atual, 2, ',', '.') . '  vezes no período.';
            },
            'prazo_medio_pagamento' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Entre o recebimento das compras de insumos e mercadorias e o pagamento dos fornecedores decorre cerca de  {$unit} " . number_format((float) $atual, 2, ',', '.') . ' dias.';
            },
            'giro_contas_pagar' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "As contas a pagar se renovam aproximadamente {$unit} " . number_format((float) $atual, 2, ',', '.') . ' no período.';
            },
            'prazo_medio_cobranca' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Entre as vendas e o recebimento dos valores de clientes decorre cerca de {$unit} " . number_format((float) $atual, 2, ',', '.') . ' dias. Este indicador considera que as vendas realizadas que formam a receita líquida foram realizadas a prazo.';
            },
            'giro_valores_receber' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "As contas a receber se renovam aproximadamente {$unit} " . number_format((float) $atual, 2, ',', '.') . ' no período.';
            },
            'giro_ativo' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Um valor equivalente a {$unit} " . number_format((float) $atual, 2, ',', '.') . ' de todo o ativo foi vendido no período.';
            },
            'giro_investimento' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Os valores investidos, sejam proveniente de empréstimos e financiamentos ou próprios, giraram cerca de {$unit} " . number_format((float) $atual, 2, ',', '.') . ' vezes no período.';
            },
            'giro_patrimonio_liquido' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Os valores investidos pelos sócios da empresa giraram cerca de {$unit} " . number_format((float) $atual, 2, ',', '.') . ' vezes no período.';
            },
            'ciclo_operacional' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Entre as compras de insumos e mercadorias junto aos fornecedores e o recebimento das vendas realizadas decorreu cerca de {$unit} " . number_format((float) $atual, 2, ',', '.') . ' dias.';
            },
            'ciclo_financeiro' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "Entre o pagamento referente as compras de insumos e mercadorias junto aos fornecedores e o recebimento das vendas realizadas decorreu cerca de {$unit} " . number_format((float) $atual, 2, ',', '.') . ' dias.';
            },
            'cap_de_terceiros_capital_proprio' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "O capital de terceiros representado pelos passivos da empresa estão na relação de {$unit} " . number_format((float) $atual, 2, ',', '.') . ' do capital próprio investido. Este indicador quando alto pode representar risco financeiro para empresa, como pode representar oportunidade de rentabilidade quando GAF superior a 1 (um). É importante sobretudo acompanhar sua evolução e saber se seu crescimento é resultado de escolhas deliberadas da empresa ou reflete descontrole dos passivos.';
            },
            'cap_de_terceiros_passivo_total' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return "As dívidas e obrigações da empresa são  {$unit} " . number_format((float) $atual, 2, ',', '.') . '  de todo o capital que existe na empresa. Este indicador quando alto pode representar risco financeiro para empresa, como pode representar oportunidade de rentabilidade quando GAF superior a 1 (um). É importante sobretudo acompanhar sua evolução e saber se seu crescimento é resultado de escolhas deliberadas da empresa ou reflete descontrole dos passivos.';
            },
            'grau_de_imobilização_de_recursos_permanentes' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return $unit . number_format((float) $atual, 2, ',', '.') . ' dos recursos com exigibilidade de longo prazo (ou seja, passivo não circulante e patrimônio líquido) se encontram financiando o ativo de longo prazo. Se este valor for maior que 100% significa que existem recursos de curto prazo financiando ativos de longo prazo. Situação que não é desejável para o equilíbrio financeiro da empresa.';
            },
            'alococao_de_recursos_permanentes_no_cp' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return $unit . number_format((float) $atual, 2, ',', '.') . ' dos recursos com exigibilidade de longo prazo (ou seja, passivo não circulante e patrimônio líquido) se encontram financiando o ativo de curto prazo.';
            },
            'indicador_de_cobertura_de_juros_conceito_ebit' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'O lucro antes da dedução das despesas financeiras e do Imposto de Renda e CSLL é ' . $unit . number_format((float) $atual, 2, ',', '.') . ' vezes maior que as despesas financeiras.';
            },
            'indicador_de_cobertura_de_juros_conceito_EBITDA' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return ' O lucro antes da dedução das despesas financeiras, do Imposto de Renda e CSLL, e Depreciação e Amortiação é ' . $unit . number_format((float) $atual, 2, ',', '.') . ' vezes maior que as despesas financeiras.';
            },
            'taxa_de_juros_sobre_empréstimos_e_financiamentos' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A taxa de juros sobre empréstimos e financiamentos foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' no período.';
            },
            'depreciação_como_porcentagem_da_ROL' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A depreciação corresponde a uma proporção de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' da receita operacional líquida.';
            },
            'CAPEX_Depreciação' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'Os gasto de aquisição de imobilizado e intangível no período correspondem a ' . $unit . number_format((float) $atual, 2, ',', '.') . ' da Depreciação. Se o indicador for maior que 100% indica que a empresa está mais que repondo a depreciação do período.';
            },
            'retorno_sobre_o_ativo_ROA_conceito_NOPAT' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'No período foi possível auferir um percentual de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' de lucro operacional descontado os impostos, sobre o ativo total da empresa.';
            },
            'retorno_sobre_o_capital_investido_ROIC_conceito_NOPAT' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'No período foi possível auferir um percentual de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' de lucro operacional descontado os impostos, sobre os valores de empréstimos e capital próprio investidos na empresa.';
            },
            'ROI_Publicidade' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'Para cada R$ 1,00 investido em publicidade e propaganda foi possível auferir ' . $unit . number_format((float) $atual, 2, ',', '.') . ' de margem de contribuição.';
            },
            'margem_bruta' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'O lucro bruto corresponde a ' . $unit . number_format((float) $atual, 2, ',', '.') . ' da receita operacional líquida.';
            },
            'margem_de_contribuição' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A margem de contribuição, que é a receita operacional líquida menos custos e despesas variáveis foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' no período.';
            },
            'margem_contribuicao_percent_rob' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A margem de contribuição corresponde a ' . $unit . number_format((float) $atual, 2, ',', '.') . ' da receita operacional bruta.';
            },
            'margem_contribuicao_percent_rol' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A margem de contribuição corresponde a ' . $unit . number_format((float) $atual, 2, ',', '.') . ' da receita operacional líquida.';
            },
            'margem_operacional_nopat' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'Para cada R$ 100,00 de receita operacional líquida ' . $unit . number_format((float) $atual, 2, ',', '.') . ' se converteram em lucro operacional descontado os impostos.';
            },
            'EBITDA' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'O lucro antes das deduções de despesas financeira, IR e CSLL, depreciação e amortização foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' no período.';
            },
            'EBIT_Lucratividade' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'O lucro antes das deduções de despesas financeira e IR e CSLL, também chamado de lucro operacional, foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' no período.';
            },
            'nopat' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'O NOPAT, lucro operacional (EBIT) deduzido de IR e CSLL, foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . ' no período.';
            },
            'margem_ebitda' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'Para cada R$ 100,00 de receita operacional líquida ' . $unit . number_format((float) $atual, 2, ',', '.') . ' se converteram em EBITDA.';
            },
            'margem_ebit' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'Para cada R$ 100,00 de receita operacional líquida ' . $unit . number_format((float) $atual, 2, ',', '.') . ' se converteram em EBIT.';
            },
            'margem_nopat' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'Para cada R$ 100,00 de receita operacional líquida ' . $unit . number_format((float) $atual, 2, ',', '.') . ' se converteram em NOPAT.';
            },
            'grau_alavancagem_operacional' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A variação percentual do lucro operacional em relação a variação percentual do nível de atividade da empresa foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . '. Um baixo GAO indica uma presença relativamente baixa de custos fixos em relação aos custos variáveis.';
            },
            'grau_alavancagem_financeira' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A variação percentual do lucro líquido em relação a variação percentual do lucro operacional foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . '.GAF superior a 1 indica que tomar empréstimos para investir na empresa é vantajoso e aumenta a rentabilidade para os sócios. Quando inferior a 1 indica redução da rentabilidade para os sócios.';
            },
            'grau_alavancagem_total' => function (float $atual, string $unit = '%', ?float $percentual = null): string {
                return 'A variação percentual do lucro líquido em relação a variação percentual do nível de atividade da empresa foi de ' . $unit . number_format((float) $atual, 2, ',', '.') . '.Ou seja, para cada 1% de aumento da receita o lucro líquido aumentou ' . $unit . number_format((float) $atual / 100, 2, ',', '.');
            },
        ];
    }

    /* =============================================================== */
    /*  Geração de comentário p/ cada indicador                        */
    /* =============================================================== */
    public function gerarComentarioArray(
        array $dados,
        array $funcoesIndicador,
        array $grupos
    ): array {

        $comentariosIndividuais = [];

        // Para ajudar a achar o tipoDeValor do indicador
        $mapaIndicadorParaTipoValor = [];

        foreach ($grupos as $grupo) {
            $tipoDeValor = $grupo['tipoDeValor'] ?? 'atual';
            foreach ($grupo['itens'] as $ind) {
                $mapaIndicadorParaTipoValor[$ind] = $tipoDeValor;
            }
        }

        // 1. Dispara a função de cada indicador
        foreach ($dados as $indicador => $info) {
            if (!isset($funcoesIndicador[$indicador])) {
                continue; // sem função, ignora
            }

            $fn = $funcoesIndicador[$indicador];

            // Descobre qual valor pegar
            $tipoValor = $mapaIndicadorParaTipoValor[$indicador] ?? 'atual';
            $valorParaComentario = $info[$tipoValor] ?? ($info['atual'] ?? 0);

            // Passa: valor, unidade, percentual (se existir)
            $comentariosIndividuais[$indicador] = $fn(
                $valorParaComentario,
                $info['unit'] ?? '',
                $info['percentual'] ?? null
            );
        }

        // 2. Processa grupos (igual antes)
        $comentariosPorGrupo = [];
        foreach ($grupos as $grupoCfg) {
            $nomeGrupo = $grupoCfg['nome'];
            $itens = $grupoCfg['itens'];
            $tipo = $grupoCfg['comentarioTipo'] ?? 'individual';

            // Para agregado, montamos array associativo com valores brutos, não comentários textuais
            if ($tipo === 'agregado') {
                // pega os valores "atuais" dos indicadores (ou outro tipo, se você parametrizar)
                $valoresIndicadores = [];
                foreach ($itens as $ind) {
                    $valoresIndicadores[$ind] = $dados[$ind]['atual'] ?? 0.0;
                }

                $comentariosPorGrupo[$nomeGrupo] = isset($grupoCfg['fnAgregado'])
                    ? $grupoCfg['fnAgregado']($valoresIndicadores)
                    : $valoresIndicadores;

            } else {
                // individual: pega os comentários gerados antes
                $indGrupo = array_filter(
                    array_map(fn($ind) => $comentariosIndividuais[$ind] ?? null, $itens)
                );
                $comentariosPorGrupo[$nomeGrupo] = $indGrupo;
            }
        }

        return [
            'porIndicador' => $comentariosIndividuais,
            'porGrupo' => $comentariosPorGrupo,
        ];
    }


    public function calcularSetaArray($nome, $valores, $anoAtual)
    {
        if (!isset($valores[$anoAtual])) {
            return ''; // Sem dado no ano atual, sem seta mesmo
        }

        $anterior = $valores[$anoAtual - 1] ?? null;
        $atual = $valores[$anoAtual];

        // Se não tiver ano anterior, pode escolher como tratar:
        // Por exemplo, seta neutra ou nenhuma seta:
        if ($anterior === null) {
            return '→'; // seta neutra, ou '' para nada
        }
        $regra = $this->regraFlecha($nome);

        if ($regra === 'sem')
            return '';

        if ($regra === 'especial_rob') {
            $rol = session('relatorio_temporario.receita_operacional_líquida_(ROL).valores.' . $anoAtual);
            if ($rol !== null && $atual === $rol)
                return '';
        }

        // Converte para float garantindo operação numérica segura
        $anteriorFloat = (float) str_replace(',', '.', $anterior);
        $atualFloat = (float) str_replace(',', '.', $atual);

        $diff = $atualFloat - $anteriorFloat;

        if ($regra === 'inverso') {
            return abs($diff) < 0.01 * abs($anteriorFloat) ? '→' : ($diff > 0 ? '↓' : '↑');
        }
        if ($regra === 'normal') {
            return abs($diff) < 0.01 * abs($anteriorFloat) ? '→' : ($diff > 0 ? '↑' : '↓');
        }
    }


    protected function formatarNomeIndicador(string $chave): string
    {
        // Substitui underline por espaço
        $nome = str_replace('_', ' ', $chave);

        // Coloca os parênteses com espaçamento correto
        $nome = preg_replace('/\s*\(\s*/', ' (', $nome);
        $nome = preg_replace('/\s*\)\s*/', ')', $nome);

        // Coloca a primeira letra de cada palavra em maiúscula, preservando siglas
        $nome = ucwords($nome);

        // Corrige siglas específicas, como ROL, ROB, EBITDA, ROIC, ROA, NOPAT etc.
        $siglas = ['Rol', 'Rob', 'Roe', 'Roic', 'Roa', 'Ebitda', 'Ebit', 'Nopat', 'Gaf'];
        foreach ($siglas as $sigla) {
            $nome = preg_replace('/\b' . $sigla . '\b/i', strtoupper($sigla), $nome);
        }

        return $nome;
    }

    public function gerarPdf()
    {
        // Mesmo processamento do método render()
        $indicators = Cache::get('relatorio_temporario_' . auth()->id(), []);
        $anos = [];
        if ($indicators) {
            $primeiro = reset($indicators);
            $anos = array_keys($primeiro['valores'] ?? []);
            sort($anos); 
            $anos = array_slice($anos, -4); 
        }
        $anoAtual = end($anos) ?: date('Y');

        $gruposData = $this->gruposIndicadores();
        $definicaoGrupos = $gruposData['definicao'];
        $mapIndicadorGrupo = $gruposData['map'];

        $indicadoresPorGrupo = [];
        $setas = [];
        $dadosParaComentario = [];

        foreach ($definicaoGrupos as $grupoCfg) {
            $nomeGrupo = $grupoCfg['nome'];
            $indicadoresPorGrupo[$nomeGrupo] = [];
        }

        foreach ($indicators as $nome => $dados) {
            $grupo = $mapIndicadorGrupo[$nome]['grupo'] ?? 'Outros';
            $setas[$nome] = $this->calcularSetaArray($nome, $dados['valores'] ?? [], $anoAtual);

            $dadosParaComentario[$nome] = [
                'atual' => $dados['valores'][$anoAtual] ?? 0,
                'unit' => $dados['unit'] ?? '',
                'percentual' => $dados['percentual'] ?? null,
            ];

            if (!isset($indicadoresPorGrupo[$grupo])) {
                $indicadoresPorGrupo[$grupo] = [];
            }

            $indicadoresPorGrupo[$grupo][$nome] = $dados;
        }

        foreach ($definicaoGrupos as $grupoCfg) {
            $nomeGrupo = $grupoCfg['nome'];
            $itensOrdenados = $grupoCfg['itens'];

            $ordenado = [];

            foreach ($itensOrdenados as $indicadorEsperado) {
                if (isset($indicadoresPorGrupo[$nomeGrupo][$indicadorEsperado])) {
                    $ordenado[$indicadorEsperado] = $indicadoresPorGrupo[$nomeGrupo][$indicadorEsperado];
                    unset($indicadoresPorGrupo[$nomeGrupo][$indicadorEsperado]);
                }
            }

            $ordenado += $indicadoresPorGrupo[$nomeGrupo] ?? [];
            $indicadoresPorGrupo[$nomeGrupo] = $ordenado;
        }

        $funcoesIndicador = $this->comentariosIndicadores();
        $comentarios = $this->gerarComentarioArray(
            $dadosParaComentario,
            $funcoesIndicador,
            $definicaoGrupos
        );

        $comentariosPorGrupo = $comentarios['porGrupo'];
        $comentariosPorIndicador = $comentarios['porIndicador'];

        if (isset($indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)'])) {
            $fcff = $indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)'];
            unset($indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)']);
            $indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)'] = $fcff;
        }

        // Renderiza o PDF com a mesma view (ou outra específica)
        $pdf = Pdf::loadView('livewire.admin.analise.relatorio_pdf', [
            'anos' => $anos,
            'anoAtual' => $anoAtual,
            'indicadoresPorGrupo' => $indicadoresPorGrupo,
            'comentariosPorGrupo' => $comentariosPorGrupo,
            'comentariosPorIndicador' => $comentariosPorIndicador,
            'setas' => $setas,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(fn() => print ($pdf->stream()), 'relatorio.pdf');
    }

    /* =============================================================== */
    /*  Render                                                         */
    /* =============================================================== */
    public function render()
    {
        /* 1. Pega dados brutos do cache -------------------------------- */
        $indicators = Cache::get('relatorio_temporario_' . auth()->id(), []);
        $anos = [];
        if ($indicators) {
            $primeiro = reset($indicators);
            $anos = array_keys($primeiro['valores'] ?? []);
            sort($anos);
        }
        $anoAtual = end($anos) ?: date('Y');

        /* 2. Grupos: definição + mapa rápido --------------------------- */
        $gruposData = $this->gruposIndicadores();
        $definicaoGrupos = $gruposData['definicao']; // definição dos grupos com ordem correta
        $mapIndicadorGrupo = $gruposData['map'];

        /* 3. Inicializa arrays para armazenar dados formatados --------- */
        $indicadoresPorGrupo = [];
        $setas = [];
        $dadosParaComentario = [];

        // Inicializa os grupos vazios para preservar ordem
        foreach ($definicaoGrupos as $grupoCfg) {
            $nomeGrupo = $grupoCfg['nome'];
            $indicadoresPorGrupo[$nomeGrupo] = [];
        }

        /* 4. Distribui indicadores dentro dos grupos ------------------- */
        foreach ($indicators as $nome => $dados) {
            $grupo = $mapIndicadorGrupo[$nome]['grupo'] ?? 'Outros';

            // Guarda seta para o indicador
            $setas[$nome] = $this->calcularSetaArray($nome, $dados['valores'] ?? [], $anoAtual);

            // Dados para gerar comentários
            $dadosParaComentario[$nome] = [
                'atual' => $dados['valores'][$anoAtual] ?? 0,
                'unit' => $dados['unit'] ?? '',
                'percentual' => $dados['percentual'] ?? null,
            ];

            // Adiciona indicador no grupo correspondente
            if (!isset($indicadoresPorGrupo[$grupo])) {
                // Cria grupo "Outros" se não existir
                $indicadoresPorGrupo[$grupo] = [];
            }

            $indicadoresPorGrupo[$grupo][$nome] = $dados;
        }

        /* 5. Reordena indicadores dentro de cada grupo conforme a ordem definida */
        foreach ($definicaoGrupos as $grupoCfg) {
            $nomeGrupo = $grupoCfg['nome'];
            $itensOrdenados = $grupoCfg['itens'];

            $ordenado = [];

            // Adiciona os indicadores na ordem definida
            foreach ($itensOrdenados as $indicadorEsperado) {
                if (isset($indicadoresPorGrupo[$nomeGrupo][$indicadorEsperado])) {
                    $ordenado[$indicadorEsperado] = $indicadoresPorGrupo[$nomeGrupo][$indicadorEsperado];
                    unset($indicadoresPorGrupo[$nomeGrupo][$indicadorEsperado]);
                }
            }

            // Adiciona no final os indicadores restantes (não definidos na ordem)
            $ordenado += $indicadoresPorGrupo[$nomeGrupo] ?? [];

            // Sobrescreve o grupo com o array ordenado
            $indicadoresPorGrupo[$nomeGrupo] = $ordenado;
        }

        /* 6. Funções de comentário por indicador ----------------------- */
        $funcoesIndicador = $this->comentariosIndicadores();

        /* 7. Gera comentários (por indicador & por grupo) -------------- */
        $comentarios = $this->gerarComentarioArray(
            $dadosParaComentario,
            $funcoesIndicador,
            $definicaoGrupos
        );

        $comentariosPorGrupo = $comentarios['porGrupo'];
        $comentariosPorIndicador = $comentarios['porIndicador'];

        /* 8. (Opcional) força FCFF no fim do array --------------------- */
        if (isset($indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)'])) {
            $fcff = $indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)'];
            unset($indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)']);
            $indicadoresPorGrupo['Fluxo de Caixa Livre da Firma (FCFF)'] = $fcff;
        }

        /* 9. Envia para a view ----------------------------------------- */
        return view('livewire.admin.analise.relatorio', [
            'anos' => $anos,
            'anoAtual' => $anoAtual,
            'indicadoresPorGrupo' => $indicadoresPorGrupo,
            'comentariosPorGrupo' => $comentariosPorGrupo,
            'comentariosPorIndicador' => $comentariosPorIndicador,
            'setas' => $setas,
        ])->layout('layouts.admin');
    }
}