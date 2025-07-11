<?php

namespace App\Livewire\Coupons;

use App\Models\Coupon;
use Livewire\Component;
use App\Facades\Cart as CartFacade;

class ApplyCoupon extends Component
{
    public $code; 
    public $discountedTotal;
    public $totalPrice;

    public function apply()
    {
        $coupon = Coupon::where('code', $this->code)->first();
        $session_coupon = CartFacade::getCoupon();

        if (!$coupon) {
            session()->flash('error', 'Cupom inválido.');
            return;
        }

        if (!$coupon->isValid()) {
            session()->flash('error', 'Cupom expirado ou limite de uso atingido.');
            return;
        }

        if (isset($session_coupon['code'])) {
            session()->flash('error', 'Você ja adicionou um cupom.');
            return;
        }

        $this->totalPrice = CartFacade::getTotalPrice();
         
        $coupon->increment('times_used');
  
        session()->put('coupon', [
            'id' => $coupon->id,
            'code'  => $coupon->code,
            'value' =>  $coupon->discount
        ]); 
         
        $this->dispatch('cartupdated');
        $this->dispatch('productAdded');   
        session()->flash('success', 'Cupom aplicado com sucesso!');
 
        $this->dispatch('success');
    }
    
    public function render()
    {
        return view('livewire.coupons.apply-coupon');
    }
}