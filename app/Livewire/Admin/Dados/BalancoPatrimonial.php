<?php

namespace App\Livewire\Admin\Dados;

use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;

class BalancoPatrimonial extends Component
{
 
  use WithFileUploads;

    #[Validate('required|file|mimes:xlsx,xls')]
    public $arquivo;

    public Collection $dados;

    public function render()
    {
        return view('livewire.admin.dados.balancoPatrimonial')->layout('layouts.admin');
    }

   public function mount()
    {
        $this->dados = collect();
    }

    public function importar()
    {
        $this->validate();

        $this->dados = collect(Excel::toArray([], $this->arquivo)[0]); // Primeira aba da planilha
    }

}


