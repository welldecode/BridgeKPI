<div>

    <x-slot name="header">
        <!-- Page header -->
        <div class="page-header d-print-none">
            <div class="container-xl">
                <div class="row g-2 align-items-center">
                    <div class="col">
                        <!-- Page pre-title -->
                        <div class="page-pretitle">
                         Inserção de Dados
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
            <div class="p-5">
                <form id="dataForm" class="mb-4">
                    <div class="mt-4">
                        <div class="flex justify-between items-center w-full md:flex-row gap-2 flex-col">

                            <div class="flex flex-wrap gap-2  ">
                                <input type="file" id="fileInput" accept=".csv, .xlsx" class="hidden">
                                <label for="fileInput" id="fileLabel"
                                    class="cursor-pointer border border-gray-300 text-gray-600 text-sm rounded-md py-2 px-3 text-center focus:outline-none bg-gray-200 hover:bg-gray-300">
                                    <i class="fa-solid fa-file"></i> Escolher arquivo
                                </label>
                                <button type="button" id="uploadFile" class="btn btn-success" disabled="">Carregar
                                    Arquivo</button>
                            </div>
                            <div class="flex items-center flex-wrap gap-2">
                                <button type="button" id="resetTable" class="btn btn-danger">Resetar
                                    Tabela</button>
                                <button type="button" id="clearTable" class="btn btn-warning">Limpar
                                    Tabela</button>
                                <div class="flex items-center flex-wrap gap-2">
                                    <select id="modeloExcel" class="  p-2 border border-gray-300 rounded  mr-2">

                                        <option value="Mensal">Mensal</option>
                                        <option value="Trimestral">Trimestral</option>
                                        <option value="Anual">Anual</option>
                                    </select>
                                    <button id="baixarModelo" class="btn btn-info ">
                                        Baixar Modelo
                                    </button>
                                    <script>
                                        document.getElementById('baixarModelo').addEventListener('click', function() {
                                            const modelo = document.getElementById('modeloExcel').value;
                                            if (modelo) {
                                                const caminhoArquivo = `/public/assets/modelos/Balanco_Patrimonial_${modelo}.xlsx`;
                                                window.open(caminhoArquivo, '_blank');
                                            } else {
                                                alert('Por favor, selecione um modelo antes de baixar.');
                                            }
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="flex items-center h-full flex-wrap gap-4  ">
                    <div>
                        <label for="insertionType" class="block">Tipo de Inserção:</label>
                        <select id="insertionType" name="insertionType"
                            class="p-2 pe-5 border border-gray-300 rounded w-fit">
                            <option value="mensal">Inserção Mensal</option>
                            <option value="trimestral">Inserção Trimestral</option>
                            <option value="anual">Inserção Anual</option>
                        </select>
                    </div>

                    <div id="yearSelection">
                        <label for="selectedYear" class="block">Ano:</label>
                         <select id="selectedYear" name="selectedYear"
                            class="p-2 pe-8 border border-gray-300 rounded w-fit"> 
                        </select>
                    </div>

                    <div id="monthSelection">
                        <label for="selectedMonth" class="block">Mês:</label>
                        <select id="selectedMonth" name="selectedMonth"
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

                    <div id="quarterSelection" style="display: none;">
                        <label for="selectedQuarter" class="block">Trimestre:</label>
                        <select id="selectedQuarter" class="p-2 pe-8 border border-gray-300 rounded w-fit">
                            <option value="0">1º Trimestre</option>
                            <option value="1">2º Trimestre</option>
                            <option value="2">3º Trimestre</option>
                            <option value="3">4º Trimestre</option>
                        </select>
                    </div>

                </div>

                <button id="addColumn" class="mt-3 px-4 h-full py-2 btn btn-primary">Adicionar Coluna</button>

            </div>

            <div class=" relative">
 
                <table id="dataTable" class="w-full  ">
                    <thead class=" ">
                        <tr>
                            <th class="p-2 bg-white">Demonstração do resultado do exercício</th>
                        </tr>
                    </thead>
                    <tbody class="categoria-principal">
                        <tr class="categoria-row hover-highlight" data-categoria="receita_operacional_bruta">
                            <td class="p-2 bg-slate-100 cursor-pointer border border-b font-bold">RECEITA OPERACIONAL
                                BRUTA</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="receita_operacional_bruta">
                            <td class="p-2 bg-white cursor-pointer border border-b ps-5">Venda de Produtos Mercadorias
                                Serviços</td>
                        </tr>
                    </tbody>

                    <tbody class="categoria-principal">
                        <tr class="categoria-row hover-highlight" data-categoria="deducoes_da_receita_bruta">
                            <td class="p-2 bg-slate-100 cursor-pointer border border-b font-bold">(-) DEDUÇÕES DA
                                RECEITA BRUTA</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="deducoes_da_receita_bruta">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">Devolução de Vendas</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="deducoes_da_receita_bruta">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">Descontos</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="deducoes_da_receita_bruta">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">Impostos e Contribuições
                                Incidentes sobre Vendas</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="receita_operacional_liquida">
                            <td class="p-2 bg-white cursor-pointer border border-b">(=) RECEITA OPERACIONAL LÍQUIDA
                            </td>
                        </tr>
                        <tr class="categoria-row hover-highlight"
                            data-categoria="custo_produtos_mercadorias_servicos">
                            <td class="p-2 bg-white cursor-pointer border border-b">(-) CUSTO
                                PRODUTOS/MERCADORIAS/SERVIÇOS</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="resultado_operacional_bruto">
                            <td class="p-2 bg-slate-100  cursor-pointer border border-b">(=) RESULTADO OPERACIONAL BRUTO
                            </td>
                        </tr>
                    </tbody>

                    <tbody class="categoria-principal">
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_operacionais">
                            <td class="py-2 bg-slate-100 cursor-pointer border border-b font-bold">(-) DESPESAS
                                OPERACIONAIS</td>
                        </tr>
                    </tbody>

                    <tbody class="categoria-principal">
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_com_vendas">
                            <td class="py-2 bg-slate-100 cursor-pointer border border-b ps-5 font-bold">Despesas com
                                Vendas</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_com_vendas">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-16">Comissões sobre Vendas</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_com_vendas">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-16">Propaganda e Publicidade</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_com_vendas">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-16">Outras Despesas com Vendas</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_administrativas_e_gerais">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">Despesas Administrativas e Gerais</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="outros_resultados_operacionais">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">Outros Resultados Operacionais</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="ebitda">
                            <td class="p-2 bg-slate-100  cursor-pointer border border-b">(=) EBITDA</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="depreciacao_e_amortizacao">
                            <td class="p-2 bg-white cursor-pointer border border-b">(-) DEPRECIAÇÃO E AMORTIZAÇÃO</td>
                        </tr>
                    </tbody>

                    <tbody class="categoria-principal">
                        <tr class="categoria-row hover-highlight" data-categoria="ebit">
                            <td class="p-2 bg-slate-100 cursor-pointer border border-b font-bold">(=) EBIT</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="ebit">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">Receitas Não Operacionais
                            </td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="ebit">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">(-) Despesas Não Operacionais
                            </td>
                        </tr>
                        <tr class="categoria-row hover-highlight"
                            data-categoria="resultado_antes_do_resultado_financeiro">
                            <td class="p-2 bg-slate-100  cursor-pointer border border-b">(=) RESULTADO ANTES DO RESULTADO
                                FINANCEIRO</td>
                        </tr>
                    </tbody>

                    <tbody class="categoria-principal">
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_financeiras_liquidas">
                            <td class="p-2 bg-slate-100 cursor-pointer border border-b font-bold">(-) DESPESAS
                                FINANCEIRAS LÍQUIDAS</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_financeiras_liquidas">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">(-) Despesas Financeiras</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="despesas_financeiras_liquidas">
                            <td class="py-2 bg-white cursor-pointer border border-b ps-5">(+) Receitas Financeiras</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="resultado_antes_do_ir_e_csll">
                            <td class="p-2 bg-slate-100  cursor-pointer border border-b">(=) RESULTADO ANTES DO IR E CSLL
                            </td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="ir_e_csll">
                            <td class="p-2 bg-white cursor-pointer border border-b">(-) IR e CSLL</td>
                        </tr>
                        <tr class="categoria-row hover-highlight" data-categoria="resultado_liquido_do_exercicio">
                            <td class="p-2 bg-slate-100  cursor-pointer border border-b">(=) RESULTADO LÍQUIDO DO EXERCÍCIO
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="px-5 py-5">

                    <button id="saveData" class="btn btn-primary">Salvar Dados</button>
                </div>
            </div>
        </div>
    </div>
</div>

@section('script')
    <script src="https://cdn.sheetjs.com/xlsx-0.20.0/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/autonumeric@4.6.0/dist/autoNumeric.min.js"></script>
    <script type="text/javascript" src="/assets/js/frontend/dados/dre.js"></script>
@endsection
