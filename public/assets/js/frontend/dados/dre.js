$(document).ready(function () {
    var editedCells = new Set();
    var months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    var quarters = ['1T', '2T', '3T', '4T'];
    var tableHeaders = [];
    var tableData = [];
    var categoryCounter = 0;

    var nonEditableCategories = [  
        'receita_operacional_bruta',
        '(-)_deduções_da_receita_bruta',
        '(=)_resultado_operacional_bruto',
        '(-)_despesas_operacionais',
        'despesas_com_vendas',
        '(=)_ebit',
        '(=)_resultado_antes_do_resultado_financeiro',
        '(-)_despesas_financeiras_líquidas',
        '(=)_ebitda',
        '(=)_resultado_antes_do_ir_e_csll',
        '(=)_resultado_líquido_do_exercício'
    ];
    var editableCategories = [
        'Receita de Vendas',
        'Receita de Serviços',
        'Impostos sobre Vendas',
        'Devoluções e Descontos',
        'Despesas com Vendas',
        'Despesas Administrativas e Gerais',
        'Outros Resultados Operacionais',
        'Receitas Financeiras',
        'Despesas Financeiras'
    ];

    // Função para formatar números
    function formatNumber(num) {
        return parseFloat(num).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            useGrouping: true,
            style: 'decimal'
        });
    }

    // Função para calcular o total de uma categoria
    function calculateCategoryTotal(category) {
        var total = 0;
        var categoryItems = $(`tr[data-categoria="${category}"] input`);

        categoryItems.each(function () {
            var value = $(this).val().replace(/\./g, '').replace(',', '.');
            const numberValue = parseFloat(value);
            if (!isNaN(numberValue)) {
                total += numberValue;
            }
        });

        return total;
    }

    // Função para atualizar os totais
    function updateTotalValues() {
        var totalSum = 0;

        $('#dataTable tbody tr').each(function () {
            var category = $(this).find('td:first').text().trim();
            var categoryTotal = calculateCategoryTotal(category);

            $(this).find('.category-total').html(formatNumber(categoryTotal));

            totalSum += categoryTotal;
        });

        $('#totalGeral').html(formatNumber(totalSum));
    }

    // Função para padronizar os nomes das categorias
    function normalizeCategory(cat) {
        return cat
            .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // remove acentos
            .toLowerCase()
            .replace(/[^a-z0-9]/gi, '_') // substitui qualquer coisa que não seja letra/número por _
            .replace(/_+/g, '_') // evita múltiplos underlines
            .replace(/^_+|_+$/g, ''); // remove _ no começo/fim
    }
 
    // Habilitar edição inline
    function enableInlineEditing() {
        $('#dataTable tbody td:not(:first-child)').off('click').on('click', function (e) {
            var $cell = $(this);
            if ($cell.find('input').length > 0) return;

            var categoryLabel = $cell.closest('tr').find('td:first').text().trim();

            var category = $cell.closest('tr').data('categoria');
            var category_year = $cell.closest('td').data('year');
            var currentValue = $cell.text().replace(/\./g, '').replace(',', '.');

            var category_n = categoryLabel.toLowerCase().replace(/\s+/g, '_');
            var category_ns = normalizeCategory(categoryLabel);

           
            var input = $('<input>', {
                type: 'text',
                val: $cell.text().trim(),
                class: `w-full focus:outline-none bg-transparent text-right input_number`,
                'data-category': category_n,
                'data-year': category_year
            });

            $cell.html(input);
            input.focus();
            // Aplica AutoNumeric ao input
            const anElement = new AutoNumeric(input[0], {
                digitGroupSeparator: '.',
                decimalCharacter: ',',
                decimalPlaces: 2,
                modifyValueOnWheel: false,
                unformatOnSubmit: true,
                minimumValue: '0',
                allowNegative: true, // 👈 permite digitar "-"
                minimumValue: '-10000000000000', // 👈 agora permite valores negativos
                maximumValue: '10000000000000',
            });

            input.on('input', function () {
                var soma = new BigNumber(0);

                $(`input[data-category="${category_n}"][data-year="${category_year}"]`).each(function () {
                    const anInstance = AutoNumeric.getAutoNumericElement(this);
                    let valor = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    soma = soma.plus(valor);
                });

                updateTotalValues();
                calcularDRE();  // Rodar os cálculos com o novo valor
            });
            // Quando sair do input (blur)
            input.on('blur', function () {
                const anInstance = AutoNumeric.getAutoNumericElement(this);
                const valorFormatado = anInstance ? anInstance.getFormatted() : currentText;

                $cell.html(valorFormatado); // substitui input pelo valor final
            });
        });
    }
    // Função para calcular a Receita Operacional Líquida
    function calcularDRE() {
        tableHeaders.forEach(function (header) {
            var convertHeader = header.replace(/[\/\.]/g, '');

            // Função para escapar os caracteres especiais no seletor
            function escapeSelector(categoria) {
                return CSS.escape(categoria);
            }

            // Função para obter o valor do campo
            function getValor(categoria) {
                var escapedCategoria = escapeSelector(categoria);
                var cell = $(`.category-${escapedCategoria}-${convertHeader}`);
                var valorTexto = cell.find('input').length ? cell.find('input').val() : cell.text();
                valorTexto = valorTexto.replace(/\./g, '').replace(',', '.');
                return parseFloat(valorTexto) || 0;
            }

            // Função para definir o valor na célula
            function setValor(categoria, valor) {
                var escapedCategoria = escapeSelector(categoria);

                // Renderiza input editável 
                if (nonEditableCategories.includes(categoria)) { 
                    $(`.category-${escapedCategoria}-${convertHeader}`).html(`
                    <input  disabled class="cursor-not-allowed auto-numeric w-full text-right bg-white focus:outline-none"
                           value="${valor}">`); 
                } else { 
                $(`.category-${escapedCategoria}-${convertHeader}`).html(`
                    <input class="auto-numeric w-full text-right bg-white focus:outline-none"
                           value="${valor}">`);
          }
                // Inicializa AutoNumeric nesse input
                new AutoNumeric(`.category-${escapedCategoria}-${convertHeader} input`, {
                    decimalCharacter: ',',
                    digitGroupSeparator: '.',
                    decimalPlaces: 2,
                    modifyValueOnWheel: false,
                    allowNegative: true, // 👈 permite digitar "-"
                    minimumValue: '-10000000000000', // 👈 agora permite valores negativos
                    maximumValue: '10000000000000',
                    watchExternalChanges: true,
                });
            }

            // === Cálculos baseados no modelo de DRE ===

            // RECEITA OPERACIONAL LÍQUIDA
            var receita_bruta = getValor('receita_operacional_bruta');
            var devolucoes = getValor('devolucao_de_vendas');
            var descontos = getValor('descontos');
            var impostos = getValor('impostos_e_contribuicoes_incidentes_sobre_vendas');

            // Se o valor da receita líquida já foi digitado manualmente, usá-lo, caso contrário, calcular
            var receita_liquida = getValor('(=)_receita_operacional_líquida');

            // Caso não tenha sido preenchido, faça o cálculo
            if (isNaN(receita_liquida) || receita_liquida === 0) {
                receita_liquida = receita_bruta - (devolucoes + descontos + impostos);
                setValor('(=)_receita_operacional_líquida', receita_liquida);
            }
            // RESULTADO OPERACIONAL BRUTO
            var custo_produtos = getValor('(-)_custo_produtos/mercadorias/serviços');
            var resultado_bruto = receita_liquida + custo_produtos; // CUIDADO: Custo já é negativo
            setValor('(=)_resultado_operacional_bruto', resultado_bruto);


            // DESPESAS COM VENDAS (somando os componentes)
            var comissoes = getValor('comissoes_sobre_vendas');
            var propaganda = getValor('propaganda_e_publicidade');
            var outras_vendas = getValor('outras_despesas_com_vendas');

            var despesas_vendas = comissoes + propaganda + outras_vendas;
            setValor('despesas_com_vendas', despesas_vendas);


            // DESPESAS OPERACIONAIS (somando todas)
            var despesas_admin = getValor('despesas_administrativas_e_gerais');


            var outros_resultados = getValor('outros_resultados_operacionais');

            var total_despesas_operacionais = despesas_vendas + despesas_admin + outros_resultados;
            setValor('(-)_despesas_operacionais', total_despesas_operacionais);


            // EBITDA
            var EBITDA = resultado_bruto + total_despesas_operacionais;
            setValor('(=)_ebitda', EBITDA);

            // EBIT
            var depre_amort = getValor('(-)_depreciação_e_amortização');
            var EBIT = EBITDA + depre_amort;
            setValor('(=)_ebit', EBIT);

            // RESULTADO ANTES DO RESULTADO FINANCEIRO
            var receitas_nao_operacionais = getValor('receitas_nao_operacionais');
            var despesas_nao_operacionais = getValor('(-)_despesas_não_operacionais');
            var resultado_antes_financeiro = EBIT + receitas_nao_operacionais - despesas_nao_operacionais;
            setValor('(=)_resultado_antes_do_resultado_financeiro', resultado_antes_financeiro);

            // RESULTADO ANTES DO IR E CSLL
            var despesas_financeiras = getValor('(-)_despesas_financeiras');
            var receitas_financeiras = getValor('(+)_receitas_financeiras');
            var despesas_fin_liquidas = despesas_financeiras + receitas_financeiras;
            setValor('(-)_despesas_financeiras_líquidas', despesas_fin_liquidas);

            var resultado_antes_ir = resultado_antes_financeiro + despesas_fin_liquidas;
            setValor('(=)_resultado_antes_do_ir_e_csll', resultado_antes_ir);

            // RESULTADO LÍQUIDO
            var ir_csll = getValor('(-)_ir_e_csll');
            var resultado_liquido = resultado_antes_ir + ir_csll;
            setValor('(=)_resultado_líquido_do_exercício', resultado_liquido);
        });
    }

    function formatarNumero(valorTexto) {
        valorTexto = valorTexto.trim();

        if (valorTexto === "") return "0";

        let partes = valorTexto.split(",");
        if (partes.length > 2) {
            let parteInteira = partes.slice(0, partes.length - 1).join("");
            let parteDecimal = partes[partes.length - 1];
            valorTexto = parteInteira + "," + parteDecimal;
        }

        valorTexto = valorTexto.replace(",", ".");

        let regex = /^-?\d+(\.\d{0,4})?$/;

        return regex.test(valorTexto) ? valorTexto : "0";
    }
    // Função para adicionar vírgulas a cada 3 dígitos na parte inteira
    function adicionarVirgulas(valor) {
        let partes = valor.split(",");
        let parteInteira = partes[0];
        let parteDecimal = partes.length > 1 ? "," + partes[1] : "";

        // Adiciona as vírgulas a cada 3 dígitos na parte inteira
        parteInteira = parteInteira.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

        return parteInteira + parteDecimal;
    }

    // Adicionar coluna
    $('#addColumn').click(function () {
        var insertionType = $('#insertionType').val();
        var selectedYear = $('#selectedYear').val();
        var newHeader = '';

        switch (insertionType) {
            case 'mensal':
                var selectedMonth = parseInt($('#selectedMonth').val());
                newHeader = `${months[selectedMonth]}/${selectedYear}`;
                break;
            case 'trimestral':
                var selectedQuarter = parseInt($('#selectedQuarter').val());
                newHeader = `${quarters[selectedQuarter]}/${selectedYear}`;
                break;
            case 'anual':
                newHeader = selectedYear;
                break;
        }

        if (!tableHeaders.includes(newHeader)) {
            tableHeaders.push(newHeader);
            tableHeaders.sort((a, b) => {
                let [aValue, aYear] = a.split('/');
                let [bValue, bYear] = b.split('/');

                if (!aYear) {
                    aYear = aValue;
                    aValue = '';
                }
                if (!bYear) {
                    bYear = bValue;
                    bValue = '';
                }

                if (aYear !== bYear) return parseInt(aYear) - parseInt(bYear);

                if (months.includes(aValue) && months.includes(bValue)) {
                    return months.indexOf(aValue) - months.indexOf(bValue);
                } else if (quarters.includes(aValue) && quarters.includes(bValue)) {
                    return quarters.indexOf(aValue) - quarters.indexOf(bValue);
                }

                return 0;
            });

            updateTable();
        }
    });


    $(document).on('click', '.removeColumn', function (e) {
        e.stopPropagation();
        var index = $(this).closest('th').index() - 1;
        var removedHeader = tableHeaders[index];
        tableHeaders.splice(index, 1);

        // Remover dados da coluna removida
        tableData.forEach(function (rowData) {
            delete rowData.values[removedHeader];
        });

        updateTable();
    });

    // Atualizar tabela
    function updateTable() {
        // Atualizar cabeçalho da tabela
        var headerRow = '<tr class="bg-indigo-100">';
        headerRow += '<th class="p-2 bg-white"> <div></div></th>';
        tableHeaders.forEach(function (header) {
            var convertHeader = header.replace(/[\/\.]/g, '');
            headerRow += `<th class="border text-center border-slate-300 p-2 bg-slate-100 text-dark relative year_select" data-ano="${convertHeader}" >
            <button class="removeColumn w-full bg-red rounded mb-2 text-white hover:bg-red">X</button>
                       ${header} 
                     </th>`;
        });
        headerRow += '</tr>';
        $('#dataTable thead').html(headerRow);

        var tbody = $('#dataTable tbody');

        // Manter as células de categoria existentes
        tbody.find('tr').each(function (indtrex) {
            var $row = $(this);
            var category = $row.find('td:first').text().trim();
            var category_body = category.toLowerCase().replace(/\s+/g, '_');

            var category_year = $row.closest('thead').data('ano');
            var dataAno = $('.year_select').data('ano'); // Pega o valor do atributo data-ano 
            var rowData = tableData.find(data => data.category === category) || {
                values: {}
            };

            // Remover células de dados existentes
            $row.find('td:not(:first-child)').remove();
            tableHeaders.forEach(function (header) {
                var cellData = rowData.values[header] || { value: null, edited: false };
                var cellClass = cellData.edited ? 'border-yellow-500 border-2 table_rows' : '';
                var displayValue = cellData.value === null ? '' : formatNumber(cellData.value);
                var convertHeader = header.replace(/[\/\.]/g, '');

                // Adicionar classe para células não editáveis
                if (nonEditableCategories.includes(category)) {
                    cellClass += `bg-slate-300 cursor-not-allowed addActivetotal category-${category_body}-${convertHeader}`;
                    displayValue = '';
                } else {
                    // Mesmo nas editáveis, adicionar a classe correta pra poder pegar depois
                    cellClass += `category-${category_body}-${convertHeader}`;
                }

                $row.append(`<td class='p-2 bg-white cursor-pointer border border-b text-right ${cellClass}' data-year='${convertHeader}'>${displayValue}</td>`);
            });
        });

        enableInlineEditing();
    }

    // Função de resetar a tabela
    $('#resetTable').click(function () {
        tableHeaders = [];
        tableData.forEach(function (rowData) {
            rowData.values = {};
        });

        updateTable();
    });

    // Função de limpar a tabela
    $('#clearTable').click(function () {
        tableData.forEach(function (rowData) {
            Object.keys(rowData.values).forEach(function (header) {
                rowData.values[header] = { value: null, edited: false };
            });
        });

        updateTable();
    });

    // Função de salvar os dados
    $('#saveData').click(function () {
        var data = [];
        var list_values = {
            'mes': $('select[name=selectedMonth]').val(),
            'type': $('select[name=insertionType]').val(),
            'year': $('select[name=selectedYear]').val()
        };

        const items = [];
        $('#dataTable tbody tr').each(function (index) {
            var $row = $(this);
            var category = $row.find('td:first').text().trim();


            $row.find('input').each(function (cellIndex) {

                var $cell = $(this);
                var decimalValue = $.trim($(this).val()).replace('.', '').replace(',', '.');
                // Supondo que 'table' é seu elemento <table>
                var $headerCell = $('thead th').eq(cellIndex + 1); // +1 pois o primeiro th é nome da categoria

                // Pega o valor do header (por exemplo, data-ano)
                var header = $headerCell.data('ano');
                items.push({
                    id: index + 1,
                    categoria: category,
                    periodo: header,
                    valor: decimalValue === null ? null : decimalValue
                })

            });
        });

        data.push({
            items,
            list_values
        });

        if (data.length > 0) {
            Swal.fire({
                title: "Inserindo os dados",
                text: 'Verifique todos os dados e campos antes de inserir os dados.',
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                cancelButtonText: "Cancelar!",
                confirmButtonText: "Sim, confirmar!"
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../../dados/dre',
                        type: 'POST',
                        data: { dados: JSON.stringify(data) },
                        success: function (response) {
                            if (response.success == true) {
                                Swal.fire({
                                    title: "Dados Inserido!",
                                    text: "Seus dados foram inseridos com sucesso.",
                                    icon: "success"
                                });
                            }
                            if (response.errors) {
                                Swal.fire({
                                    title: "Deseja Inserir os dados?",
                                    text: response.errors[0],
                                    icon: "warning",
                                    showCancelButton: true,
                                    confirmButtonColor: "#3085d6",
                                    cancelButtonColor: "#d33",
                                    cancelButtonText: "Cancelar!",
                                    confirmButtonText: "Sim, confirmar!"
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        $.ajax({
                                            url: ajaxUrl,
                                            type: 'POST',
                                            data: {
                                                type: 'success',
                                                dados: JSON.stringify(data)
                                            },
                                            success: function (response) {
                                                Swal.fire({
                                                    title: "Dados Inseridos!",
                                                    text: "Seus dados foram inserido com sucesso.",
                                                    icon: "success"
                                                });
                                            }
                                        });

                                    }
                                });

                            }
                        },
                        error: function () {
                            alert('Erro ao salvar os dados.');
                        }
                    });

                }
            });

        } else {
            alert('Nenhum dado para salvar.');
        }
    });

    const insertionType = document.getElementById("insertionType");
    const monthSelection = document.getElementById("monthSelection");
    const quarterSelection = document.getElementById("quarterSelection");
    const yearSelect = document.getElementById("selectedYear");

    // Preenche dinamicamente os anos no select
    const currentYear = new Date().getFullYear();
    const totalYears = 30; // você pode ajustar esse valor se quiser mais ou menos anos

    for (let i = 0; i < totalYears; i++) {
        const year = currentYear - i;
        const option = document.createElement("option");
        option.value = year;
        option.text = year;
        yearSelect.appendChild(option);
    }

    function updateSelections() {
        const selected = insertionType.value;

        if (selected === "anual") {
            monthSelection.style.display = "none";
            quarterSelection.style.display = "none";
        } else if (selected === "mensal") {
            monthSelection.style.display = "block";
            quarterSelection.style.display = "none";
        } else if (selected === "trimestral") {
            monthSelection.style.display = "none";
            quarterSelection.style.display = "block";
        }
    }

    insertionType.addEventListener("change", updateSelections);
    updateSelections(); // inicializa ao carregar


    document.getElementById('fileInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const reader = new FileReader();

        reader.onload = function (event) {

            const data = new Uint8Array(event.target.result);
            const workbook = XLSX.read(data, { type: 'array' });

            const sheet = workbook.Sheets['DRE'];
            const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            const bpStartIndex = jsonData.findIndex(row =>
                row[0] && row[0].toString().includes('Demonstração de Resultado do Exercício')
            );

            for (const row of jsonData) {
                if (Array.isArray(row) && row[0]?.toUpperCase() === '(=) RECEITA OPERACIONAL LÍQUIDA') {
                    // Verifica se algum dos valores (do índice 1 em diante) é diferente de 0
                    const temValor = row.slice(1).some(valor => {
                        return valor !== null && valor !== undefined && parseFloat(valor) !== 0;
                    });

                    if (temValor) {
                        console.log('(=) RECEITA OPERACIONAL LÍQUIDA encontrado com valores:', row);
                    } else {
                        console.log('(=) RECEITA OPERACIONAL LÍQUIDA encontrado, mas todos os valores são 0.');
                        Swal.fire({
                            title: "Erro ao Importar",
                            text: "Parece que sua planilha não tem valores reais no (=) RECEITA OPERACIONAL LÍQUIDA.",
                            icon: "error"
                        });
                        return;
                    }
                }
            }
            if (bpStartIndex === -1) {
                Swal.fire({
                    title: "Erro ao Importar",
                    text: "DRE não encontrado!",
                    icon: "error"
                });
                return;
            }

            $('#yearSelection').hide();
            $('#addColumn').hide();

            const headerRow = jsonData[bpStartIndex];

            const bpRows = [];

            for (let i = bpStartIndex + 1; i < jsonData.length; i++) {
                const row = jsonData[i];
                if (!row[0] || row[0].toString().toUpperCase().includes('DRE')) break;
                bpRows.push(row);
            }

            // Remove thead anterior se existir
            $('#dataTable thead').remove();

            // Cria thead e tr
            const $thead = $('<thead>');
            const $trHead = $('<tr>', { class: 'bg-indigo-100' });

            // Primeiro th vazio com div
            const $firstTh = $('<th>', { class: 'p-2 bg-white' }).append('<div></div>');
            $trHead.append($firstTh);

            // Para cada ano, cria um th com botão e texto formatado
            headerRow.slice(1).forEach(ano => {
                const $th = $('<th>', {
                    class: 'border text-center border-slate-300 p-2 bg-slate-100 text-dark relative year_select',
                    'data-ano': ano
                });

                $th.append(`${ano}`);
                $trHead.append($th);
            });

            // Adiciona o tr ao thead e insere no início da tabela
            $thead.html($trHead);
            $('#dataTable').prepend($thead);
            // 1. Antes do loop principal
            let anos = $('thead th:not(:first-child)').filter(function () {
                return $(this).text();
            });
            let qtdColunas = anos.length;

            $('#dataTable tr[data-categoria]').each(function () {
                $(this).find('td:not(:first-child)').remove();

                for (let i = 0; i < qtdColunas; i++) {
                    $(this).append('<td class="p-2 bg-white cursor-pointer border border-b text-right"></td>');
                }
            });

            // 2. Loop nas linhas importadas
            for (const row of bpRows) {
                const categoriaExcel = row[0];
                const valores = row.slice(1);

                const todosZeradosOuVazios = valores.every(valor =>
                    valor === null || valor === undefined || valor === '' || Number(valor) === 0
                );
                if (todosZeradosOuVazios) continue;

                const nomeCategoriaNormalizado = categoriaExcel.trim().toLowerCase().replace(/\s+/g, '_');

                const $tr = $(`tr[data-categoria]`).filter(function () {
                    const nomeTd = $(this).find('td:first').text().trim().toLowerCase().replace(/\s+/g, '_');
                    return nomeTd === nomeCategoriaNormalizado;
                });

                valores.forEach((valor, index) => {
                    const ano = headerRow[index + 1];

                    const input = $('<input>', {
                        type: 'text',
                        val: adicionarVirgulas(valor.toFixed(2).replace(".", ",")),
                        class: 'w-full focus:outline-none bg-transparent text-right input_number',
                        'data-category': nomeCategoriaNormalizado,
                        'data-year': ano
                    });

                    const td = $('<td>', {
                        'data-input': 'true',
                        'data-category': nomeCategoriaNormalizado,
                        'data-year': ano,
                        class: 'p-2 bg-white cursor-pointer border border-b text-right bg-slate-300 category-' + nomeCategoriaNormalizado + '-' + ano,
                    }).html(input);

                    // Aplica AutoNumeric ao input
                    const anElement = new AutoNumeric(input[0], {
                        digitGroupSeparator: '.',
                        decimalCharacter: ',',
                        decimalPlaces: 2,
                        modifyValueOnWheel: false,
                        unformatOnSubmit: true,
                        minimumValue: '0',
                        allowNegative: true, // 👈 permite digitar "-"
                        minimumValue: '-10000000000000', // 👈 agora permite valores negativos
                        maximumValue: '10000000000000',
                    });

                    input.on('input', function () {
                        var soma = new BigNumber(0);

                        $(`input[data-category="${nomeCategoriaNormalizado}"][data-year="${ano}"]`).each(function () {
                            const anInstance = AutoNumeric.getAutoNumericElement(this);
                            let valor = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                            soma = soma.plus(valor);
                        });

                        updateTotalValues();
                        calcularDRE();  // Rodar os cálculos com o novo valor
                    });
                    // Quando sair do input (blur)
                    input.on('blur', function () {
                        const anInstance = AutoNumeric.getAutoNumericElement(this);
                        const valorFormatado = anInstance ? anInstance.getFormatted() : currentText;

                        td.html(valorFormatado); // substitui input pelo valor final
                    });
                    // ⚠️ Posiciona corretamente o valor no <td> do ano correspondente
                    const indexAno = anos.toArray().findIndex(th => $(th).text().trim() == ano);
                    if (indexAno !== -1) {
                        $tr.find('td:not(:first-child)').eq(indexAno).html(td.html());
                    }
                });
            }
        };

        reader.readAsArrayBuffer(file);
    });
});