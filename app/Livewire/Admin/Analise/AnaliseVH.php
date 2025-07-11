<?php

namespace App\Livewire\Admin\Analise;

use App\Models\BalancoPatrimonial;
use App\Models\CategoryBP;
use App\Models\CategoryDre;
use App\Models\Dres;
use Livewire\Component;

class AnaliseVH extends Component
{
    public $anos = '2025';
    public $type = 'anual';
    public $order = 'bp';
    public $dados = [];
    public $mesSelecionado = 1;
    public $trimestreSelecionado = 1;
    public $mes = 1;
    public $trimestre = '1T';
    public function render()
    {
        $this->getDados();
        return view('livewire.admin.analise.analiseVH')->layout('layouts.admin');
    }
    public function getMesesDoTrimestre()
    {
        return match ((int) $this->trimestreSelecionado) {
            1 => [1, 2, 3],
            2 => [4, 5, 6],
            3 => [7, 8, 9],
            4 => [10, 11, 12],
            default => []
        };
    }
    public function getDados()
    {
        $this->reset('dados');

        // Filtro compartilhado para queries por tipo
        $filtroTipo = function ($query) {
            $query->where('year', $this->anos)
                ->where('type', $this->type);

            if ($this->type === 'mensal') {
                $query->where('month', $this->mes);
            }

            if ($this->type === 'trimestral') {
                $query->where('periodo', $this->trimestre . '/' . $this->anos);
            }
        };

        if ($this->order === 'bp') {
            // ATIVO TOTAL para AV (%)
            $ativoTotal = BalancoPatrimonial::where('categoria', 'ATIVO')
                ->where('type', $this->type)
                ->where('year', $this->anos)
                ->when($this->type === 'mensal', fn($q) => $q->where('month', $this->mes))
                ->when($this->type === 'trimestral', fn($q) => $q->where('periodo', $this->trimestre . '/' . $this->anos))
                ->sum('valor');

            CategoryBP::all()
                ->each(function ($categoria) use ($ativoTotal) {
                    $query = $categoria->balancopatrimonial()
                        ->where('year', $this->anos)
                        ->where('type', $this->type);

                    if ($this->type === 'mensal') {
                        $query->where('month', $this->mes);
                    }

                    if ($this->type === 'trimestral') {
                        $query->where('periodo', $this->trimestre . '/' . $this->anos);
                    }

                    $valorAtual = $query->get()->sum(fn($item) => $this->limparValor($item->valor));

                    $valorAnterior = $this->getValorAnoAnteriorBP($categoria->name, $this->anos);

                    $this->dados[] = [
                        'name' => $categoria->name,
                        'nivel' => $categoria->nivel,
                        'type' => $categoria->type ?? 'anual',
                        'bp' => ['value' => $valorAtual],
                        'av' => $this->calcularAV($valorAtual, $ativoTotal),
                        'ah' => $this->calcularAH($valorAtual, $valorAnterior),
                        'anterior' => $valorAnterior
                    ];
                });

        } else {
            // RECEITA OPERACIONAL LÍQUIDA para AV (%)
            $receitaLiquida = Dres::where('categoria', 'LIKE', '%RECEITA OPERACIONAL LÍQUIDA%')
                ->where('type', $this->type)
                ->where('year', $this->anos)
                ->when($this->type === 'mensal', fn($q) => $q->where('month', $this->mes))
                ->when($this->type === 'trimestral', fn($q) => $q->where('periodo', $this->trimestre . '/' . $this->anos))
                ->sum('valor');

        CategoryDre::all()
    ->each(function ($categoria) use ($receitaLiquida) {
        $query = $categoria->dre()
            ->where('year', $this->anos)
            ->where('type', $this->type);

        if ($this->type === 'mensal') {
            $query->where('month', $this->mes);
        }

        if ($this->type === 'trimestral') {
            $query->where('periodo', $this->trimestre . '/' . $this->anos);
        }

        $valorAtual = $query->get()->sum(fn($item) => $this->limparValor($item->valor));

                    $valorAnterior = $this->getValorAnoAnteriorDRE($categoria->name, $this->anos);

                    $this->dados[] = [
                        'name' => $categoria->name,
                        'nivel' => $categoria->nivel,
                        'type' => $categoria->type ?? 'anual',
                        'dre' => ['value' => $valorAtual],
                        'av' => $this->calcularAV($valorAtual, $receitaLiquida),
                        'ah' => $this->calcularAH($valorAtual, $valorAnterior),
                        'anterior' => $valorAnterior
                    ];
                });
        }
    }

    public function limparValor($valor)
    {
        if (is_null($valor) || $valor === '')
            return 0;

        $valor = str_replace('.', '', $valor);
        $valor = str_replace(',', '.', $valor);

        return floatval(preg_replace('/[^0-9\.-]/', '', $valor));
    }

    public function calcularAV($valor, $base)
    {
        $valor = $this->limparValor($valor);
        $base = $this->limparValor($base);

        if ($base == 0)
            return 0;

        return ($valor / $base) * 100;
    }

    public function calcularAH($valorAtual, $valorAnterior)
    {
        if ($valorAnterior <= 0) {
            return null; // Evita divisão por 0 ou negativos
        }

        return (($valorAtual - $valorAnterior) / abs($valorAnterior)) * 100;
    }

    public function getValorAnoAnteriorBP($categoriaNome, $anoAtual)
    {
        $anoAnterior = $anoAtual - 1;
        $valores = BalancoPatrimonial::where('categoria', $categoriaNome)
            ->where('year', $anoAnterior)
            ->pluck('valor');

        return $valores->sum(fn($v) => $this->limparValor($v));
    }

    public function getValorAnoAnteriorDRE($categoriaNome, $anoAtual)
    {
        $anoAnterior = $anoAtual - 1;
        $valores = Dres::where('categoria', $categoriaNome)
            ->where('year', $anoAnterior)
            ->pluck('valor');

        return $valores->sum(fn($v) => $this->limparValor($v));
    }
}