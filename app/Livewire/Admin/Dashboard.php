<?php

namespace App\Livewire\Admin;

use App\Models\Dres;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public array $kpis = [];
    public array $anosRecentes = [];
    public array $anosDisponiveis = [];

    public string $periodicidade = 'anual';

    public function mount(): void
    {
        $this->atualizarDisponiveis();
    }

    public function atualizarDisponiveis()
    {
        // Busca anos disponíveis para a periodicidade atual
        $this->anosDisponiveis = Dres::where('type', $this->periodicidade)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->pluck('periodo')
            ->unique()
            ->values()
            ->toArray();

        $this->gerarKpis();
    }

    public function updatedPeriodicidade()
    {
        $this->atualizarDisponiveis();
    }

    public function gerarKpis(): void
    {
        // Pega os 4 últimos anos distintos, independentemente do tipo
      $this->anosRecentes = Dres::select('year')
    ->distinct()
    ->orderByDesc('year')
    ->limit(4)
    ->pluck('year')
    ->values()
    ->toArray();

        // Busca registros para os 4 anos recentes e para a periodicidade selecionada
        $registros = Dres::where('type', $this->periodicidade)
            ->whereIn('year', $this->anosRecentes)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get()
            ->groupBy('categoria');

        $mapa = [
            'Lucro Líquido' => '(=) RESULTADO LÍQUIDO DO EXERCÍCIO',
            'EBITDA' => '(=) EBITDA',
            'EBIT' => '(=) EBIT',
        ];

        $this->kpis = collect($mapa)->map(function ($categoriaBanco, $titulo) use ($registros) {
            $serie = $this->coletarSerie($registros, $categoriaBanco);
            return [
                'id' => Str::slug($titulo),
                'title' => $titulo,
                'valor' => $serie[array_key_first($serie)] ?? 0,
                'serie' => array_values($serie),
                'sufix' => $titulo === 'ROE' ? '%' : '',
            ];
        })->values()->toArray();

        // Fluxo de Caixa Livre
        $serieFcl = collect($this->anosRecentes)->map(function ($periodo) use ($registros) {
            $ebitda = $this->valor($registros, '(=) EBITDA', $periodo);
            $ir = $this->valor($registros, '(=) EBIT', $periodo);
            return $ebitda - $ir;
        })->values()->toArray();

        $this->kpis[] = [
            'id' => 'fluxo-caixa-livre',
            'title' => 'Fluxo de Caixa Livre',
            'valor' => $serieFcl[0] ?? 0,
            'serie' => $serieFcl,
            'sufix' => '',
        ];

        $this->dispatch(
            'atualizar-kpis',
            anos: $this->anosRecentes,
            kpis: $this->kpis
        );
    }

    private function coletarSerie($registros, string $categoria): array
    {
        return collect($this->anosRecentes)->mapWithKeys(function ($periodo) use ($registros, $categoria) {
            return [$periodo => $this->valor($registros, $categoria, $periodo)];
        })->toArray();
    }

    private function valor($registros, string $categoria, int $ano): float
    {
        $item = $registros[$categoria]?->firstWhere('year', $ano);
        $valor = $item->valor ?? 0;
        return (float) str_replace(['.', ','], ['', '.'], $valor);
    }

    public function render()
    {
        return view('livewire.admin.dashboard')
            ->layout('layouts.admin');
    }
}
