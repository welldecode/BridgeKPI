$(document).ready(function () {
    var editedCells = new Set();
    var months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    var quarters = ['1T', '2T', '3T', '4T'];
    var tableHeaders = [];
    var tableData = [];
    var categoryCounter = 0;

    // Inicializar tableData com as categorias fixas
    function initializeTableData() {
        $('#dataTable tbody tr').each(function () {
            var $row = $(this);
            var category = $row.find('td:first').text().trim();
            tableData.push({
                id: ++categoryCounter,
                category: category,
                values: {}
            });
        });
    }

    var nonEditableCategories = [
        'RECEITA OPERACIONAL BRUTA',
        '(-) DEDUÇÕES DA RECEITA BRUTA',
        '(=) RESULTADO OPERACIONAL BRUTO',
        '(-) DESPESAS OPERACIONAIS',
        'Despesas com Vendas',
        '(=) EBITDA',
        '(=) EBIT',
        '(=) RESULTADO ANTES DO RESULTADO FINANCEIRO',
        '(-) DESPESAS FINANCEIRAS LÍQUIDAS',
        '(=) RESULTADO ANTES DO IR E CSLL',
        '(=) RESULTADO LÍQUIDO DO EXERCÍCIO'
      ];

    function updateTable() {
        // Atualizar cabeçalho da tabela
        var headerRow = '<tr class="bg-indigo-100">';
        headerRow += '<th class="p-2 bg-white">Demonstração do resultado do exercício - BRL</th>';
        tableHeaders.forEach(function (header) {
            headerRow += `<th class="border text-center border-slate-300 p-2 bg-slate-100 text-dark relative">
             <button class="removeColumn w-full bg-red rounded mb-2 text-white hover:bg-red">X</button>
                        ${header}
                        
                      </th>`;
        });
        headerRow += '</tr>';
        $('#dataTable thead').html(headerRow);

        // Atualizar corpo da tabela
        var tbody = $('#dataTable tbody');

        // Manter as células de categoria existentes
        tbody.find('tr').each(function (indtrex) {
            var $row = $(this);
            var category = $row.find('td:first').text().trim();
            var category_body = $row.closest('tbody').data('categoria');

            var rowData = tableData.find(data => data.category === category) || {
                values: {}
            };

            // Remover células de dados existentes
            $row.find('td:not(:first-child)').remove();

            // Adicionar novas células de dados
            tableHeaders.forEach(function (header) {
                var cellData = rowData.values[header] || {
                    value: null,
                    edited: false
                };
                var cellClass = cellData.edited ? 'border-yellow-500 border-2 table_rows' : '';
                var displayValue = cellData.value === null ? '' : formatNumber(cellData.value);

                // Adicionar classe para células não editáveis
                if (nonEditableCategories.includes(category)) {
                    cellClass += ' bg-slate-300 cursor-not-allowed addActivetotal category-' + category_body;
                    displayValue = '';
                }

                $row.append(`<td class='p-2 bg-white cursor-pointer border border-b text-right ${cellClass}'>${displayValue}</td>`);
            });
        });

        enableInlineEditing();
    }

    function formatNumber(num) {
        return parseFloat(num).toLocaleString('pt-BR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2,
            useGrouping: true,
            style: 'decimal'
        });
    }

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

                // Se não houver ano (caso anual), use o valor como ano
                if (!aYear) {
                    aYear = aValue;
                    aValue = '';
                }
                if (!bYear) {
                    bYear = bValue;
                    bValue = '';
                }

                // Comparar anos primeiro
                if (aYear !== bYear) return parseInt(aYear) - parseInt(bYear);

                // Se os anos forem iguais, comparar meses ou trimestres
                if (aValue && bValue) {
                    if (months.includes(aValue) && months.includes(bValue)) {
                        return months.indexOf(aValue) - months.indexOf(bValue);
                    } else if (quarters.includes(aValue) && quarters.includes(bValue)) {
                        return quarters.indexOf(aValue) - quarters.indexOf(bValue);
                    }
                }

                // Para anual, a comparação de anos já foi feita
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


    function enableInlineEditing() {

        $('#dataTable tbody td:not(:first-child)').off('click').on('click', function (e) {

            var $cell = $(this);

            var category = $cell.closest('tbody').data('categoria');
            console.log(category);


            var currentValue = $cell.text().replace(/\./g, '').replace(',', '.');


            var input = $('<input>')
                .val(currentValue)
                .addClass('w-full focus:outline-none bg-transparent text-right input_number category-' + category)
                .attr('type', 'text');

            $cell.html(input);
            input.focus();

            input.on('input', function () {
                var value = $(this).val().replace(/\D/g, '');
                if (value === '') {
                    $(this).val('');
                    return;
                }

                var numericValue = parseInt(value, 10);
                if (isNaN(numericValue)) {
                    $(this).val('');
                    return;
                }

                var formattedValue = (numericValue / 100).toLocaleString('pt-BR', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2,
                    style: 'decimal'
                });

                $(this).val(formattedValue);

                var soma = 0; // Variável para armazenar a soma dos valores 


                const categoryItems = $(`input.category-${category}`);

                categoryItems.each(function (e, i) {

                    var nomeDaLinha = convertToSlug($('tbody[data-categoria="' + category + '"]').find("td:first").text()); // Pega o nome da primeira célula da linha

                    const valor = $(this).val().replace('.', '').replace(',', '.');
                    const numero = parseFloat(valor);
                    if (!isNaN(numero)) {
                        soma += numero;
                    }
                });


                var input = $('<input>')
                    .val(format('pt-BR', 'BRL', soma))
                    .addClass('w-full focus:outline-none bg-transparent text-right input_number category-' + category)
                    .attr('type', 'text');

                $(`.addActivetotal.category-${category}`).html(input);

                var somaTotal = 0; // Variável para armazenar a soma dos valores  

                const categoryAllItems = $(`td input`);

                categoryAllItems.each(function (e, i) {

                    const valorT = $(this).val().replace('.', '').replace(',', '.'); // Remove milhar e ajusta decimal
                    const numeroTotal = parseFloat(valorT); // Converte para número
                    if (!isNaN(numeroTotal)) { // Verifica se é um número válido
                        somaTotal += numeroTotal;
                    }
                });
  
                var input_Ativo = $('<input>')
                    .val(format('pt-BR', 'BRL', somaTotal))
                    .addClass('w-full focus:outline-none bg-transparent text-right input_number')
                    .attr('type', 'text');

                $('.category-ativo').html(input_Ativo); 
            });

        });

    }

    function format(locale, currency, number) {
        return new Intl.NumberFormat(locale, {
            style: 'currency',
            currency,
            currencyDisplay: "code"
        })
            .format(number)
            .replace(currency, "")
            .trim();
    }
    function convertToSlug(str) {
        str = str.replace(/[`~!@#$%^&*()_\-+=\[\]{};:'"\\|\/,.<>?\s]/g, ' ')
            .toLowerCase();

        str = str.replace(/^\s+|\s+$/gm, '');

        str = str.replace(/\s+/g, '_');
        return str;
    }

    $('#resetTable').click(function () {
        tableHeaders = [];
        editedCells.clear();

        // Manter as categorias, mas limpar os valores
        tableData.forEach(function (rowData) {
            rowData.values = {};
        });

        updateTable();
    });

    $('#clearTable').click(function () {
        tableData.forEach(function (rowData) {
            Object.keys(rowData.values).forEach(function (header) {
                rowData.values[header] = {
                    value: null,
                    edited: false
                };
            });
        });
        editedCells.clear();
        updateTable();
    });

    $('#saveData').click(function () {
        var data = [];
        var insertionType = $('#insertionType').val();
        var ajaxUrl = '';

        $('#saving').css('display', 'flex');

        switch (insertionType) {
            case 'anual':
                ajaxUrl = '../../dados/dre';
                break;
            case 'mensal':
                ajaxUrl = '../../dados/dre';
                break;
            case 'trimestral':
                ajaxUrl = '../../dados/dre';
                break;
        }


        $('#dataTable tbody tr').each(function (index) {
            var $row = $(this);
            var category = $row.find('td:first').text().trim();

            $row.find('input').each(function (cellIndex) {

                var $cell = $(this);
                var header = tableHeaders[cellIndex];
                var decimalValue = $.trim($(this).val()).replace('.', '').replace(',', '.');

                data.push({
                    id: index + 1,
                    categoria: category,
                    periodo: header,
                    valor: decimalValue === null ? null : decimalValue
                });
            });
        });

        if (data.length > 0) {
            $.ajax({
                url: ajaxUrl,
                type: 'POST',
                data: {
                    dados: JSON.stringify(data)
                },

                success: function (response) {
                    $('#savingLoading').removeClass('animate-spin border-t-2 border-b-2 border-blue-500')
                        .addClass('text-green-500 text-5xl')
                        .html('<i class="fas fa-check-circle"></i>');
                    $('#savingData').text('Dados salvos com sucesso!');
                    setTimeout(function () {
                        $('#saving').css('display', 'none');
                        $('#savingLoading').removeClass('text-green-500 text-5xl')
                            .addClass('animate-spin border-t-2 border-b-2 border-blue-500')
                            .html('');
                    }, 3000);
                },
                error: function () {
                    alert('Erro ao salvar os dados.');
                    $('#saving').css('display', 'none');
                }
            });
        } else {
            alert('Nenhum dado para salvar.');
            $('#saving').css('display', 'none');
        }
    });

}); 