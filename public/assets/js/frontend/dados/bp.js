$(document).ready(function () {
    var editedCells = new Set();
    var months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    var quarters = ['1T', '2T', '3T', '4T'];
    var tableHeaders = [];
    var tableData = [];
    var categoryCounter = 0;

    var nonEditableCategories = [
        'ATIVO', 'ATIVO CIRCULANTE', 'ATIVO NÃO CIRCULANTE', 'Realizável a Longo Prazo',
        'PASSIVO E PATRIMÔNIO LÍQUIDO', 'PASSIVO CIRCULANTE', 'PASSIVO NÃO CIRCULANTE',
        'PATRIMÔNIO LÍQUIDO', 'Reservas'
    ];

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

        // Atualizar corpo da tabela
        var tbody = $('#dataTable tbody');

        var convertHeader = '';
        // Manter as células de categoria existentes
        tbody.find('tr').each(function (indtrex) {
            var $row = $(this);
            var category = $row.find('td:first').text().trim();
            var category_body = $row.closest('tr').data('categoria');
            var category_year = $row.closest('thead').data('ano');
            var dataAno = $('.year_select').data('ano'); // Pega o valor do atributo data-ano 
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

                if (category === 'ATIVO CIRCULANTE') {
                    cellClass += ` category-ativo_circulante-${convertHeader}`;
                }
                if (category === 'Realizável a Longo Prazo') {
                    cellClass += ` category-realizavel_a_longo_prazo-${convertHeader}`;
                }
                if (category === 'Reservas') {
                    cellClass += ` category-reservas-${convertHeader}`;
                }
                var displayValue = cellData.value === null ? '' : formatNumber(cellData.value);

                var convertHeader = header.replace(/[\/\.]/g, '');

                // Adicionar classe para células não editáveis
                if (nonEditableCategories.includes(category)) {
                    cellClass += `bg-slate-300 cursor-not-allowed addActivetotal category-${category_body}-${convertHeader}`;
                    displayValue = '';
                }

                $row.append(`<td class='p-2 bg-white cursor-pointer border border-b text-right ${cellClass}' data-year='${convertHeader}'>${displayValue}</td>`);
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


    $(document).on('click', '.removeColumns', function (e) {
        e.stopPropagation();

        // Remover dados da coluna removida
        tableData.forEach(function (rowData) {
            delete rowData.values[removedHeader];
        });

    });
    function enableInlineEditing() {
        $('#dataTable tbody td:not(:first-child)').off('click').on('click', function (e) {

            var $cell = $(this);
            // Evita múltiplos inputs na mesma célula
            if ($cell.find('input').length > 0) return;

            var category = $cell.closest('tr').data('categoria');
            var category_year = $cell.closest('td').data('year');
            var currentValue = $cell.text().replace(/\./g, '').replace(',', '.');

            // Pega o nome da categoria a partir da primeira célula da linha
            var category_n = $cell.closest('tr').find('td:first').text().trim().toLowerCase().replace(/\s+/g, '_');

            var input = $('<input>', {
                type: 'text',
                val: $cell.text().trim(),
                class: `w-full focus:outline-none bg-transparent text-right input_number`, // Remova a parte da classe específica
                'data-category': category_n, // Agora usando o valor correto da categoria
                'data-year': category_year // Atribui o ano como data
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

                $(`.category-${category}-${category_year}`).empty().html(
                    adicionarVirgulas(soma.isEqualTo(0) ? "0,0000" : soma.toFixed(2).replace(".", ","))
                );

                recalcularAtivoTotal(category_year);
                recalcularAtivoCirculante(category_year);
                recalcularRealizavelLongoPrazo(category_year);
                recalcularReservas(category_year);
                recalcularTotalPassivo(category_year);
            });
            // Quando sair do input (blur)
            // Salva valor ao sair
            input.on('blur', function () {
                var soma = new BigNumber(0);
                $(`input[data-category="${category_n}"][data-year="${category_year}"]`).each(function () {
                    const anInstance = AutoNumeric.getAutoNumericElement(this);
                    let valor = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    soma = soma.plus(valor);
                });

                $(`.category-${category}-${category_year}`).empty().html(
                    adicionarVirgulas(soma.isEqualTo(0) ? "0,0000" : soma.toFixed(2).replace(".", ","))
                );

                // Recalcula totais
                recalcularAtivoTotal(category_year);
                recalcularAtivoCirculante(category_year);
                recalcularRealizavelLongoPrazo(category_year);
                recalcularReservas(category_year);
                recalcularTotalPassivo(category_year);
            });
        });
    }
    function recalcularAtivoCirculante(category_year) {
        let soma = new BigNumber(0);
        let dentro = false;

        $('#dataTable tbody tr').each(function () {
            let categoria = $(this).find('td:first').text().trim();

            if (categoria === 'ATIVO CIRCULANTE') {
                dentro = true;
                return;
            }
            if (categoria === 'ATIVO NÃO CIRCULANTE') {
                dentro = false;
                return;
            }

            if (dentro) {
                let input = $(this).find(`input[data-year="${category_year}"]`);
                if (input.length > 0) {
                    const anInstance = AutoNumeric.getAutoNumericElement(input[0]);
                    let val = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    soma = soma.plus(val);
                }
            }
        });

        $(`.category-ativo_circulante-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(soma.toFixed(2).replace(".", ","))}">
    `);

        return soma;
    }

    function recalcularRealizavelLongoPrazo(category_year) {
        let soma = new BigNumber(0);
        let dentro = false;

        $('#dataTable tbody tr').each(function () {
            let categoria = $(this).find('td:first').text().trim();

            if (categoria === 'Realizável a Longo Prazo') {
                dentro = true;
                return;
            }
            if (['Investimentos', 'Imobilizado', 'Intangível', 'PASSIVO E PATRIMÔNIO LÍQUIDO'].includes(categoria)) {
                dentro = false;
                return;
            }

            if (dentro) {
                let input = $(this).find(`input[data-year="${category_year}"]`);
                if (input.length > 0) {
                    const anInstance = AutoNumeric.getAutoNumericElement(input[0]);
                    let val = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    soma = soma.plus(val);
                }

            }
        });

        $(`.category-realizavel-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(soma.toFixed(2).replace(".", ","))}">
    `);

        return soma;
    }

    function recalcularAtivoNaoCirculante(category_year) {
        let soma = new BigNumber(0);
        let dentro = false;

        $('#dataTable tbody tr').each(function () {
            let categoria = $(this).find('td:first').text().trim();

            if (categoria === 'ATIVO NÃO CIRCULANTE') {
                dentro = true;
                return;
            }
            if (categoria === 'PASSIVO E PATRIMÔNIO LÍQUIDO') {
                dentro = false;
                return;
            }

            if (dentro) {
                let input = $(this).find(`input[data-year="${category_year}"]`);
                if (input.length > 0) {
                    const anInstance = AutoNumeric.getAutoNumericElement(input[0]);
                    let val = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    soma = soma.plus(val);
                }

            }
        });

        $(`.category-ativo_nao_circulante-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(soma.toFixed(2).replace(".", ","))}">
    `);

        return soma;
    }

    function recalcularAtivoTotal(category_year) {
        let totalAtivoCirc = recalcularAtivoCirculante(category_year);
        let totalAtivoNaoCirc = recalcularAtivoNaoCirculante(category_year);

        let somaTotal = totalAtivoCirc.plus(totalAtivoNaoCirc);

        $(`.category-ativo-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(somaTotal.toFixed(2).replace(".", ","))}">
    `);
    }

    function recalcularPassivoCirculante(category_year) {
        let soma = new BigNumber(0);
        let dentro = false;

        $('#dataTable tbody tr').each(function () {
            let categoria = $(this).find('td:first').text().trim();

            if (categoria === 'PASSIVO CIRCULANTE') {
                dentro = true;
                return;
            }
            if (categoria === 'PASSIVO NÃO CIRCULANTE') {
                dentro = false;
                return;
            }

            if (dentro) {
                let input = $(this).find(`input[data-year="${category_year}"]`);
                if (input.length > 0) {
                    const anInstance = AutoNumeric.getAutoNumericElement(input[0]);
                    let val = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    soma = soma.plus(val);
                }

            }
        });

        $(`.category-passivo_circulante-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(soma.toFixed(2).replace(".", ","))}">
    `);

        return soma;
    }
    function recalcularPassivoNaoCirculante(category_year) {
        let soma = new BigNumber(0);
        let dentro = false;

        $('#dataTable tbody tr').each(function () {
            let categoria = $(this).find('td:first').text().trim();

            if (categoria === 'PASSIVO NÃO CIRCULANTE') {
                dentro = true;
                return;
            }
            if (categoria === 'PATRIMÔNIO LÍQUIDO') {
                dentro = false;
                return;
            }

            if (dentro) {
                let input = $(this).find(`input[data-year="${category_year}"]`);
                if (input.length > 0) {
                    const anInstance = AutoNumeric.getAutoNumericElement(input[0]);
                    let val = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    soma = soma.plus(val);
                }

            }
        });

        $(`.category-passivo_nao_circulante-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(soma.toFixed(2).replace(".", ","))}">
    `);

        return soma;
    }

    function recalcularPatrimonioLiquido(category_year) {
        let somaPL = new BigNumber(0);
        let dentroPL = false;

        $('#dataTable tbody tr').each(function () {
            let categoria = $(this).find('td:first').text().trim();

            if (categoria === 'PATRIMÔNIO LÍQUIDO') {
                dentroPL = true;
                return;
            }

            if (['PASSIVO E PATRIMÔNIO LÍQUIDO'].includes(categoria)) {
                dentroPL = false;
                return;
            }

            if (dentroPL) {
                // Ignora linhas de subtotal, como "Reservas"
                if (categoria !== 'Reservas') {
                    let input = $(this).find(`input[data-year="${category_year}"]`);
                    if (input.length > 0) {
                        const anInstance = AutoNumeric.getAutoNumericElement(input[0]);
                        let val = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                        somaPL = somaPL.plus(val);
                    }

                }
            }
        });

        $(`.category-patrimonio_liquido-${category_year}`).html(`
                <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
                    value="${adicionarVirgulas(somaPL.toFixed(2).replace(".", ","))}">
            `);

        return somaPL;
    }

    function recalcularTotalPassivo(category_year) {
        let totalCirc = recalcularPassivoCirculante(category_year);
        let totalNaoCirc = recalcularPassivoNaoCirculante(category_year);
        let patrimonioLiquido = recalcularPatrimonioLiquido(category_year);

        let somaTotal = totalCirc.plus(totalNaoCirc).plus(patrimonioLiquido);

        $(`.category-passivo_e_patrimonio_liquido-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(somaTotal.toFixed(2).replace(".", ","))}">
    `);
    }

    function recalcularReservas(category_year) {
        let somaReservas = new BigNumber(0);
        let dentroReservas = false;

        $('#dataTable tbody tr').each(function () {
            let categoria = $(this).find('td:first').text().trim();

            // Começa quando encontrar "Reservas de Capital"
            if (categoria === 'Reservas de Capital') {
                dentroReservas = true;
            }

            // Para quando passar das Reservas
            if (dentroReservas && !['Reservas de Capital', 'Reservas de Reavaliação', 'Reservas de Lucros'].includes(categoria)) {
                dentroReservas = false;
            }

            // Se estiver dentro das Reservas, somar
            if (dentroReservas) {
                let input = $(this).find(`input[data-year="${category_year}"]`);
                if (input.length > 0) {
                    const anInstance = AutoNumeric.getAutoNumericElement(input[0]);
                    let val = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                    somaReservas = somaReservas.plus(val);
                }

            }
        });

        $(`.category-reservas-${category_year}`).html(`
        <input disabled class="cursor-not-allowed w-full focus:outline-none bg-transparent text-right"
               value="${adicionarVirgulas(somaReservas.toFixed(2).replace(".", ","))}">
    `);

        return somaReservas;
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

    $('#resetTable').click(function () {
        tableHeaders = [];
        editedCells.clear();


        $('#yearSelection').show();
        $('#addColumn').show();

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

        $('#saving').css('display', 'flex');

        ajaxUrl = '../../dados/balanco_patrimonial';

        const list_values = {
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
        })

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
                        url: ajaxUrl,
                        type: 'POST',
                        data: {
                            dados: JSON.stringify(data)
                        },
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
            $('#saving').css('display', 'none');
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

            const sheet = workbook.Sheets['BP'];
            const jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

            const bpStartIndex = jsonData.findIndex(row =>
                row[0] && row[0].toString().includes('Balanço Patrimonial')
            );

            for (const row of jsonData) {
                if (Array.isArray(row) && row[0]?.toUpperCase() === 'ATIVO') {
                    // Verifica se algum dos valores (do índice 1 em diante) é diferente de 0
                    const temValor = row.slice(1).some(valor => {
                        return valor !== null && valor !== undefined && parseFloat(valor) !== 0;
                    });

                    if (temValor) {
                        console.log('ATIVO encontrado com valores:', row);
                    } else {
                        console.log('ATIVO encontrado, mas todos os valores são 0.');
                        Swal.fire({
                            title: "Erro ao Importar",
                            text: "Parece que sua planilha não tem valores reais no ATIVO.",
                            icon: "error"
                        });
                        return;
                    }
                }
            }
            if (bpStartIndex === -1) {
                Swal.fire({
                    title: "Erro ao Importar",
                    text: "Balanço Patrimonial não encontrado!",
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
                    console.log(ano)
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

                    // AutoNumeric
                    const anElement = new AutoNumeric(input[0], {
                        digitGroupSeparator: '.',
                        decimalCharacter: ',',
                        decimalPlaces: 2,
                        modifyValueOnWheel: false,
                        unformatOnSubmit: true,
                        minimumValue: '-10000000000000',
                        maximumValue: '10000000000000',
                        allowNegative: true,
                    });

                    input.on('input blur', function () {
                        var soma = new BigNumber(0);
                        $(`input[data-category="${nomeCategoriaNormalizado}"][data-year="${ano}"]`).each(function () {
                            const anInstance = AutoNumeric.getAutoNumericElement(this);
                            let valor = new BigNumber(anInstance ? anInstance.getNumber() : 0);
                            soma = soma.plus(valor);
                        });

                        // Recalcular os totais
                        recalcularAtivoTotal(ano);
                        recalcularAtivoCirculante(ano);
                        recalcularRealizavelLongoPrazo(ano);
                        recalcularReservas(ano);
                        recalcularTotalPassivo(ano);
                    });
                    // ⚠️ Posiciona corretamente o valor no <td> do ano correspondente
                    const indexAno = anos.toArray().findIndex(th => $(th).text() == ano);
                    if (indexAno !== -1) {
                        $tr.find('td:not(:first-child)').eq(indexAno).html(td.html());
                    }
                });
            }
        };

        reader.readAsArrayBuffer(file);
    });
});

