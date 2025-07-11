<?php

namespace App\Livewire\Profile;

use App\Models\Order;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class Invoices extends Component
{ 

    public $orders;

    public function render()
    {
        return view('livewire.profile.sections.invoices')->layout('layouts.admin');
    }

    public function mount() { 
        $this->orders = Order::where('user_id', Auth()->user()->id)->get(); 
    }
 
}
