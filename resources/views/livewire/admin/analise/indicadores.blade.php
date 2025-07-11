<div>
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
                        <h2 class="page-title"> Indicadores Econômico-Financeiros
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div>
        <div class="card">
            <div class="  ">
                <div class= "  ">
                    @if ($currentStep == 1)
                        <div class="card-header ">
                            <h3 class="card-title">Selecione os Indicadores</h3>
                        </div>
                        {{-- STEP 1 --}}

                        <div class="space-y-3 p-4">
                            <!-- Checkbox global -->
                            <label class="inline-flex items-center space-x-2  cursor-pointer">
                                <input type="checkbox"
                                    class="form-checkbox h-3 w-3 text-indigo-600 rounded focus:ring focus:ring-indigo-300"id="selectAllGlobal">
                                <span class="text-gray-800 font-semibold">Selecionar Todos os Indicadores</span>
                            </label>
                            <div class="mb-4">
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="principais-indicadores">
                                    <!-- Checkbox Selecionar Todos deste grupo -->
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox" class="select-all" data-grupo="principais-indicadores">
                                        Principais Indicadores
                                    </label>
                                    <div class="flex flex-wrap justify-start gap-4 w-full  ">
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="principais-indicadores"
                                                value="receita_operacional_bruta_(ROB)">
                                            Receita Operacional Bruta (ROB)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="principais-indicadores"
                                                value="deduções_da_receita_bruta_(ROB)">
                                            Deduções da Receita Bruta
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="principais-indicadores"
                                                value="receita_operacional_líquida_(ROL)">
                                            Receita Operacional Líquida (ROL)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="principais-indicadores"
                                                value="resultado_de_Op_desc_Resultado_Não_Operacionais">
                                            Resultado de Op. Desc. + Resultado Não Operacionais
                                        </label>

                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="custos_e_despesas_fixas" data-grupo="principais-indicadores">
                                            Custos e Despesas Fixas
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="custos_e_despesas_fixas_porcental_do_rol"
                                                data-grupo="principais-indicadores">
                                            Custos e Despesas Fixas (% do ROL)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="custos_e_despesas_variaveis" data-grupo="principais-indicadores">
                                            Custos e Despesas Variáveis
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="principais-indicadores"
                                                value="custos_e_despesas_variaveis_porcental">
                                            Custos e Despesas Variavies (% do ROL)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="principais-indicadores"
                                                value="ponto_de_equilíbrio_contábil">
                                            Ponto de Equilíbrio Contábil (Conceito ROL)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="lucro_líquido_do_exercício" data-grupo="principais-indicadores">
                                            Lucro Líquido do Exercício
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="margem_líquida"
                                                data-grupo="principais-indicadores">
                                            Margem Líquida
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="patrimônio_líquido"
                                                data-grupo="principais-indicadores">
                                            Patrimônio Líquido
                                        </label>


                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="principais-indicadores"
                                                value="retorno_sobre_patrimonio_liquido_roe">
                                            Retorno sobre o Patrimônio Líquido (ROE)
                                        </label>
                                    </div>
                                </div>
                                <br>
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="indicadores-de-liquidez">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200 text-zinc-700">
                                        <input type="checkbox" class="select-all" data-grupo="indicadores-de-liquidez">
                                        Indicadores de Liquidez</label>

                                    <div class="datagrid">

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="capital_circulante_líquido"
                                                data-grupo="indicadores-de-liquidez">
                                            Capital Circulante Líquido (R$)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="liquidez_corrente"
                                                data-grupo="indicadores-de-liquidez">
                                            Liquidez Corrente
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="liquidez_seca"
                                                data-grupo="indicadores-de-liquidez">
                                            Liquidez Seca
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="liquidez_imediata"
                                                data-grupo="indicadores-de-liquidez">
                                            Liquidez Imediata
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="liquidez_geral"
                                                data-grupo="indicadores-de-liquidez">
                                            Liquidez Geral
                                        </label>
                                    </div>
                                </div>

                                <br>
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="indicadores-de-atividades">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox" class="select-all"
                                            data-grupo="indicadores-de-atividades">
                                        Indicadores de Atividades</label>

                                    <div class="datagrid">

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">

                                            <input type="checkbox" wire:model="indicadores"
                                                value="prazo_medio_estocagem" data-grupo="indicadores-de-atividades">
                                            Prazo Médio de Estocagem (dias)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="giro_estoque"
                                                data-grupo="indicadores-de-atividades">
                                            Giro do Estoque (vezes)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="prazo_medio_pagamento" data-grupo="indicadores-de-atividades">
                                            Prazo Médio de Pagamento a Fornecedores (dias)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="giro_contas_pagar"
                                                data-grupo="indicadores-de-atividades">
                                            Giro das Contas a Pagar (vezes)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="prazo_medio_cobranca" data-grupo="indicadores-de-atividades">
                                            Prazo Médio de Cobrança (dias)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="giro_valores_receber" data-grupo="indicadores-de-atividades">
                                            Giro dos Valores a Receber (vezes)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="giro_ativo"
                                                data-grupo="indicadores-de-atividades">
                                            Giro do Ativo (vezes)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="giro_investimento"
                                                data-grupo="indicadores-de-atividades">
                                            Giro do Investimento (vezes)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="giro_patrimonio_liquido"
                                                data-grupo="indicadores-de-atividades">
                                            Giro do Patrimônio Líquido (vezes)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="ciclo_operacional"
                                                data-grupo="indicadores-de-atividades">
                                            Ciclo Operacional (dias)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="ciclo_financeiro"
                                                data-grupo="indicadores-de-atividades">
                                            Ciclo Financeiro (dias)
                                        </label>
                                    </div>
                                </div>
                                <br>
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="indicadores-de-endividamento-estrutura">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox" class="select-all"
                                            data-grupo="indicadores-de-endividamento-estrutura">
                                        Indicadores de Endividamento e Estrutura</label>

                                    <div class="datagrid">

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="cap_de_terceiros_capital_proprio">
                                            Relação Capital de Terceiros/Capital Próprio (Endividamento)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="cap_de_terceiros_passivo_total">
                                            Cap. de Terceiros/Passivo Total (Dependência Financeira)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="grau_de_imobilização_de_recursos_permanentes">
                                            Grau de Imobilização de Recursos Permanentes
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="alococao_de_recursos_permanentes_no_cp">
                                            Alocação de Recursos Permanentes no CP
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="indicador_de_cobertura_de_juros_conceito_ebit">
                                            Indicador de Cobertura de Juros (Conceito EBIT)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="indicador_de_cobertura_de_juros_conceito_EBITDA">
                                            Indicador de Cobertura de Juros (Conceito EBITDA)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="taxa_de_juros_sobre_empréstimos_e_financiamentos">
                                            Taxa de juros sobre Empréstimos e Financiamentos
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-endividamento-estrutura"
                                                value="depreciação_como_porcentagem_da_ROL">
                                            Depreciação como % da ROL
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="CAPEX_Depreciação"
                                                data-grupo="indicadores-de-endividamento-estrutura">
                                            CAPEX/Depreciação
                                        </label>

                                    </div>
                                </div>
                                <br>
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="indicadores-de-rentabilidade-lucratividade">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox"
                                            class="select-all"data-grupo="indicadores-de-rentabilidade-lucratividade">Indicadores
                                        de Rentabilidade e
                                        Lucratividade</label>

                                    <div class="datagrid">
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade"
                                                value="retorno_sobre_o_ativo_ROA_conceito_NOPAT">
                                            Retorno sobre o Ativo (ROA) (Conceito NOPAT)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade"
                                                value="retorno_sobre_o_capital_investido_ROIC_conceito_NOPAT">
                                            Retorno sobre o Capital Investido (ROIC) (Conceito NOPAT)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="ROI_Publicidade"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            ROI da Publicidade e Propaganda (Conceito MC)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="margem_bruta"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            Margem Bruta
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="margem_de_contribuição"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            Margem de Contribuição
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade"
                                                value="margem_contribuicao_percent_rob">
                                            Margem de Contribuição % (Conceito ROB)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade"
                                                value="margem_contribuicao_percent_rol">
                                            Margem de Contribuição % (Conceito ROL)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="margem_operacional_nopat"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            Margem Operacional (Conceito NOPAT)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="EBITDA"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            EBITDA
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="EBIT_Lucratividade"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            EBIT
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="nopat"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            NOPAT
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="margem_ebitda"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            Margem Ebitda
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="margem_ebit"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            Margem Ebit
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="margem_nopat"
                                                data-grupo="indicadores-de-rentabilidade-lucratividade">
                                            Margem NOPAT
                                        </label>
                                    </div>
                                </div>
                                <br />
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="graus-de-alavancagem">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox" class="select-all"
                                            data-grupo="graus-de-alavancagem">Graus de Alavancagem</label>

                                    <div class="datagrid">

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="graus-de-alavancagem"
                                                value="grau_alavancagem_operacional">
                                            Grau de Alavancagem Operacional - GAO (Conceito EBIT)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="graus-de-alavancagem" value="grau_alavancagem_financeira">
                                            Grau de Alavancagem Financeira - GAF (Conceito EBIT)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="grau_alavancagem_total" data-grupo="graus-de-alavancagem">
                                            Grau de Alavancagem Total - GAT
                                        </label>

                                    </div>
                                </div>
                                <br>
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="identidade-duPont">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox" class="select-all"
                                            data-grupo="identidade-duPont">Identidade DuPont</label>

                                    <div class="datagrid">

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="margem_líquida_ll_rol" data-grupo="identidade-duPont">
                                            Margem Líquida (LL/ROL)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="giro_do_ativo_total" data-grupo="identidade-duPont">
                                            Giro do Ativo Total (ROL/AT)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="multiplicadorPl"
                                                data-grupo="identidade-duPont">
                                            Multiplicador do PL (AT/PL)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="rentabilidade_roe"
                                                data-grupo="identidade-duPont">
                                            Rentabilidade - ROE
                                        </label>

                                    </div>
                                </div>

                                <br>
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="analise-do-grau-de-alavancagem-financeira">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox" class="select-all"
                                            data-grupo="analise-do-grau-de-alavancagem-financeira">Análise do Grau de
                                        Alavancagem Financeira
                                        (GAF)</label>

                                    <div class="datagrid">

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="gaf"
                                                data-grupo="analise-do-grau-de-alavancagem-financeira">
                                            GAF
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="roe_retorno_sobre_o_pl"
                                                data-grupo="analise-do-grau-de-alavancagem-financeira">
                                            ROE (Retorno sobre o PL)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm"
                                            data-grupo="analise-do-grau-de-alavancagem-financeira">
                                            <input type="checkbox" wire:model="indicadores" value="roic">
                                            ROIC (Retorno sobre o Capital Investido)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm"
                                            data-grupo="analise-do-grau-de-alavancagem-financeira">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="ganho_pela_alavancagem_financeira">
                                            Ganho pela Alavancagem Financeira
                                        </label>

                                    </div>
                                </div>
                                <br>
                                <div class="grupo-indicadores border border-zinc-100 p-4 rounded-md "
                                    data-grupo="fluxo-de-caixa-livre-da-firma">
                                    <label
                                        class="flex items-center gap-2 mb-3 text-base font-bold border-b pb-3 border-zinc-200">
                                        <input type="checkbox" class="select-all"
                                            data-grupo="fluxo-de-caixa-livre-da-firma">Fluxo de Caixa Livre da Firma
                                        (FCFF)</label>

                                    <div class="datagrid">

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="EBIT"
                                                data-grupo="fluxo-de-caixa-livre-da-firma">
                                            Lucro Operacional antes do IR e CSLL (EBIT)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="ir_e_csll"
                                                data-grupo="fluxo-de-caixa-livre-da-firma">
                                            (-) IR e CSLL
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores" value="no_pat"
                                                data-grupo="fluxo-de-caixa-livre-da-firma">
                                            (=)Lucro Operacional Líquido de IR e CSLL (NOPAT)
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="fluxo-de-caixa-livre-da-firma"
                                                value="depreciacao_e_amortização">
                                            (+) Depreciação e Amortização
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="fluxo-de-caixa-livre-da-firma"
                                                value="fluxo_de_caixa_operacional">
                                            (=) Fluxo de Caixa Operacional
                                        </label>

                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                value="investimentos_fixos"
                                                data-grupo="fluxo-de-caixa-livre-da-firma">

                                            (-) Investimentos Fixos (CAPEX)
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="fluxo-de-caixa-livre-da-firma"
                                                value="investimento_em_capital_de_giro">
                                            (-) Investimento em Capital de Giro
                                        </label>
                                        <label class="flex items-center gap-2 font-semibold text-zinc-600 text-sm">
                                            <input type="checkbox" wire:model="indicadores"
                                                data-grupo="fluxo-de-caixa-livre-da-firma"
                                                value="fluxo_de_caixa_livre_da_firma">
                                            (=) Fluxo de Caixa Livre da Firma (FCFF)
                                        </label>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endif

                    @if ($currentStep == 2)
                        <div class="card-header ">
                            <h3 class="card-title">Selecione o Tipo</h3>
                        </div>
                        <div class="space-y-3 p-4">
                            <div>
                                <label class="font-semibold">Tipo</label>
                                <select wire:model="tipo" class="w-full border rounded p-2">
                                    <option value="anual">Anual</option>
                                    <option value="mensal">Mensal</option>
                                    <option value="trimestral">Trimestral</option>
                                </select>
                            </div>

                            <div>
                                <label class="font-semibold">Anos</label>
                                <select multiple wire:model="anos" class="w-full border rounded p-2 h-28">
                                    @for ($y = date('Y'); $y >= date('Y') - 25; $y--)
                                        <option value="{{ $y }}">{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>

                            @if ($tipo === 'mensal')
                                <div>
                                    <label class="font-semibold">Meses</label>
                                    <select multiple wire:model="meses" class="w-full border rounded p-2 h-28">
                                        @foreach (range(1, 12) as $m)
                                            @php
                                                $month = Carbon\Carbon::parse("2025-$m-01");
                                            @endphp
                                            <option value="{{ $m }}">
                                                {{ ucfirst($month->translatedFormat('F')) }} </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif

                            @if ($tipo === 'trimestral')
                                <div>
                                    <label class="font-semibold">Trimestres</label>
                                    <select multiple wire:model="trimestres" class="w-full border rounded p-2 h-28">
                                        <option value="1">1º Trimestre</option>
                                        <option value="2">2º Trimestre</option>
                                        <option value="3">3º Trimestre</option>
                                        <option value="4">4º Trimestre</option>
                                    </select>
                                </div>
                            @endif
                        </div>
                    @endif



                    @if ($currentStep == 3)
                        <div class="card-header ">
                            <h3 class="card-title">Resultado dos Indicadores</h3>
                        </div>
                        {{-- STEP 1 --}}

                        <div class="space-y-3 p-4">
                            <table class="w-full border text-sm">
                                @php
                                    // Extrai todas as colunas possíveis a partir dos resultados (ordenadas)
                                    $colunas = collect($resultado)
                                        ->flatMap(fn($valores) => array_keys($valores))
                                        ->unique()
                                        ->sort()
                                        ->values();
                                @endphp

                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="border px-2 py-1">Indicadores Econômico-Financeiros</th>
                                        @foreach ($colunas as $coluna)
                                            <th class="border px-2 py-1">{{ $coluna }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($resultado as $indicador => $valores)
                                        <tr>
                                            <td class="border px-2 py-1">
                                                {{ $this->formatarNomeIndicador(ucfirst(str_replace('_', ' ', $indicador))) }}</td>

                                            @foreach ($colunas as $coluna)
                                                <td class="border px-2 py-1">
                                                    @php
                                                        $valor = $valores[$coluna] ?? null;

                                                        $formatarComoPorcentagem = in_array($indicador, [
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

                                                        echo $valor !== null && $valor !== 'N/A'
                                                            ? ($formatarComoPorcentagem
                                                                ? number_format((float) $valor, 2, ',', '.') . '%'
                                                                : number_format((float) $valor, 2, ',', '.'))
                                                            : '';
                                                    @endphp
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if ($currentStep == 3)
                        <button wire:click="decreaseStep()" type="button" class="btn btn-md btn-danger m-4">
                            {{ __('Back') }}
                        </button>


                        <button wire:click="createReport" type="submit" class=" btn btn-primary">
                            Gerar Relatorio</button>

                                @error('success')
                            <div class="alert alert-danger flex items-center mx-4 mb-4" role="alert">
                                <div class="alert-icon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                {{ $message }}
                            </div>
                        @enderror
                    @endif

                    @if ($currentStep == 1)
                        <div class="flex flex-col gap-4 m-4">

                            @error('indicadores')
                                <div class="alert alert-danger flex items-center " role="alert">
                                    <div class="alert-icon">
                                        <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            class="icon alert-icon icon-2">
                                            <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                            <path d="M12 8v4"></path>
                                            <path d="M12 16h.01"></path>
                                        </svg>
                                    </div>
                                    {{ $message }}
                                </div>
                            @enderror
                            <button type="button" class="btn btn-md btn-success "
                                wire:click="increaseStep()">Continuar</button>

                        </div>
                    @endif

                    @if ($currentStep == 2)
                        @error('anos')
                            <div class="alert alert-danger flex items-center mx-4 mb-4" role="alert">
                                <div class="alert-icon">
                                    <!-- Download SVG icon from http://tabler.io/icons/icon/alert-circle -->
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" class="icon alert-icon icon-2">
                                        <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0"></path>
                                        <path d="M12 8v4"></path>
                                        <path d="M12 16h.01"></path>
                                    </svg>
                                </div>
                                {{ $message }}
                            </div>
                        @enderror
                        <div class="flex items-center gap-4 mx-4 mb-4">
                            <button wire:click="decreaseStep()" type="button" class="btn btn-md btn-danger mt-4">
                                {{ __('Back') }}
                            </button>
                            <button wire:click="calcular" type="submit" class=" btn btn-primary  mt-4">Calcular
                                Indicadores</button>

                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript">
        const selectAllGlobal = document.getElementById('selectAllGlobal');

        selectAllGlobal.addEventListener('change', () => {
            // Marca/desmarca todos os checkboxes (incluindo os "selecionar todos" dos grupos)
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"]');
            allCheckboxes.forEach(cb => {
                cb.checked = selectAllGlobal.checked;
                // Se quiser disparar eventos:
                cb.dispatchEvent(new Event('change'));
            });
        });

        // Checkbox selecionar todos por grupo
        document.querySelectorAll('.select-all').forEach(selectAllCheckbox => {
            selectAllCheckbox.addEventListener('change', () => {
                console.log('passando aq');
                const grupo = selectAllCheckbox.dataset.grupo;
                // Seleciona os checkboxes do grupo correspondente, excluindo o "selecionar todos"
                const checkboxes = document.querySelectorAll(
                    `input[type="checkbox"][data-grupo="${grupo}"]:not(.select-all)`);

                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;

                    // Se precisar disparar o evento change para Livewire ou outro framework
                    checkbox.dispatchEvent(new Event('change'));
                });
            });
        });

        // Atualiza o checkbox global para refletir o estado atual dos checkboxes
        function updateGlobalCheckbox() {
            const allCheckboxes = document.querySelectorAll('input[type="checkbox"]:not(#selectAllGlobal)');
            const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
            selectAllGlobal.checked = allChecked;
        }

        // Atualiza o checkbox de selecionar todos de cada grupo se todos os checkboxes daquele grupo estiverem marcados
        function updateGroupSelectAll() {
            document.querySelectorAll('.grupo-indicadores').forEach(grupoDiv => {
                const grupo = grupoDiv.dataset.grupo;
                const checkboxes = grupoDiv.querySelectorAll(
                    `input[type="checkbox"][data-grupo="${grupo}"]:not(.select-all)`);
                const selectAllCheckbox = grupoDiv.querySelector(`.select-all[data-grupo="${grupo}"]`);

                if (checkboxes.length === 0) return;

                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
            });
        }
    </script>


</div>
