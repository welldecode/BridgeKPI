<?php

namespace App\Livewire\Payment;

use Illuminate\Support\Facades\Redirect;
use Livewire\Component;
use App\Facades\Cart as CartFacade;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class Checkout extends Component
{
    public $cart;
    public $totalPrice;

    public $mp;
    public $type_payment = ['types' => ['card_credit' => ['active', 'credit_cart'], 'pix' => ['active', 'pix'], 'boleto' => ['active', 'method_boleto']]];

    public function render()
    {
        return view('livewire.payment.checkout')->layout('layouts.guest');
    }

    public function mount()
    {

        $this->cart = CartFacade::get(); 
        $this->totalPrice = CartFacade::getTotalPrice(); 
        
      
    }
    private function getPaymentDetails($paymentId)
    {

        MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        $client = new PaymentClient();
        $payment = $client->get($paymentId);

        return $payment ? $payment : null;
    }
}
