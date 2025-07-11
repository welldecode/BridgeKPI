<?php

namespace App\Livewire\Admin\Dados;

use Livewire\Component;

class Dre extends Component
{

    public $insertionType;
    public $insertionyear;
    public $insertionmonth;

    public $availableMonths = [
        'Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho',
        'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'
    ];

    public $i = 25;
    public $grid_items = [];

    public function render()
    {
        return view('livewire.admin.dados.dre')->layout('layouts.admin');
    }

    public function mount() {
           // Inicializa com um mês padrão e uma grade vazia
           $this->selectedMonth = now()->format('F');
           $this->addGrade(); // Adiciona uma linha inicial
    }
    public function addGrade()
    {
        $this->grid_items[] = ['name' => '', 'value' => 0];
    }

    public function addInsertion()
    {

        for ($i = 1; $i <= $this->i; $i++) {
            $this->addGrid();
        }

        $this->removeGrid($this->i);
    } 

    public function addGrid($i = 0)
    {
        $this->i = $this->i + $i;
        array_push($this->grid_items, []);
    }

    public function removeGrid($i)
    {
        array_splice($this->grid_items, $i, 1);
    }
}


