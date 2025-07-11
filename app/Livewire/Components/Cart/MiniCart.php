<?php

namespace App\Livewire\Components\Cart;

use App\Helper\CartItems;
use App\Facades\Cart as CartFacade;
use App\Models\Cart;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class MiniCart extends Component
{
 

    public $cart;
    public $totalPrice; 
    public bool $openCart = false;
    
    protected $listeners = [
        'openCart',
        'cartupdated' => 'mount',
    ];

    public function render()
    {
        return view('components.cart.minicart');
    }

    public function mount()
    {
        $this->cart = CartFacade::get();
        $this->totalPrice = CartFacade::getTotalPrice();
    }

    public function destroy($id)
    {
        CartFacade::remove($id);

        $this->dispatch('cartupdated');
        $this->dispatch('productAdded'); 

      
    }

    public function changeQuantity(string $hash, int $quantity)
    {
        CartFacade::changeQuantity($hash, $quantity);
 
        $this->dispatch('cartupdated');
        $this->dispatch('productAdded'); 
    }

    public function openCart(): void
    {
        $this->openCart = true;
        $this->cart = CartFacade::get();
    }

}
