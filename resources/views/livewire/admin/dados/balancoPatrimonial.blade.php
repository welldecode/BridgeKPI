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
                            Balanço Patrimonial
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>
    <div>

        <div class="card">
            <div class="p-5  ">
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
                                <div class="flex items-center flex-wrap gap-2 ">
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
                <div class="flex items-center h-full flex-wrap gap-4 filtersTop ">
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

                <table id="dataTable" class="w-full overflow-y-auto">
                    <thead class=" ">
                        <tr>
                            <th class="p-2 bg-white">Balanço patrimonial - BRL</th>

                        </tr>
                    </thead>
                    <tbody class="categoria-principal">
                        <tr class='categoria-row hover-highlight' data-categoria="ativo">
                            <td class='p-2 bg-slate-100 cursor-pointer border border-b font-bold'
                                data-categoria="ativo">
                                ATIVO
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="categoria-principal" data-categoria="ativo_circulante">
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_circulante">
                            <td class='py-2  bg-slate-100 cursor-pointer border border-b ps-4 font-bold'>
                                ATIVO CIRCULANTE
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Caixa e Equivalentes de Caixa
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Aplicações Financeiras
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Contas a Receber
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Estoque
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Tributos a Recuperar
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Outros Créditos
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight'>
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Despesas Antecipadas
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="categoria-principal" data-categoria="ativo_nao_circulante">
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_nao_circulante">
                            <td class='py-2 bg-slate-100 cursor-pointer border border-b ps-4 font-bold'
                                data-id="ativo_nao_circulante">
                                ATIVO NÃO CIRCULANTE
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="realizavel">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16 font-bold'
                                data-id="realizavel">
                                Realizável a Longo Prazo
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="realizavel">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-24'>
                                Contas a Receber
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="realizavel">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-24'>
                                Tributos a Recuperar
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="realizavel">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-24'>
                                Outros Créditos
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Investimentos
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Imobilizado
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Direito de Uso
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="ativo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Intangível
                            </td>
                        </tr>
                    </tbody>

                    <tbody class="categoria-principal" data-categoria="passivo_e_patrimonio_liquido">
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_e_patrimonio_liquido">
                            <td class='p-2 bg-slate-100 cursor-pointer border border-b font-bold'
                                data-id="passivo_e_patrimonio_liquido">
                                PASSIVO E PATRIMÔNIO LÍQUIDO
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="categoria-principal" data-categoria="passivo_circulante">
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='py-2 bg-slate-100 cursor-pointer border border-b ps-4 font-bold'
                                data-id="passivo_circulante">
                                PASSIVO CIRCULANTE
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Fornecedores
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Empréstimos e Financiamentos</td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Obrigações Tributárias</td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Obrigações Trabalhistas e Sociais</td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Provisões
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Lucro/JCP a Distribuir
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Adiantamento de Clientes</td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Outras Obrigações
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="categoria-principal" data-categoria="passivo_nao_circulante">
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_nao_circulante">
                            <td class='py-2 bg-slate-100 cursor-pointer border border-b ps-4 font-bold'
                                data-id="patrimonio_nao_circulante">
                                PASSIVO NÃO CIRCULANTE
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Fornecedores LP
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Empréstimos e Financiamentos'
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Obrigações Tributárias
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Provisões'
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="passivo_nao_circulante">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Outras Obrigações
                            </td>
                        </tr>
                    </tbody>
                    <tbody class="categoria-principal" data-categoria="patrimonio_liquido">
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-slate-100 cursor-pointer border border-b ps-4 font-bold'>
                                PATRIMÔNIO LÍQUIDO
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Capital Social
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="reservas">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16 font-bold'>
                                Reservas
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="reservas">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-24'>
                                Reservas de Capital
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="reservas">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-24'>
                                Reservas de Reavaliação
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="reservas">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-24'>
                                Reservas de Lucros
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Ajustes de Avaliação Patrimonial
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Lucros/Prejuízos Acumulados
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Resultado do Exercício
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Participação de Acionistas não Controladores
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Ajustes Acumulados de Conversão
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                Outros Resultados
                            </td>
                        </tr>
                        <tr class='categoria-row hover-highlight' data-categoria="patrimonio_liquido">
                            <td class='table_info py-2 bg-white cursor-pointer border border-b ps-16'>
                                (-) Ações em Tesouraria
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
    <script type="text/javascript" src="/assets/js/frontend/dados/bp.js"></script>
@endsection
