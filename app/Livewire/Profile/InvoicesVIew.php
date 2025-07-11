<?php

namespace App\Livewire\Profile;

use App\Models\Order;
use App\Models\Plan;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;

class InvoicesVIew extends Component
{ 
    public $orders;
    public $id;

    public $plan;

    public function render()
    {
        return view('livewire.profile.invoices')->layout('layouts.admin');
    }

    public function mount($id)
    {

        $this->id = $id;
        $this->orders = Order::where('order_number', $id)->firstOrFail();
        $this->plan = Plan::where('id', $this->orders->plan_id)->firstOrFail();
    }

}
