<?php

namespace App\Livewire\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Redirect;
use Str;
use Livewire\Component;
use App\Facades\Cart as CartFacade;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
class Orders extends Component
{

    public $cart, $totalPrice;

    public $order;
    public $address;

    public $id;
    public $payment;

    public function render()
    {
        return view('livewire.payment.order')->layout('layouts.guest');
    }

    public function mount()
    {
        Order::where('order_number', $this->id)->where('payment_method', $this->payment)->get()->each(function (Order $order) {
            $this->order = $order;
        });

        $this->cart = CartFacade::get();
        $this->totalPrice = CartFacade::getTotalPrice();
        $this->dispatch('clearCart');

        if (isset($this->cart)) { 
            return Redirect::to('cart.index');
       }
         
        
        $this->js("setTimeout(function() {window.location.replace('/user/dashboard'); }, 1200);");
    }

    public function getPaymentMethodPix($type = '')
    {

        MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
        try {
            $client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(['X-Idempotency-Key: ' . Str::uuid()->toString() . '']);

            $client->get($this->order->payment_order);

            $payment = $client->create([
                "transaction_amount" => (float) $this->order->sub_total,
                "payment_method_id" => $this->payment,
                "payer" => [
                    "email" => auth()->user()->email
                ]
            ], $request_options); 
            $response = $payment->point_of_interaction->transaction_data;
            echo $response->$type;
        } catch (MPApiException $e) {
            echo "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
            echo "Content: ";
            var_dump($e->getApiResponse()->getContent());
            echo "\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

}
