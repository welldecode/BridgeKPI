<?php

namespace App\Livewire\Components\Cart;

use App\Helper\CartItems;
use App\Facades\Cart as CartFacade;
use App\Models\Cart;
use App\Models\Coupon;
use Livewire\Component;
use Masmerise\Toaster\Toaster;

class CartPage extends Component
{

    public $cart;
    public $coupon;
    public $totalPrice;
    public $getPrice;
    public $totalPriceCoupon;
    public $quantity = 0;

    protected $listeners = [
        'openCart',
        'cartupdated' => 'mount',
    ];

    public function render()
    {
        return view('livewire.components.cart.cart');
    }

    public function mount(): void
    { 
        $this->cart = CartFacade::get(); 
        $this->totalPrice = CartFacade::getTotalPrice(); 
        $this->getPrice = CartFacade::getPrice(); 
        $this->totalPriceCoupon = CartFacade::getTotalPriceCoupon();
        $this->coupon = CartFacade::getCoupon();
    }

    public function changeQuantity()
    {
        CartFacade::changeQuantity($this->quantity);

        $this->dispatch('cartupdated');
        $this->dispatch('productAdded');
    }

    public function addQuantity()
    { 
        $this->quantity++;
        $this->changeQuantity();
        if ($this->quantity > 10) {
            $this->quantity = 10;
        } 
    }

    public function removeQuantity()
    {
        $this->quantity--;
        $this->changeQuantity();
        if ($this->quantity < 1) {
            $this->quantity = 1;
        }
    }


    public function removeCoupon()
    {
        request()->session()->put('coupon', []);

        $this->dispatch('cartupdated');
        $this->dispatch('productAdded');
        Toaster::success('Cupom removido com sucesso!');
    }
    public function destroy($id)
    {

        CartFacade::remove($id);

        $this->dispatch('cartupdated');
        $this->dispatch('productAdded');

        Toaster::error('Seu Produto foi removido do carrinho!');

    }

}
