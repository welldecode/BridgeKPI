<?php

namespace App\Livewire\Admin\Demo;


use App\Models\CategoryBP;
use App\Models\BalancoPatrimonial;
use Carbon\Carbon;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class BP extends Component
{
    public $tipo;
    public $anos;
    public $meses;
    public $trimestres;
    public $type = '';
    public $consultaRealizada = false;
    public $categorys;
    public $dados = [];
    public $values = [];
    public $periodos = [];

    public bool $showAno = false;
    public bool $showMes = false;
    public bool $showTrimestre = false;
    public bool $showTypeSelect = true;  // controla se o select aparece



    public function render()
    {
        return view('livewire.admin.demo.bp')->layout('layouts.admin');
    }
    public function onTypeChange()
    {
        // Aqui você pode fazer a lógica quando o type mudar
        // por exemplo, resetar selects, setar variáveis para mostrar/esconder etc.

        $this->anos = [];
        $this->meses = [];
        $this->trimestres = [];
        $this->showTypeSelect = true;


        if ($this->type === 'mensal') {
            $this->showAno = true;
            $this->showMes = true;
            $this->showTrimestre = false;
        } elseif ($this->type === 'trimestral') {
            $this->showAno = true;
            $this->showMes = false;
            $this->showTrimestre = true;
        } elseif ($this->type === 'anual') {
            $this->showAno = true;
            $this->showMes = false;
            $this->showTrimestre = false;  
        } else {
            $this->showAno = false;
            $this->showMes = false;
            $this->showTrimestre = false;
        }
    }

    public function getDados()
    {
        $this->reset('dados', 'periodos');

        $this->categorys = CategoryBP::all();

        $this->periodos = [];
 
        foreach ($this->anos as $ano) {
            // Monta a lista de períodos válidos com base no tipo
            $labelsPeriodo = [];

            if ($this->type === 'mensal') {
                foreach ($this->meses as $mes) {
                    $labelsPeriodo[] = Carbon::createFromDate($ano, $mes + 1, 1)->format('m/Y');
                    // Inverte a ordem pra começar do último mês pro primeiro 
                }
                $labelsPeriodo = array_reverse($labelsPeriodo);
            }

            if ($this->type === 'trimestral') {
                foreach ($this->trimestres as $t) {
                    $labelsPeriodo[] = "{$t}T/{$ano}";
                }
                $labelsPeriodo = array_reverse($labelsPeriodo);
            }
            if ($this->type === 'anual') {
                $labelsPeriodo[] = $ano;
                $labelsPeriodo = array_reverse($labelsPeriodo);
            }


            // Garante que todos os períodos existam no array geral
            foreach ($labelsPeriodo as $label) {
                if (!in_array($label, $this->periodos)) {
                    $this->periodos[] = $label;
                }
            }
        }

        // Opcional: inverter o array geral de períodos se necessário (caso tenha períodos de anos diferentes)
        if ($this->type === 'mensal' || $this->type === 'trimestral') {
            $this->periodos = array_reverse($this->periodos);
        }

        // Busca todas as categorias com os dados relacionados (se houver)
        $categorias = CategoryBP::with([
            'balancopatrimonial' => function ($q) {
                $q->where('type', $this->type);

                if ($this->type === 'mensal') {
                    $q->whereIn('month', $this->meses)->whereIn('year', $this->anos);
                }

                if ($this->type === 'trimestral') {
                    $periodos = collect($this->periodos)->toArray();
                    $q->whereIn('periodo', $periodos);
                }

                if ($this->type === 'anual') {
                    $q->whereIn('year', $this->anos);
                }
            }
        ])->get();

        // Prepara os dados, mesmo se não houver valores
        foreach ($categorias as $cat) {
            $this->dados[$cat->id]['categoria'] = $cat;

            $valoresIndexados = [];

            foreach ($cat->balancopatrimonial as $bpItem) {
                if ($this->type === 'mensal') {
                    if ($bpItem->month === 0) {
                        $label = '01/' . ($bpItem->year + 1);
                    } else {
                        $label = str_pad($bpItem->month + 1, 2, '0', STR_PAD_LEFT) . '/' . $bpItem->year;
                    }
                } elseif ($this->type === 'trimestral') {
                    $label = $bpItem->periodo;
                } else {
                    $label = $bpItem->year;
                }

                $valoresIndexados[$label] = $bpItem->valor ?? 0;
            }

            // Usa a mesma ordem dos períodos invertidos
            foreach ($this->periodos as $label) {
                $this->dados[$cat->id]['valores'][$label] = $valoresIndexados[$label] ?? '';
            }
        }
        if ($this->type === 'anual') {
            $this->periodos = $this->periodos;
        } else {
            $this->periodos = array_reverse($this->periodos);
        }
    }
    public function apagarAno($ano)
    {
        $this->dispatch('show-log');

        LivewireAlert::title('Deseja Apagar os dados?')
            ->text('Confirma que deseja apagar os dados do período: ' . $ano . '?')
            ->position('center')
            ->info()
            ->timer(6000)
            ->withConfirmButton('Sim, confirmar!')
            ->confirmButtonColor('#3085d6')
            ->onConfirm('saveDeleteDados', ['id' => $ano])
            ->withCancelButton('Cancelar')
            ->cancelButtonColor('#d33')
            ->show();
    }

    public function saveDeleteDados($data)
    {
        $periodo = $data['id'] ?? null;

        if (!$periodo) {
            LivewireAlert::title('Erro')
                ->text('Período inválido.')
                ->position('center')
                ->error()
                ->timer(4000)
                ->show();
            return;
        }

        $query = BalancoPatrimonial::where('type', $this->type);

        if ($this->type === 'anual') {
            $query->where('year', $periodo);
        }

        if ($this->type === 'mensal') {
            if (!preg_match('/^\d{2}\/\d{4}$/', $periodo)) {
                LivewireAlert::title('Erro')
                    ->text('Formato de período inválido. Use mm/aaaa.')
                    ->position('center')
                    ->error()
                    ->timer(4000)
                    ->show();
                return;
            }

            [$mes, $ano] = explode('/', $periodo);

            $mes = (int) $mes - 1;
            $ano = (int) $ano;

            if ($mes < 0 || $mes > 11) {
                LivewireAlert::title('Erro')
                    ->text('Mês inválido.')
                    ->position('center')
                    ->error()
                    ->timer(4000)
                    ->show();
                return;
            }

            // Adicione os filtros corretamente
            $query->where('year', $ano)
                ->where('month', $mes);
        }


        if ($this->type === 'trimestral') {
            $query->where('periodo', $periodo);
        }
        if ($query->count() > 1000) {
            LivewireAlert::title('Alerta')
                ->text('Muitos dados encontrados. Cancelado para segurança.')
                ->position('center')
                ->warning()
                ->timer(6000)
                ->show();
            return;
        }
        // ✅ Antes de apagar, valide se a query está correta:
        $dados = $query->get();

        if ($dados->isEmpty()) {
            LivewireAlert::title('Erro ao Apagar dados')
                ->text('Nenhum dado encontrado para apagar.')
                ->position('center')
                ->error()
                ->timer(6000)
                ->show();
            return;
        }

        $query->delete();

        $this->getDados();

        LivewireAlert::title('Sucesso')
            ->text("Dados do período {$periodo} apagados com sucesso.")
            ->position('center')
            ->success()
            ->timer(4000)
            ->show();
    }
}