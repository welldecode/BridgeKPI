<?php

namespace App\Livewire\Admin\Analise;

use App\Models\BalancoPatrimonial;
use App\Models\Dres;
use App\Models\Indicator;
use App\Models\IndicatorValue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Closure;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Indicadores extends Component
{


    public function mount()
    {
        Carbon::setLocale('pt_BR');  // Define o local como português
    }
    public function render()
    {
        return view('livewire.admin.analise.indicadores')->layout('layouts.admin');
    }

    #[Validate('required')] // 1MB Max 
    public $tipo = 'anual';
    #[Validate('required')] // 1MB Max 
    public $anos = [];
    #[Validate('required')] // 1MB Max 
    public $meses = [];
    #[Validate('required')] // 1MB Max 
    public $trimestres = [];

    #[Validate('required')] // 1MB Max 
    public $indicadores = [];
    public $resultado = [];
    public $erro = null;

    protected $cacheBP = [];
    protected $cacheDRE = [];

    public $totalSteps = 3;
    public $currentStep = 1;


    public function increaseStep()
    {
        $this->resetErrorBag();
        $this->validateData();
        $this->currentStep++;
        if ($this->currentStep > $this->totalSteps) {
            $this->currentStep = $this->totalSteps;
        }
    }

    public function decreaseStep()
    {
        $this->resetErrorBag();
        $this->currentStep--;
        if ($this->currentStep < 1) {
            $this->currentStep = 1;
        }
    }


    public function validateData()
    {
        if ($this->currentStep == 1) {
            $this->validate([
                'indicadores' => 'required',
            ]);
        } elseif ($this->currentStep == 2) {
            $this->validate([
                'anos' => 'required',
            ]);
        }
    }
    public function createReport()
    {
        try {
            $dados = [];

            // Mapeamento dos indicadores por grupo
            $gruposIndicadores = [
                'EBIT' => 'Fluxo de Caixa Livre da Firma (FCFF)',
                'ir_e_csll' => 'Fluxo de Caixa Livre da Firma (FCFF)',
                'no_pat' => 'Fluxo de Caixa Livre da Firma (FCFF)',
                'depreciacao_e_amortização' => 'Fluxo de Caixa Livre da Firma (FCFF)',
                'fluxo_de_caixa_operacional' => 'Fluxo de Caixa Livre da Firma (FCFF)',
                'investimentos_fixos' => 'Fluxo de Caixa Livre da Firma (FCFF)',
                'investimento_em_capital_de_giro' => 'Fluxo de Caixa Livre da Firma (FCFF)',

                // ... adicione outros indicadores e seus grupos aqui
            ];

            // Dependências: indicadores que precisam vir juntos
            $dependenciasIndicadores = [
                'custos_e_despesas_fixas' => [
                    'custos_e_despesas_fixas_porcental_do_rol',
                ],
                'custos_e_despesas_variaveis' => [
                    'custos_e_despesas_variaveis_porcental'
                ],
                'lucro_líquido_do_exercício' => [
                    'margem_líquida'
                ],
                'identidade_dupont' => [
                    'margem_líquida_ll_rol',
                    'giro_do_ativo_total',
                    'multiplicadorPl',
                    'rentabilidade_roe'
                ],
                'analise_do_grau_de_alavancagem_financeira_gaf' => [
                    'gaf',
                    'roe_retorno_sobre_o_pl',
                    'roic',
                    'ganho_pela_alavancagem_financeira',
                ],
                'fluxo_de_caixa_livre_da_firma' => [
                    'EBIT',
                    'ir_e_csll',
                    'no_pat',
                    'depreciacao_e_amortização',
                    'fluxo_de_caixa_operacional',
                    'investimentos_fixos',
                    'investimento_em_capital_de_giro',
                ],
            ];

            // Dados principais
            foreach ($this->resultado as $nome => $valores) {
                $dados[$nome] = [
                    'unit' => 'R$',
                    'valores' => [],
                    'grupo' => $gruposIndicadores[$nome] ?? 'Outros',
                ];

                foreach ($valores as $periodo => $valor) {
                    $dados[$nome]['valores'][$periodo] = $valor;
                }
            }

            // Adiciona indicadores dependentes
            foreach ($this->resultado as $nome => $_) {
                if (isset($dependenciasIndicadores[$nome])) {
                    foreach ($dependenciasIndicadores[$nome] as $dependente) {
                        if (!isset($dados[$dependente])) {
                            $dados[$dependente] = [
                                'unit' => 'R$',
                                'valores' => [],
                                'grupo' => $gruposIndicadores[$dependente] ?? 'Outros',
                            ];

                            foreach ($this->anos as $ano) {
                                $periodos = match ($this->tipo) {
                                    'mensal' => $this->meses,
                                    'trimestral' => $this->trimestres,
                                    default => [null],
                                };

                                foreach ($periodos as $periodo) {
                                    $coluna = match ($this->tipo) {
                                        'mensal' => $periodo . '/' . $ano,
                                        'trimestral' => $periodo . 'T/' . $ano,
                                        default => $ano,
                                    };

                                    $valor = $this->calcularIndicador($dependente, $ano, $periodo);
                                    if (is_callable($valor)) {
                                        $valor = $valor();
                                    }
                                    $dados[$dependente]['valores'][$coluna] = $valor;
                                }
                            }
                        }
                    }
                }
            }

            // Indicadores obrigatórios
            $indicadoresObrigatorios = array_keys($gruposIndicadores);

            foreach ($indicadoresObrigatorios as $indicador) {
                if (isset($dados[$indicador])) {
                    continue;
                }

                foreach ($this->anos as $ano) {
                    $periodos = match ($this->tipo) {
                        'mensal' => $this->meses,
                        'trimestral' => $this->trimestres,
                        default => [null],
                    };

                    foreach ($periodos as $periodo) {
                        $coluna = match ($this->tipo) {
                            'mensal' => $periodo . '/' . $ano,
                            'trimestral' => $periodo . 'T/' . $ano,
                            default => $ano,
                        };

                        $valor = $this->calcularIndicador($indicador, $ano, $periodo);
                        if (is_callable($valor)) {
                            $valor = $valor();
                        }

                        $dados[$indicador]['unit'] = 'R$';
                        $dados[$indicador]['grupo'] = $gruposIndicadores[$indicador] ?? 'Outros';
                        $dados[$indicador]['valores'][$coluna] = $valor;
                    }
                }
            }

            // ✅ Reforço final: garantir grupo e unidade correta para todos
            foreach ($dados as $nome => &$info) {
                $info['grupo'] = $gruposIndicadores[$nome] ?? 'Outros';
                $info['unit'] = $info['unit'] ?? 'R$';
            }
            // Salva em sessão
            Cache::put('relatorio_temporario_' . auth()->id(), $dados, now()->addHours(6));
             
            session()->flash('success', 'Relatório criado com sucesso (em memória).');
            
            return redirect()->route('analise.relatorio');
        } catch (\Exception $e) {
            LivewireAlert::title('Erro')
                ->text($e->getMessage())
                ->position('center')
                ->error()
                ->timer(6000)
                ->show();
        }
    }
    public function calcular()
    {
        $this->increaseStep();
        $this->resultado = [];
        $this->erro = null;

        try {
            $dadosFormatados = [];

            foreach ($this->anos as $ano) {
                $periodos = match ($this->tipo) {
                    'mensal' => $this->meses,
                    'trimestral' => $this->trimestres,
                    default => [null],
                };

                foreach ($periodos as $periodo) {
                    foreach ($this->indicadores as $indicador) {
                        $valor = $this->calcularIndicador($indicador, $ano, $periodo);
                        if (is_callable($valor)) {
                            $valor = $valor();
                        }

                        // Formatando chave com base no tipo
                        $coluna = match ($this->tipo) {
                            'mensal' => $periodo . '/' . $ano,        // Ex: 1/2025
                            'trimestral' => $periodo . 'T/' . $ano,    // Ex: 1T/2025
                            default => $ano,
                        };

                        $dadosFormatados[$indicador][$coluna] = $valor;
                    }
                }
            }

            $this->resultado = $dadosFormatados;

        } catch (\Exception $e) {
            $this->erro = 'Erro ao calcular: ' . $e->getMessage();
        }
    }
    protected function fromDRE($queryPeriodo, array $categorias, $is_subtraction = false)
    {

        $itens = Dres::where(function ($query) use ($queryPeriodo) {
            $queryPeriodo($query);
        })
            ->whereIn('categoria', $categorias)
            ->get();

        $valorTotal = $itens->sum(function ($item) {
            $valor = str_replace('.', '', $item->valor);
            $valor = str_replace(',', '.', $valor);
            return floatval($valor);
        });

        Log::debug("Valor total do DRE: $valorTotal");

        return $valorTotal;
    }
    protected function fromBP($queryPeriodo, array $categorias)
    {
        $itens = BalancoPatrimonial::where(function ($query) use ($queryPeriodo) {
            $queryPeriodo($query);
        })
            ->whereIn('categoria', $categorias)
            ->get();

        if ($itens->isEmpty()) {
            Log::debug("Nenhum dado encontrado para as categorias: " . implode(", ", $categorias));
            return 0;
        }

        $valorTotal = $itens->sum(function ($item) {
            $valor = str_replace('.', '', $item->valor);
            $valor = str_replace(',', '.', $valor);
            return floatval($valor);
        });

        Log::debug("Valor total do BP: $valorTotal");
        return $valorTotal;
    }
    protected function calcularIndicador($indicador, $ano, $periodo = null)
    {
        $tipo = $this->tipo;

        if ($tipo === 'trimestral') {
            $trimestre = $periodo;
            $periodoAtual = "{$trimestre}T/{$ano}";

            // Corrige para 4T do ano anterior se for 1T
            if ($trimestre == 1) {
                $periodoAnterior = "4T/" . ($ano - 1);
            } else {
                $periodoAnterior = ($trimestre - 1) . "T/" . $ano;
            }

            $queryPeriodo = fn($query) => $query
                ->where('type', $tipo)
                ->where('periodo', $periodoAtual);

            $queryPeriodoAtual = fn($query) => $query
                ->where('type', $tipo)
                ->where('periodo', $periodoAtual);

            $queryPeriodoAnterior = fn($query) => $query
                ->where('type', $tipo)
                ->where('periodo', $periodoAnterior);

        } else if ($tipo === 'mensal' && $periodo !== null) {
            $mesBanco = $periodo - 1; // Ajuste para o mês iniciar em 0

            $queryPeriodo = fn($query) => $query
                ->where('type', $tipo)
                ->where('year', $ano)
                ->where('month', $mesBanco);

            $queryPeriodoAtual = fn($query) => $query
                ->where('type', $tipo)
                ->where('year', $ano)
                ->where('month', $mesBanco);

            $queryPeriodoAnterior = fn($query) => $query
                ->where('type', $tipo)
                ->where('year', $ano)
                ->where('month', $mesBanco - 1);

        } else {
            // Anual ou outro tipo
            $queryPeriodo = fn($query) => $query
                ->where('type', $tipo)
                ->where('year', $ano);

            $queryPeriodoAtual = fn($query) => $query
                ->where('type', $tipo)
                ->where('year', $ano);

            $queryPeriodoAnterior = fn($query) => $query
                ->where('type', $tipo)
                ->where('year', $ano - 1);
        }

        return match ($indicador) {
            'receita_operacional_bruta_(ROB)' => function () use ($queryPeriodo) {
                    $rob = $this->fromDRE($queryPeriodo, ['RECEITA OPERACIONAL BRUTA']);
                    if ($rob == 0)
                        return null;
                    return $rob;
                },
            'deduções_da_receita_bruta_(ROB)' => function () use ($queryPeriodo) {
                    $deducoes = $this->fromDRE($queryPeriodo, ['DEDUÇÕES DA RECEITA BRUTA']);
                    if ($deducoes == 0)
                        return null;
                    return $deducoes;
                },
            'receita_operacional_líquida_(ROL)' => $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']),

            'resultado_de_Op_desc_Resultado_Não_Operacionais' => function () use ($queryPeriodo) {
                    $resultadoNaoOperacional = $this->fromDRE($queryPeriodo, ['Receitas Não Operacionais']);
                    $dp = $this->fromDRE($queryPeriodo, ['Despesas Não Operacionais']);

                    if ($resultadoNaoOperacional + $dp == 0)
                        return null;

                    return $resultadoNaoOperacional + $dp;
                },
            'custos_e_despesas_fixas' => function () use ($queryPeriodo) {
                    $despesasAdministrativas = $this->fromDRE($queryPeriodo, ['Despesas Administrativas e Gerais']);
                    // Recupera os valores das células específicas no BP e DRE 
                    $depreciacaoAmortizacao = $this->fromDRE($queryPeriodo, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);
                    -$despesasFinanceiras = $this->fromDRE($queryPeriodo, ['(-) DESPESAS FINANCEIRAS LÍQUIDAS']);

                    // Soma os valores para calcular os Custos e Despesas Fixas
                    $custosFixos = $despesasAdministrativas + $depreciacaoAmortizacao + $despesasFinanceiras;

                    return -$custosFixos;
                },
            'custos_e_despesas_fixas_porcental_do_rol' => function () use ($queryPeriodo) {
                    $despesasAdministrativas = $this->fromDRE($queryPeriodo, ['Despesas Administrativas e Gerais']);
                    // Recupera os valores das células específicas no BP e DRE 
                    $depreciacaoAmortizacao = $this->fromDRE($queryPeriodo, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);  // Substitua 'Célula F73' pela chave correta
                    $despesasFinanceiras = $this->fromDRE($queryPeriodo, ['(-) DESPESAS FINANCEIRAS LÍQUIDAS']);  // Substitua 'Célula F78' pela chave correta
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);  // Substitua 'Célula F78' pela chave correta
    
                    if ($rol === null || $rol == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    // Soma os valores para calcular os Custos e Despesas Fixas
                    $custosFixos = $despesasAdministrativas + $depreciacaoAmortizacao + $despesasFinanceiras;
                    $custosFixosPercentual = (-$custosFixos / $rol) * 100;
                    return $custosFixosPercentual;
                },
            'custos_e_despesas_variaveis' => function () use ($queryPeriodo) {
                    $valores = $this->fromDRE($queryPeriodo, [
                    '(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS',
                    'Outras Despesas com Vendas',
                    '(-) IR e CSLL',
                    ]);

                    return -$valores; // Deve retornar -8.507.439
                },
            'custos_e_despesas_variaveis_porcental' => function () use ($queryPeriodo) {
                    $valores = $this->fromDRE($queryPeriodo, [
                    '(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS',
                    'Outras Despesas com Vendas',
                    '(-) IR e CSLL',
                    ]);
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);  // Substitua 'Célula F78' pela chave correta
                    if ($rol === null || $rol == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    $custosVariaveisPercentual = ($valores / $rol) * 100;
                    return -$custosVariaveisPercentual; // Deve retornar -8.507.439
                },
            'ponto_de_equilíbrio_contábil' => function () use ($queryPeriodo) {
                    $custosFixos = $this->fromDRE($queryPeriodo, [
                    'Despesas Administrativas e Gerais',
                    '(-) DEPRECIAÇÃO E AMORTIZAÇÃO',
                    '(-) DESPESAS FINANCEIRAS LÍQUIDAS'
                    ]);

                    $custosVariaveis = $this->fromDRE($queryPeriodo, [
                    '(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS',
                    'Outras Despesas com Vendas', // ou a subcategoria que você usa
                    '(-) IR e CSLL'
                    ]);

                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);

                    if ($rol == 0)
                        return null;

                    return -$custosFixos / (($rol + $custosVariaveis) / $rol);
                },
            'margem_líquida' => function () use ($queryPeriodo) {
                    $lucro = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);
                    $receita = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    return $receita != 0 ? round(($lucro / $receita) * 100, 2) : null;
                },

            'patrimônio_líquido' => $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']),
            'retorno_sobre_patrimonio_liquido_roe' => function () use ($queryPeriodo) {
                    $lucroLiquido = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);
                    $patrimonioLiquido = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);

                    if ($patrimonioLiquido == 0)
                        return null;
                    return round(($lucroLiquido / $patrimonioLiquido) * 100, 2); // Retorna como percentual
                },
            'lucro_líquido_do_exercício' => $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']),

            'capital_circulante_líquido' =>
            $this->fromBP($queryPeriodo, ['ATIVO CIRCULANTE'])
            - $this->fromBP($queryPeriodo, ['PASSIVO CIRCULANTE']),

            // 🆕 Liquidez Corrente
            'liquidez_corrente' => function () use ($queryPeriodo) {
                    $ativo = $this->fromBP($queryPeriodo, ['ATIVO CIRCULANTE']);
                    $passivo = $this->fromBP($queryPeriodo, ['PASSIVO CIRCULANTE']);
                    return $passivo != 0 ? ($ativo / $passivo) : null;  // Sem arredondamento
                },

            // 🆕 Liquidez Seca
            'liquidez_seca' => function () use ($queryPeriodo) {

                    $ativoCirculante = $this->fromBP($queryPeriodo, [
                    'ATIVO CIRCULANTE',
                    ]);

                    $estoque = $this->fromBP($queryPeriodo, ['Estoque', 'Despesas Antecipadas']);
                    $passivoCirculante = $this->fromBP($queryPeriodo, ['PASSIVO CIRCULANTE']);

                    // Calcula a liquidez seca excluindo o estoque, sem arredondamento
                    return $passivoCirculante != 0 ? ($ativoCirculante - $estoque) / $passivoCirculante : null;
                },

            // 🆕 Liquidez Imediata
            'liquidez_imediata' => function () use ($queryPeriodo) {
                    $caixa = $this->fromBP($queryPeriodo, ['Caixa e Equivalentes de Caixa']);
                    $aplicacoes = $this->fromBP($queryPeriodo, ['Aplicações Financeiras']);
                    $passivo_circ = $this->fromBP($queryPeriodo, ['PASSIVO CIRCULANTE']);

                    // Sem arredondamento
                    return $passivo_circ != 0 ? ($caixa) / $passivo_circ : null;
                },

            // 🆕 Liquidez Geral
            'liquidez_geral' => function () use ($queryPeriodo) {
                    $ativoCirculante = $this->fromBP($queryPeriodo, ['ATIVO CIRCULANTE']);
                    $realizavelLP = $this->fromBP($queryPeriodo, ['Realizável a Longo Prazo']);
                    $passivoCirculante = $this->fromBP($queryPeriodo, ['PASSIVO CIRCULANTE']);
                    $passivoNaoCirculante = $this->fromBP($queryPeriodo, ['PASSIVO NÃO CIRCULANTE']);
                    $divisor = $passivoCirculante + $passivoNaoCirculante;
                    return $divisor != 0 ? ($ativoCirculante + $realizavelLP) / $divisor : null;
                },


            // 🆕 Prazo Médio de Estocagem (dias)
            'prazo_medio_estocagem' => function () use ($queryPeriodo, $tipo) {

                    // Obter o estoque médio
                    $estoqueMedio = $this->fromBP($queryPeriodo, ['Estoque']);

                    // Obter o Custo das Mercadorias Vendidas (CMV)
                    $custoMercadoriasVendidas = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);
            if ($custoMercadoriasVendidas === null || $custoMercadoriasVendidas <= 0 || $estoqueMedio === null || $estoqueMedio >= 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }

                    // Verificar se o custo das mercadorias vendidas não é zero para evitar divisão por zero
    
                    $baseDias = match ($tipo) {
                        'anual' => 365,
                        'trimestral' => 90,
                        'mensal' => 30,
                        default => 365,
                    };

                    // Calcular o Prazo Médio de Estocagem
                    $prazo = ($estoqueMedio / $custoMercadoriasVendidas) * $baseDias;
                    return $prazo;

                },

            // 🆕 Giro do Estoque (vezes)
            'giro_estoque' => function () use ($queryPeriodo, $tipo) {
                    // Obter o estoque médio
                    $estoqueMedio = $this->fromBP($queryPeriodo, ['Estoque']);

                    // Obter o Custo das Mercadorias Vendidas (CMV)
                    $custoMercadoriasVendidas = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);

                    // Verifique se os valores são positivos ou negativos, se necessário
                    $estoqueMedio = abs($estoqueMedio);
                    $custoMercadoriasVendidas = abs($custoMercadoriasVendidas);
                    // Calcular o Prazo Médio de Estocagem (dias) sem arredondamento
    
                    $baseDias = match ($tipo) {
                        'anual' => 365,
                        'trimestral' => 90,
                        'mensal' => 30,
                        default => 365, // padrão para segurança
                    };
                    if ($custoMercadoriasVendidas === null || $custoMercadoriasVendidas == 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }
                    $prazo = ($estoqueMedio / $custoMercadoriasVendidas) * $baseDias;

                    return $baseDias / $prazo;
                },

            // 🆕 Prazo Médio de Pagamento a Fornecedores (dias) 
            'prazo_medio_pagamento' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior, $tipo) {

                    $Fornecedores = $this->fromBP($queryPeriodo, ['Fornecedores']);
                    $FornecedoresLP = $this->fromBP($queryPeriodo, ['Fornecedores LP']);

                    $custoMercadoriasVendidas = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);

                    $estoqueAtual = $this->fromBP($queryPeriodo, ['Estoque']);
                    $estoqueAnterior = $this->fromBP($queryPeriodoAnterior, ['Estoque']);

                    if ($estoqueAnterior == 0) {
                        return null;
                    }
                    $somaFornecedores = ($FornecedoresLP + $Fornecedores);
                    $somaTotal = -$custoMercadoriasVendidas + $estoqueAtual - $estoqueAnterior;
                    $baseDias = match ($tipo) {
                        'anual' => 365,
                        'trimestral' => 90,
                        'mensal' => 30,
                        default => 365, // padrão para segurança
                    };
                    return ($somaFornecedores / $somaTotal) * $baseDias;

                },

            // 🆕 Giro das Contas a Pagar (vezes)
            'giro_contas_pagar' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior, $tipo) {

                    $Fornecedores = $this->fromBP($queryPeriodo, ['Fornecedores']);
                    $FornecedoresLP = $this->fromBP($queryPeriodo, ['Fornecedores LP']);

                    $custoMercadoriasVendidas = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);

                    $estoqueAtual = $this->fromBP($queryPeriodoAtual, ['Estoque']);
                    $estoqueAnterior = $this->fromBP($queryPeriodoAnterior, ['Estoque']);
                    if ($estoqueAnterior == 0) {
                        return null;
                    }
                    $somaFornecedores = ($FornecedoresLP + $Fornecedores);
                    $somaTotal = -$custoMercadoriasVendidas + $estoqueAtual - $estoqueAnterior;
                    $baseDias = match ($tipo) {
                        'anual' => 365,
                        'trimestral' => 90,
                        'mensal' => 30,
                        default => 365, // padrão para segurança
                    };
                    return $baseDias / (($somaFornecedores / $somaTotal) * $baseDias);

                },

            // 🆕 Prazo Médio de Cobrança (dias)
            'prazo_medio_cobranca' => function () use ($queryPeriodo, $tipo) {
                    $contasReceber = $this->fromBP($queryPeriodo, ['Contas a Receber']);
                    $vendasCredito = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']); // Ajustar conforme necessário
                    $baseDias = match ($tipo) {
                        'anual' => 365,
                        'trimestral' => 90,
                        'mensal' => 30,
                        default => 365, // padrão para segurança
                    };
                    return $vendasCredito != 0 ? round(($contasReceber / $vendasCredito) * $baseDias, 2) : null;
                },

            // 🆕 Giro dos Valores a Receber (vezes)
            'giro_valores_receber' => function () use ($queryPeriodo) {
                    $contasReceber = $this->fromBP($queryPeriodo, ['Contas a Receber']);
                    $vendasCredito = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']); // Ajustar conforme necessário
                    return $contasReceber != 0 ? round($vendasCredito / $contasReceber, 2) : null;
                },

            // 🆕 Giro do Ativo (vezes)
            'giro_ativo' => function () use ($queryPeriodo) {
                    $vendas = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $ativoTotal = $this->fromBP($queryPeriodo, ['ATIVO CIRCULANTE', 'ATIVO NÃO CIRCULANTE']);
                    return $ativoTotal != 0 ? round($vendas / $ativoTotal, 2) : null;
                },

            // 🆕 Giro do Investimento (vezes)
            'giro_investimento' => function () use ($queryPeriodo) {

                    $vendas = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);

                    $emprestimosFinanciamentosPC = $this->fromBP($queryPeriodo, ['Empréstimos e Financiamentos']);
                    $emprestimosFinanciamentosPNC = $this->fromBP($queryPeriodo, ["Empréstimos e Financiamentos'"]);
                    $totalEmprestimos = $emprestimosFinanciamentosPC + $emprestimosFinanciamentosPNC;

                    $patrimonioLiquido = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    if ($patrimonioLiquido === null || $patrimonioLiquido == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    $soma2 = $totalEmprestimos + $patrimonioLiquido;
                    return ($vendas / $soma2);
                },

            // 🆕 Giro do Patrimônio Líquido (vezes)
            'giro_patrimonio_liquido' => function () use ($queryPeriodo) {
                    $vendas = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $patrimonioLiquido = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    return $patrimonioLiquido != 0 ? round($vendas / $patrimonioLiquido, 2) : null;
                },

            // 🆕 Ciclo Operacional (dias)
            'ciclo_operacional' => function () use ($queryPeriodo, $tipo) {
                    // Obter o estoque médio
                    $estoqueMedio = $this->fromBP($queryPeriodo, ['Estoque']);

                    // Obter o Custo das Mercadorias Vendidas (CMV)
                    $custoMercadoriasVendidas = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);

                    // Verifique se os valores são positivos ou negativos, se necessário
                    $estoqueMedio = abs($estoqueMedio);
                    $custoMercadoriasVendidas = abs($custoMercadoriasVendidas);

                    $baseDias = match ($tipo) {
                        'anual' => 365,
                        'trimestral' => 90,
                        'mensal' => 30,
                        default => 365, // padrão para segurança
                    };
                    if ($custoMercadoriasVendidas === null || $custoMercadoriasVendidas == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    // Calcular o Prazo Médio de Estocagem (dias) sem arredondamento
                    $prazoMedio = ($estoqueMedio / $custoMercadoriasVendidas) * $baseDias;

                    $contasReceber = $this->fromBP($queryPeriodo, ['Contas a Receber']);
                    $vendasCredito = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']); // Ajustar conforme necessário
                    $prazoCobrança = $vendasCredito != 0 ? round(($contasReceber / $vendasCredito) * $baseDias, 2) : null;

                    return $prazoMedio + $prazoCobrança;
                },
            // 🆕 Ciclo Financeiro (dias)
            'ciclo_financeiro' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior, $tipo): float|null {
                    $Fornecedores = $this->fromBP($queryPeriodo, ['Fornecedores']);
                    $FornecedoresLP = $this->fromBP($queryPeriodo, ['Fornecedores LP']);

                    $custoMercadoriasVendidas = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);

                    $estoqueAtual = $this->fromBP($queryPeriodoAtual, ['Estoque']);
                    $estoqueAnterior = $this->fromBP($queryPeriodoAnterior, ['Estoque']);
                    if ($estoqueAnterior == 0) {
                        return null;
                    }
                    $somaFornecedores = ($FornecedoresLP + $Fornecedores);
                    $somaTotal = -$custoMercadoriasVendidas + $estoqueAtual - $estoqueAnterior;

                    $baseDias = match ($tipo) {
                        'anual' => 365,
                        'trimestral' => 90,
                        'mensal' => 30,
                        default => 365, // padrão para segurança
                    };
                    $prazoMedio = ($somaFornecedores / $somaTotal) * $baseDias;

                    // Obter o estoque médio
                    $estoqueMedio = $this->fromBP($queryPeriodo, ['Estoque']);

                    // Obter o Custo das Mercadorias Vendidas (CMV)
                    $custoMercadoriasVendidas = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);

                    // Verifique se os valores são positivos ou negativos, se necessário
                    $estoqueMedio = abs($estoqueMedio);
                    $custoMercadoriasVendidas = abs($custoMercadoriasVendidas);

                    // Calcular o Prazo Médio de Estocagem (dias) sem arredondamento
    
                    $prazoMedioA = ($estoqueMedio / $custoMercadoriasVendidas) * $baseDias;

                    $contasReceber = $this->fromBP($queryPeriodo, ['Contas a Receber']);
                    $vendasCredito = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']); // Ajustar conforme necessário
                    $prazoCobrança = $vendasCredito != 0 ? round(($contasReceber / $vendasCredito) * $baseDias, 2) : null;

                    $prazoTotal = $prazoMedioA + $prazoCobrança;

                    return $prazoTotal - $prazoMedio;
                },
            'grau_alavancagem_operacional' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $ebitAtual = $this->fromDRE($queryPeriodoAtual, ['(=) EBIT']);

                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) EBIT']);

                    if ($ebitAnterior === null || $ebitAnterior == 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }
                    $somabit = ($ebitAtual - $ebitAnterior) / $ebitAnterior;

                    $rolAtual = $this->fromDRE($queryPeriodoAtual, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $rolAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $somarol = ($rolAtual - $rolAnterior) / $rolAnterior;

                    $somatotal = ($somabit / $somarol);

                    if ($somatotal == 0) {
                        return null;
                    }

                    return $somatotal != 0 ? ($somatotal) : null;
                },

            'grau_alavancagem_financeira' => function () use ($queryPeriodo, $queryPeriodoAnterior) {

                    // Recupera Lucro Líquido
                    $lucroLiquidoAtual = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);
                    $lucroLiquidoAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);

                    // Recupera EBIT
                    $ebitAtual = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) EBIT']);
                    if ($ebitAnterior === null || $ebitAnterior == 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }
                    // Cálculo do Grau de Alavancagem Financeira (GAF)
                    if ($lucroLiquidoAnterior != 0 && $ebitAnterior != 0) {
                        $variacaoLucro = ($lucroLiquidoAtual - $lucroLiquidoAnterior) / $lucroLiquidoAnterior;
                        $variacaoEBIT = ($ebitAtual - $ebitAnterior) / $ebitAnterior;

                        return $variacaoEBIT != 0 ? ($variacaoLucro / $variacaoEBIT) : null;
                    }
                },
            'grau_alavancagem_total' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {
                    // Recupera Lucro Líquido
                    $lucroLiquidoAtual = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);
                    $lucroLiquidoAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);

                    // Recupera EBIT
                    $ebitAtual = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) EBIT']);
                    if ($ebitAnterior === null || $ebitAnterior == 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }
                    // Cálculo do Grau de Alavancagem Financeira (GAF)
                    if ($lucroLiquidoAnterior != 0 && $ebitAnterior != 0) {
                        $variacaoLucro = ($lucroLiquidoAtual - $lucroLiquidoAnterior) / $lucroLiquidoAnterior;
                        $variacaoEBIT = ($ebitAtual - $ebitAnterior) / $ebitAnterior;

                        $totalFinanceiro = $variacaoEBIT != 0 ? ($variacaoLucro / $variacaoEBIT) : null;
                    }

                    $ebitAtual = $this->fromDRE($queryPeriodoAtual, ['(=) EBIT']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) EBIT']);

                    $somabit = ($ebitAtual - $ebitAnterior) / $ebitAnterior;

                    $rolAtual = $this->fromDRE($queryPeriodoAtual, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $rolAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $somarol = ($rolAtual - $rolAnterior) / $rolAnterior;
                    $somatotal = ($somabit / $somarol);


                    return $totalFinanceiro * $somatotal; // Substitui vírgulas por ponto 
    
                },
            'margem_líquida_ll_rol' => function () use ($queryPeriodo) {
                    // Recupera o valor de resultado operacional (EBIT) no período atual
                    $lucro_liquido = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);

                    // Recupera o valor de Passivo Total no período atual
                    $receitaOperacionalLiquida = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    if ($receitaOperacionalLiquida === null || $lucro_liquido == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    // Calcula o Grau de Alavancagem Total
                    return $lucro_liquido / $receitaOperacionalLiquida * 100;
                },
            'giro_do_ativo_total' => function () use ($queryPeriodo) {
                    // Recupera o valor de resultado operacional (EBIT) no período atual
                    $ativoTotal = $this->fromBP($queryPeriodo, ['ATIVO']);

                    // Recupera o valor de Passivo Total no período atual
                    $receitaOperacionalLiquida = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    if ($receitaOperacionalLiquida === null || $receitaOperacionalLiquida == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    // Calcula o Grau de Alavancagem Total
                    return $receitaOperacionalLiquida / $ativoTotal;
                },

            'multiplicadorPl' => function () use ($queryPeriodo) {
                    // Recupera o valor de resultado operacional (EBIT) no período atual
                    $ativoTotal = $this->fromBP($queryPeriodo, ['ATIVO']);

                    // Recupera o valor de Passivo Total no período atual
                    $patriominioLiquido = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    if ($patriominioLiquido === null || $patriominioLiquido == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    // Calcula o Grau de Alavancagem Total
                    return $ativoTotal / $patriominioLiquido;
                },

            'rentabilidade_roe' => function () use ($queryPeriodo) {
                    // Recupera o valor de resultado operacional (EBIT) no período atual
                    $lucroLiquido = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);

                    // Recupera o valor de Passivo Total no período atual
                    $patriominioLiquido = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    if ($patriominioLiquido === null || $patriominioLiquido == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    // Calcula o Grau de Alavancagem Total
                    return $lucroLiquido / $patriominioLiquido * 100;
                },

            'gaf' => function () use ($queryPeriodo) {

                    $pl = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    $lucroLiquido = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);
                    if ($lucroLiquido === null || $lucroLiquido == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    $roe = round(($lucroLiquido / $pl) * 100, 2);

                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);

                    $nopat = $ebit + $ircsll;

                    $financiamentosCP = $this->fromBP($queryPeriodo, ['Empréstimos e Financiamentos']);
                    $financiamentosLP = $this->fromBP($queryPeriodo, ["Empréstimos e Financiamentos'"]);

                    $pl = $this->fromBP($queryPeriodo, ["PATRIMÔNIO LÍQUIDO"]);

                    $somafinancimentos = $pl + $financiamentosCP + $financiamentosLP;

                    $roic = round(($nopat / $somafinancimentos) * 100, 2);

                    return $roe / $roic;
                },

            'roe_retorno_sobre_o_pl' => function () use ($queryPeriodo) {

                    $pl = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    $lucroLiquido = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);
                    if ($lucroLiquido === null || $lucroLiquido == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    return round(($lucroLiquido / $pl) * 100, 2);
                },


            'roic' => function () use ($queryPeriodo) {

                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);

                    $nopat = $ebit + $ircsll;
                    $financiamentosCP = $this->fromBP($queryPeriodo, ['Empréstimos e Financiamentos']);
                    $financiamentosLP = $this->fromBP($queryPeriodo, ["Empréstimos e Financiamentos'"]);

                    $pl = $this->fromBP($queryPeriodo, ["PATRIMÔNIO LÍQUIDO"]);

                    $somafinancimentos = $pl + $financiamentosCP + $financiamentosLP;
                    if ($somafinancimentos === null || $somafinancimentos == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    return round(($nopat / $somafinancimentos) * 100, 2);
                },
            'ganho_pela_alavancagem_financeira' => function () use ($queryPeriodo) {

                    $pl = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    $lucroLiquido = $this->fromDRE($queryPeriodo, ['(=) RESULTADO LÍQUIDO DO EXERCÍCIO']);
                    if ($lucroLiquido === null || $lucroLiquido == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    $roe = round(($lucroLiquido / $pl) * 100, 2);

                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);

                    $nopat = $ebit + $ircsll;

                    $financiamentosCP = $this->fromBP($queryPeriodo, ['Empréstimos e Financiamentos']);
                    $financiamentosLP = $this->fromBP($queryPeriodo, ["Empréstimos e Financiamentos'"]);

                    $pl = $this->fromBP($queryPeriodo, ["PATRIMÔNIO LÍQUIDO"]);

                    $somafinancimentos = $pl + $financiamentosCP + $financiamentosLP;

                    $roic = round(($nopat / $somafinancimentos) * 100, 2);

                    return round($roe - $roic, 2);
                },
            'EBITDA' => function () use ($queryPeriodo) {

                    $ebitda = $this->fromDRE($queryPeriodo, ['(=) EBITDA']);

                    return $ebitda;
                },
            'EBIT' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(=) EBIT']);

                    if ($ebitAnterior == 0) {
                        return null;
                    }
                    return $ebit;
                },
            'ir_e_csll' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {
                    $taxaIR = 0.34;
                    $ebit = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(-) IR e CSLL']);

                    if ($ebitAnterior == 0) {
                        return null;
                    }
                    return $ebit;
                },
            'no_pat' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {
                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ir_e_csll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(-) IR e CSLL']);

                    if ($ebitAnterior == 0) {
                        return null;
                    }
                    return $ebit + $ir_e_csll;
                },
            'depreciacao_e_amortização' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $depreciacao = $this->fromDRE($queryPeriodo, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);
                    $ir_e_csll = $this->fromDRE($queryPeriodo, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(-) IR e CSLL']);

                    if ($ebitAnterior == 0) {
                        return null;
                    }
                    return -$depreciacao;
                },

            'fluxo_de_caixa_operacional' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ir_e_Csll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);
                    $depreciacao = $this->fromDRE($queryPeriodo, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);
                    $ebitAnterior = $this->fromDRE($queryPeriodoAnterior, ['(-) IR e CSLL']);
                    if ($ebitAnterior == 0) {
                        return null;
                    }
                    // Fluxo de Caixa Operacional
                    return ($ebit + $ir_e_Csll) + -$depreciacao;
                },
            'investimentos_fixos' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {
                    $imobilizadoAtual = $this->fromBP($queryPeriodoAtual, ['Imobilizado']);
                    $imobilizadoAnterior = $this->fromBP($queryPeriodoAnterior, ['Imobilizado']);
                    $intangivelAtual = $this->fromBP($queryPeriodoAtual, ['Intangível']);
                    $intangivelAnterior = $this->fromBP($queryPeriodoAnterior, ['Intangível']);
                    $direitodeusoAtual = $this->fromBP($queryPeriodoAtual, ['Direito de Uso']);
                    $direitodeuso = $this->fromBP($queryPeriodoAnterior, ['Direito de Uso']);
                    $depreciacao = $this->fromDRE($queryPeriodoAtual, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);
                    if ($intangivelAnterior == 0) {
                        return null;
                    }
                    $capex = ($imobilizadoAtual - $imobilizadoAnterior) + ($intangivelAtual - $intangivelAnterior) + ($direitodeusoAtual - $direitodeuso) + -$depreciacao;
                    return -$capex;
                },
            'investimento_em_capital_de_giro' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $ativoCirculante = $this->fromBP($queryPeriodoAtual, ['ATIVO CIRCULANTE']);
                    $ativoCirculanteCaixa = $this->fromBP($queryPeriodoAtual, ['Caixa e Equivalentes de Caixa', 'Aplicações Financeiras']);
                    $somaAtivo = $ativoCirculante - $ativoCirculanteCaixa;

                    $passivoCirculante = $this->fromBP($queryPeriodoAtual, ['PASSIVO CIRCULANTE']);
                    $passivoCirculanteOutros = $this->fromBP($queryPeriodoAtual, ['Empréstimos e Financiamentos', 'Provisões']);

                    $somaPassivo = $passivoCirculante - $passivoCirculanteOutros;
                    $somatual = $somaAtivo - $somaPassivo;

                    $ativoCirculanteAnterior = $this->fromBP($queryPeriodoAnterior, ['ATIVO CIRCULANTE']);
                    $ativoCirculanteCaixaAnterior = $this->fromBP($queryPeriodoAnterior, ['Caixa e Equivalentes de Caixa', 'Aplicações Financeiras']);
                    $somaAtivoAnterior = $ativoCirculanteAnterior - $ativoCirculanteCaixaAnterior;
                    $passivoCirculanteAnterior = $this->fromBP($queryPeriodoAnterior, ['PASSIVO CIRCULANTE']);
                    $passivoCirculanteOutrosAnterior = $this->fromBP($queryPeriodoAnterior, ['Empréstimos e Financiamentos', 'Provisões']);

                    $somaPassivoAnterior = $passivoCirculanteAnterior - $passivoCirculanteOutrosAnterior;

                    $somAnterior = $somaAtivoAnterior - $somaPassivoAnterior;
                    if ($somaPassivoAnterior == 0) {
                        return null;
                    }
                    return -($somatual - $somAnterior);
                },
            'fluxo_de_caixa_livre_da_firma' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ir_e_Csll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);
                    $depreciacao = $this->fromDRE($queryPeriodo, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);

                    // Fluxo de Caixa Operacional
                    $totalFluxo = ($ebit + $ir_e_Csll) + -$depreciacao;
                    $imobilizadoAtual = $this->fromBP($queryPeriodoAtual, ['Imobilizado']);
                    $imobilizadoAnterior = $this->fromBP($queryPeriodoAnterior, ['Imobilizado']);
                    $intangivelAtual = $this->fromBP($queryPeriodoAtual, ['Intangível']);
                    $intangivelAnterior = $this->fromBP($queryPeriodoAnterior, ['Intangível']);
                    $direitodeusoAtual = $this->fromBP($queryPeriodoAtual, ['Direito de Uso']);
                    $direitodeuso = $this->fromBP($queryPeriodoAnterior, ['Direito de Uso']);
                    $depreciacao = $this->fromDRE($queryPeriodoAtual, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);

                    $capex = ($imobilizadoAtual - $imobilizadoAnterior) + ($intangivelAtual - $intangivelAnterior) + ($direitodeusoAtual - $direitodeuso) + -$depreciacao;

                    $totalCapex = '-' . $capex;

                    $ativoCirculante = $this->fromBP($queryPeriodoAtual, ['ATIVO CIRCULANTE']);
                    $ativoCirculanteCaixa = $this->fromBP($queryPeriodoAtual, ['Caixa e Equivalentes de Caixa', 'Aplicações Financeiras']);
                    $somaAtivo = $ativoCirculante - $ativoCirculanteCaixa;

                    $passivoCirculante = $this->fromBP($queryPeriodoAtual, ['PASSIVO CIRCULANTE']);
                    $passivoCirculanteOutros = $this->fromBP($queryPeriodoAtual, ['Empréstimos e Financiamentos', 'Provisões']);

                    $somaPassivo = $passivoCirculante - $passivoCirculanteOutros;
                    $somatual = $somaAtivo - $somaPassivo;


                    $ativoCirculanteAnterior = $this->fromBP($queryPeriodoAnterior, ['ATIVO CIRCULANTE']);
                    $ativoCirculanteCaixaAnterior = $this->fromBP($queryPeriodoAnterior, ['Caixa e Equivalentes de Caixa', 'Aplicações Financeiras']);
                    $somaAtivoAnterior = $ativoCirculanteAnterior - $ativoCirculanteCaixaAnterior;
                    $passivoCirculanteAnterior = $this->fromBP($queryPeriodoAnterior, ['PASSIVO CIRCULANTE']);
                    $passivoCirculanteOutrosAnterior = $this->fromBP($queryPeriodoAnterior, ['Empréstimos e Financiamentos', 'Provisões']);

                    $somaPassivoAnterior = $passivoCirculanteAnterior - $passivoCirculanteOutrosAnterior;

                    $somAnterior = $somaAtivoAnterior - $somaPassivoAnterior;

                    $totalCapital = ($somatual - $somAnterior);
                    if ($imobilizadoAnterior == 0) {
                        return null;
                    }
                    return (float) $totalFluxo + (float) $totalCapex + -(float) $totalCapital;
                }
            ,
            'cap_de_terceiros_capital_proprio' => function () use ($queryPeriodo) {

                    // Capital de Terceiros = Passivo Circulante + Passivo Não Circulante
                    $capitalDeTerceiros = $this->fromBP($queryPeriodo, [
                    'PASSIVO CIRCULANTE',
                    'PASSIVO NÃO CIRCULANTE'
                    ]);

                    // Capital Próprio = Patrimônio Líquido
                    $capitalProprio = $this->fromBP($queryPeriodo, [
                    'PATRIMÔNIO LÍQUIDO'
                    ]);
                    if ($capitalDeTerceiros === null || $capitalProprio == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    if ($capitalProprio != 0) {
                        $indiceEndividamento = $capitalDeTerceiros / $capitalProprio;
                        Log::info("Índice de Endividamento: $indiceEndividamento");
                        return $indiceEndividamento;
                    } else {
                        Log::warning("Patrimônio Líquido é zero. Não é possível calcular o índice de endividamento.");
                        return null;
                    }
                },
            'cap_de_terceiros_passivo_total' => function () use ($queryPeriodo) {

                    // Recupera os valores de Passivo Circulante, Passivo Não Circulante e Passivo Total
                    $passivoCirculante = $this->fromBP($queryPeriodo, ['PASSIVO CIRCULANTE']);
                    $passivoNaoCirculante = $this->fromBP($queryPeriodo, ['PASSIVO NÃO CIRCULANTE']);
                    $passivoTotal = $this->fromBP($queryPeriodo, ['PASSIVO E PATRIMÔNIO LÍQUIDO']);

                    // Somando Passivo Circulante e Passivo Não Circulante
                    $somaPassivos = $passivoCirculante + $passivoNaoCirculante;
                    if ($somaPassivos === null || $somaPassivos == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    $metadePassivoTotal = $somaPassivos / $passivoTotal;

                    return $metadePassivoTotal;
                },
            'grau_de_imobilização_de_recursos_permanentes' => function () use ($queryPeriodo) {
                    $imobilizado = $this->fromBP($queryPeriodo, ['ATIVO NÃO CIRCULANTE']); // Categoria "Imobilizado"
                    $passivoNaoCirculante = $this->fromBP($queryPeriodo, ['PASSIVO NÃO CIRCULANTE']); // Passivo Não Circulante
                    $patrimonioLiquido = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']); // Patrimônio Líquido
    
                    // Evitar divisão por zero
                    if (($passivoNaoCirculante + $patrimonioLiquido) == 0) {
                        return null; // ou 0, ou lançar exceção, conforme o caso
                    }

                    // Cálculo do Grau de Imobilização de Recursos Permanentes
                    $grauImobilizacao = $imobilizado / ($passivoNaoCirculante + $patrimonioLiquido);

                    return $grauImobilizacao;
                },
            'alococao_de_recursos_permanentes_no_cp' => function () use ($queryPeriodo) {
                    $ativoNaoCirculante = $this->fromBP($queryPeriodo, ['ATIVO NÃO CIRCULANTE']);
                    $PassivonaoCirculante = $this->fromBP($queryPeriodo, ['PASSIVO NÃO CIRCULANTE']);
                    $pat = $this->fromBP($queryPeriodo, ['PATRIMÔNIO LÍQUIDO']);
                    if ($pat === null || $pat == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    $soma = $ativoNaoCirculante / ($PassivonaoCirculante + $pat);

                    return 1 - $soma;
                },
            'indicador_de_cobertura_de_juros_conceito_ebit' => function () use ($queryPeriodo) {
                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $despesasFinanceirasLiquidas = $this->fromDRE($queryPeriodo, ['(-) DESPESAS FINANCEIRAS LÍQUIDAS']);

                    if ($ebit === null || $ebit <= 0 || $despesasFinanceirasLiquidas === null || $despesasFinanceirasLiquidas >= 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }

                    $coberturaJurosEBIT = $ebit / -$despesasFinanceirasLiquidas;

                    if (is_nan($coberturaJurosEBIT) || is_infinite($coberturaJurosEBIT) || $coberturaJurosEBIT < 0) {
                        return null;
                    }
                    Log::debug("Cobertura de Juros (EBIT): $coberturaJurosEBIT");

                    return (float) $coberturaJurosEBIT;
                },
            'indicador_de_cobertura_de_juros_conceito_EBITDA' => function () use ($queryPeriodo) {
                    $ebitda = $this->fromDRE($queryPeriodo, ['(=) EBITDA']);
                    $despesasFinanceirasLiquidas = $this->fromDRE($queryPeriodo, ['(-) DESPESAS FINANCEIRAS LÍQUIDAS']);

                    if ($ebitda === null || $ebitda <= 0 || $despesasFinanceirasLiquidas === null || $despesasFinanceirasLiquidas >= 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }

                    $coberturaJurosEBITDA = $ebitda / -$despesasFinanceirasLiquidas;

                    if (is_nan($coberturaJurosEBITDA) || is_infinite($coberturaJurosEBITDA) || $coberturaJurosEBITDA < 0) {
                        return null;
                    }
                    Log::debug("Cobertura de Juros (EBITDA): $coberturaJurosEBITDA");

                    return $coberturaJurosEBITDA;
                },
            'taxa_de_juros_sobre_empréstimos_e_financiamentos' => function () use ($queryPeriodo, $queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $despesasFinanceiras = $this->fromDRE($queryPeriodo, ['(-) Despesas Financeiras']);

                    $emprestimosFinanciamentosPC = $this->fromBP($queryPeriodoAtual, ['Empréstimos e Financiamentos']);
                    $emprestimosFinanciamentosPNC = $this->fromBP($queryPeriodoAtual, ["Empréstimos e Financiamentos'"]);

                    $totalEmprestimos = $emprestimosFinanciamentosPC + $emprestimosFinanciamentosPNC;

                    if ($despesasFinanceiras === null || $totalEmprestimos <= 0 || $despesasFinanceiras === null || $despesasFinanceiras >= 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }

                    if ($totalEmprestimos == 0) {
                        return null;
                    }

                    $taxa = -$despesasFinanceiras / $totalEmprestimos;


                    if (is_nan($taxa) || is_infinite($taxa) || $taxa < 0) {
                        return null;
                    }

                    return $taxa > 0 ? round($taxa * 100, 2) : null; // Só retorna se for positiva
    
                },
            'depreciação_como_porcentagem_da_ROL' => function () use ($queryPeriodo): float|null {
                    $depreciacao = $this->fromDRE($queryPeriodo, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);

                    if ($rol == 0) {
                        return null;
                    }

                    $depreciacaoPorRol = (-$depreciacao / $rol) * 100;

                    return $depreciacaoPorRol;
                },
            'CAPEX_Depreciação' => function () use ($queryPeriodoAtual, $queryPeriodoAnterior): float|null {
                    $imobilizadoAtual = $this->fromBP($queryPeriodoAtual, ['Imobilizado']);
                    $imobilizadoAnterior = $this->fromBP($queryPeriodoAnterior, ['Imobilizado']);
                    $intangivelAtual = $this->fromBP($queryPeriodoAtual, ['Intangível']);
                    $intangivelAnterior = $this->fromBP($queryPeriodoAnterior, ['Intangível']);
                    $direitodeusoAtual = $this->fromBP($queryPeriodoAtual, ['Direito de Uso']);
                    $direitodeuso = $this->fromBP($queryPeriodoAnterior, ['Direito de Uso']);
                    $depreciacao = $this->fromDRE($queryPeriodoAtual, ['(-) DEPRECIAÇÃO E AMORTIZAÇÃO']);

                    $capex = ($imobilizadoAtual - $imobilizadoAnterior) + ($intangivelAtual - $intangivelAnterior) + ($direitodeusoAtual - $direitodeuso) + -$depreciacao;

                    if ($imobilizadoAnterior === null || $imobilizadoAnterior <= 0 || $intangivelAnterior === null || $intangivelAnterior <= 0) {
                        Log::warning("Despesas Financeiras Líquidas igual a zero — divisão evitada.");
                        return null;
                    }

                    if ($depreciacao == 0) {
                        Log::warning("Depreciação igual a zero — divisão evitada.");
                        return null;
                    }

                    $capexPorDepreciacao = $capex / -$depreciacao;

                    return $capexPorDepreciacao;
                },
            'retorno_sobre_o_ativo_ROA_conceito_NOPAT' => function () use ($queryPeriodoAtual, $queryPeriodoAnterior): float|null {

                    $ebit = $this->fromDRE($queryPeriodoAtual, ['(=) EBIT']);
                    $ir = $this->fromDRE($queryPeriodoAtual, ['(-) IR e CSLL']);
                    $ativo = $this->fromBP($queryPeriodoAtual, ['ATIVO']);

                    if ($ir === null || $ebit == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    return round(($ebit + $ir) * 100, 2) / $ativo;
                },
            // 2. Retorno sobre o Capital Investido (ROIC) = NOPAT / (Ativo - Passivo Circulante)
            'retorno_sobre_o_capital_investido_ROIC_conceito_NOPAT' => function () use ($queryPeriodoAtual, $queryPeriodoAnterior): float|null {
                    $ebit = $this->fromDRE($queryPeriodoAtual, ['(=) EBIT']);
                    $ir = $this->fromDRE($queryPeriodoAtual, ['(-) IR e CSLL']);

                    $emprestimosFinanciamentosPC = $this->fromBP($queryPeriodoAtual, ['Empréstimos e Financiamentos']);
                    $emprestimosFinanciamentosPNC = $this->fromBP($queryPeriodoAtual, ["Empréstimos e Financiamentos'"]);

                    $totalEmprestimos = $emprestimosFinanciamentosPC + $emprestimosFinanciamentosPNC;

                    $pat = $this->fromBP($queryPeriodoAtual, ["PATRIMÔNIO LÍQUIDO"]) + $totalEmprestimos;
                    if ($ir === null || $pat == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    return round(($ebit + $ir) * 100, 2) / $pat;
                },
            'ROI_Publicidade' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);

                    $custo = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);
                    $comissoes = $this->fromDRE($queryPeriodo, ['Comissões sobre Vendas']);
                    $propaganda = $this->fromDRE($queryPeriodo, ['Propaganda e Publicidade']);
                    if ($propaganda == 0) {
                        return null;
                    } else {
                        $somaaq = ($rol - -$custo - $comissoes - $propaganda);
                        return ($somaaq) / $propaganda;
                    }
                },
            'margem_bruta' => function () use ($queryPeriodo) {
                    $rob = $this->fromDRE($queryPeriodo, ['(=) RESULTADO OPERACIONAL BRUTO']);
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    if ($rol === null || $rob == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    return round(($rob / $rol) * 100, 2);
                },
            'margem_de_contribuição' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $custo = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);
                    $outras_despesas = $this->fromDRE($queryPeriodo, ['Outras Despesas com Vendas']);
                    return $rol + $custo + $outras_despesas + $ircsll;
                },
            'margem_contribuicao_percent_rob' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $custo = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);
                    $outras_despesas = $this->fromDRE($queryPeriodo, ['Outras Despesas com Vendas']);
                    $margemContribuicao = $rol + $custo + $outras_despesas + $ircsll;

                    $rob = $this->fromDRE($queryPeriodo, ['RECEITA OPERACIONAL BRUTA']);
                    if ($rob == 0)
                        return null;
                    return round(($margemContribuicao / $rob) * 100, 2);
                },
            'margem_contribuicao_percent_rol' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $custo = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);
                    $outras_despesas = $this->fromDRE($queryPeriodo, ['Outras Despesas com Vendas']);

                    $margemContribuicao = $rol + $custo + $outras_despesas + $ircsll;

                    if ($rol == 0)
                        return null;
                    return round(($margemContribuicao / $rol) * 100, 2);
                },
            'margem_operacional_nopat' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);

                    $nopat = $ebit + $ircsll;
                    if ($rol === null || $nopat == 0) {
                        Log::warning("igual a zero — divisão evitada.");
                        return null;
                    }
                    $margem = round(($nopat / $rol) * 100, 2);

                    // Log do resultado final
                    Log::debug("Margem Operacional (NOPAT): $margem");

                    return $margem;
                },
            'EBIT_Lucratividade' => function () use ($queryPeriodo) {
                    return $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                },
            'nopat' => function () use ($queryPeriodo) {
                    // Recuperando os valores de RESULTADO ANTES DO RESULTADO FINANCEIRO e IR e CSLL
                    $resultado_antes_do_resultado_financeiro = $this->fromDRE($queryPeriodo, ['(=) RESULTADO ANTES DO RESULTADO FINANCEIRO']);

                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);

                    // Calculando o NOPAT
                    $somaebit = $ebit + $ircsll;
                    $nopat = $resultado_antes_do_resultado_financeiro + $ircsll;

                    return $somaebit;
                },
            'margem_ebitda' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $ebitda = $this->fromDRE($queryPeriodo, ['(=) EBITDA']);
                    if ($rol == 0)
                        return null;
                    return round(($ebitda / $rol) * 100, 2);
                },
            'margem_ebit' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
                    if ($rol == 0)
                        return null;
                    return round(($ebit / $rol) * 100, 2);
                },
            'margem_nopat' => function () use ($queryPeriodo) {
                    $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
                    // Recuperando os valores de RESULTADO ANTES DO RESULTADO FINANCEIRO e IR e CSLL
                    $resultado_antes_do_resultado_financeiro = $this->fromDRE($queryPeriodo, ['(=) RESULTADO ANTES DO RESULTADO FINANCEIRO']);
                    $ircsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);

                    // Calculando o NOPAT
                    $nopat = $resultado_antes_do_resultado_financeiro + $ircsll;
                    if ($rol == 0)
                        return null;
                    return round(($nopat / $rol) * 100, 2);
                },
            default => 'Nenhum valor a ser mostrado!',
        };
    }

    private function calculateNOPAT(Closure $queryPeriodo): float
    {
        $ebit = $this->fromDRE($queryPeriodo, ['(=) EBIT']);
        $irCsll = $this->fromDRE($queryPeriodo, ['(-) IR e CSLL']);

        return $ebit - $irCsll;
    }

    private function calculateMargemContribuicao(Closure $queryPeriodo): float
    {
        $rol = $this->fromDRE($queryPeriodo, ['(=) RECEITA OPERACIONAL LÍQUIDA']);
        $custo = $this->fromDRE($queryPeriodo, ['(-) CUSTO PRODUTOS/MERCADORIAS/SERVIÇOS']);
        $comissoes = $this->fromDRE($queryPeriodo, ['Comissões sobre Vendas']);
        $propaganda = $this->fromDRE($queryPeriodo, ['Propaganda e Publicidade']);

        return $rol - $custo - $comissoes - $propaganda;
    }

    private function safeDivide(float|int|null $a, float|int|null $b): float|null
    {
        return ($b === 0.0 || $b === null) ? null : $a / $b;
    }

    protected function getNOPAT($ebit, $taxa = 0.34)
    {
        return $ebit * (1 - $taxa);
    }

    protected function mediaBP(Closure $queryPeriodo, array $categorias)
    {
        $tipo = $this->tipo;

        $itensFinais = BalancoPatrimonial::where($queryPeriodo)
            ->whereIn('categoria', $categorias)
            ->get();

        $queryAnterior = function ($query) use ($queryPeriodo) {
            // Obtem ano anterior do where já existente
            $ano = now()->year;
            foreach ($queryPeriodo as $q) {
                if (isset($q['column']) && $q['column'] === 'year') {
                    $ano = $q['value'];
                }
            }
            $query->where('year', $ano - 1)
                ->where('type', $this->tipo);
        };

        $itensIniciais = BalancoPatrimonial::where($queryAnterior)
            ->whereIn('categoria', $categorias)
            ->get();

        $valorInicial = $itensIniciais->sum(fn($item) => (float) str_replace(['.', ','], ['', '.'], $item->valor));
        $valorFinal = $itensFinais->sum(fn($item) => (float) str_replace(['.', ','], ['', '.'], $item->valor));

        return ($valorInicial + $valorFinal) / 2;
    }

    public function listarPeriodosDentroDe(array $queryPeriodo)
    {
        $inicio = Carbon::createFromFormat('Y-m', $queryPeriodo['inicio']);
        $fim = Carbon::createFromFormat('Y-m', $queryPeriodo['fim']);

        $periodos = [];

        while ($inicio->lte($fim)) {
            $periodos[] = $inicio->format('Y-m');
            $inicio->addMonth();
        }

        return $periodos;
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

    protected function calcularCCL($queryPeriodo)
    {
        $ativos = [
            'ATIVO CIRCULANTE',
            'Caixa e Equivalentes de Caixa',
            'Aplicações Financeiras',
            'Contas a Receber',
            'Estoque',

            'Tributos a Recuperar',
            'Outros Créditos',
            'Despesas Antecipadas',
        ];

        $passivos = [
            'PASSIVO CIRCULANTE',
            'Fornecedores',
            'Empréstimos e Financiamentos',
            'Obrigações Tributárias',
            'Obrigações Trabalhistas e Sociais',
            'Provisões',
            'LucroJCP a Distribuir',
            'Adiantamento de Clientes',
            'Outras Obrigações',
        ];

        $totalAtivos = BalancoPatrimonial::where($queryPeriodo)
            ->whereIn('categoria', $ativos)
            ->get()
            ->sum(function ($item) {
                $valor = str_replace(['.', ','], ['', '.'], $item->valor);
                // Remove os pontos de milhar e transforma a vírgula em ponto decimal
                return is_numeric($valor) ? (float) $valor : 0;
            });

        $totalPassivos = BalancoPatrimonial::where($queryPeriodo)
            ->whereIn('categoria', $passivos)
            ->get()
            ->sum(function ($item) {
                $valor = str_replace(['.', ','], ['', '.'], $item->valor);
                // Remove os pontos de milhar e transforma a vírgula em ponto decimal
                return is_numeric($valor) ? (float) $valor : 0;
            });

        return $totalAtivos - $totalPassivos;
    }
}