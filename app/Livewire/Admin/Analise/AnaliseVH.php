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

    public function render()
    {
        $this->getDados();
        return view('livewire.admin.analise.analiseVH')->layout('layouts.admin');
    }

    public function getDados()
    {
        $this->reset('dados');

        if ($this->order === 'bp') {
            // Captura o valor do ATIVO TOTAL
            $ativoTotal = BalancoPatrimonial::where('year', $this->anos)
                ->where('categoria', 'ATIVO')
                ->value('valor');
            CategoryBP::with([
                'balancopatrimonial' => function ($query) {
                    $query->where('year', $this->anos);
                }
            ])->get()->each(function ($categoria) use ($ativoTotal) {
                $valorAtual = $categoria->balancopatrimonial
                    ? $categoria->balancopatrimonial->sum(fn($item) => $this->limparValor($item->valor))
                    : 0;

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
            // Captura o valor da RECEITA LÍQUIDA
            $receitaLiquida = Dres::where('year', $this->anos)
                ->where('categoria', 'LIKE', '%RECEITA OPERACIONAL LÍQUIDA%')
                ->sum('valor');

            CategoryDre::with([
                'dre' => function ($query) {
                    $query->where('year', $this->anos);
                }
            ])->get()->each(function ($categoria) use ($receitaLiquida) {
                $valorAtual = $categoria->dre
                    ? $categoria->dre->sum(fn($item) => $this->limparValor($item->valor))
                    : 0;

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