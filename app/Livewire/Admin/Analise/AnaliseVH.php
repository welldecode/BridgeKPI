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

    public function render()
    {
        $this->getDados();
        return view('livewire.admin.analise.analiseVH')->layout('layouts.admin');
    }
public function getMesesDoTrimestre()
{
    return match((int)$this->trimestreSelecionado) {
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

    if ($this->order === 'bp') {
        // Captura o valor do ATIVO TOTAL de acordo com o tipo selecionado
        $queryAtivo = BalancoPatrimonial::where('year', $this->anos)
            ->where('categoria', 'ATIVO');

        if ($this->type === 'mensal') {
            $queryAtivo->where('mes', $this->mesSelecionado);
        } elseif ($this->type === 'trimestral') {
            $queryAtivo->whereIn('mes', $this->getMesesDoTrimestre());
        }

        $ativoTotal = $queryAtivo->sum('valor');

        CategoryBP::with(['balancopatrimonial' => function ($query) {
            $query->where('year', $this->anos);
            if ($this->type === 'mensal') {
                $query->where('mes', $this->mesSelecionado);
            } elseif ($this->type === 'trimestral') {
                $query->whereIn('mes', $this->getMesesDoTrimestre());
            }
        }])->get()->each(function ($categoria) use ($ativoTotal) {
            $valorAtual = $categoria->balancopatrimonial
                ? $categoria->balancopatrimonial->sum(fn($item) => $this->limparValor($item->valor))
                : 0;

            $valorAnterior = $this->getValorAnoAnteriorBP($categoria->name, $this->anos);

            $this->dados[] = [
                'name' => $categoria->name,
                'nivel' => $categoria->nivel,
                'type' => $categoria->type ?? $this->type,
                'bp' => ['value' => $valorAtual],
                'av' => $this->calcularAV($valorAtual, $ativoTotal),
                'ah' => $this->calcularAH($valorAtual, $valorAnterior),
                'anterior' => $valorAnterior
            ];
        });

    } else {
        // Captura o valor da RECEITA LÍQUIDA conforme tipo
        $queryReceita = Dres::where('year', $this->anos)
            ->where('categoria', 'LIKE', '%RECEITA OPERACIONAL LÍQUIDA%');

        if ($this->type === 'mensal') {
            $queryReceita->where('mes', $this->mesSelecionado);
        } elseif ($this->type === 'trimestral') {
            $queryReceita->whereIn('mes', $this->getMesesDoTrimestre());
        }

        $receitaLiquida = $queryReceita->sum('valor');

        CategoryDre::with(['dre' => function ($query) {
            $query->where('year', $this->anos);
            if ($this->type === 'mensal') {
                $query->where('mes', $this->mesSelecionado);
            } elseif ($this->type === 'trimestral') {
                $query->whereIn('mes', $this->getMesesDoTrimestre());
            }
        }])->get()->each(function ($categoria) use ($receitaLiquida) {
            $valorAtual = $categoria->dre
                ? $categoria->dre->sum(fn($item) => $this->limparValor($item->valor))
                : 0;

            $valorAnterior = $this->getValorAnoAnteriorDRE($categoria->name, $this->anos);

            $this->dados[] = [
                'name' => $categoria->name,
                'nivel' => $categoria->nivel,
                'type' => $categoria->type ?? $this->type,
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