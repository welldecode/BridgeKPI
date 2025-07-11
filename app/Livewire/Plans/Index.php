<?php

namespace App\Livewire\Plans;

use App\Facades\Cart;
use App\Models\Plan;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class Index extends Component
{
    public $plans;
     
    public function render()
    {
        return view('livewire.plans.index');
    }
    public function mount()
    {
        $this->plans = Plan::all();
    }
    
    public function addToCart(int $productId )
    {
        Cart::addToCart($productId  ); 

        $this->dispatch('productAdded'); 
        $this->dispatch('openCart'); 
        $this->dispatch('cartupdated');
        
        Toaster::success('Seu Produto foi adicionado do carrinho');
       
    }
}
