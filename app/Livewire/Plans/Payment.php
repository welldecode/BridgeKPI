<?php

namespace App\Livewire\Plans;
 
use App\Facades\Cart;
use Livewire\Component;
use Masmerise\Toaster\Toaster; 

class Payment extends Component
{
   
    public function render()
    {
        return view('livewire.plans.payment')->layout('layouts.guest');
    }

    public function addToCart(int $productId )
    {
        Cart::addToCart($productId  );  
        
        Toaster::success('Seu Produto foi adicionado do carrinho');
       
    }
}

