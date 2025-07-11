<?php

namespace App\Http\Controllers;

use App\Events\MercadoPagoWebhook;
use App\Models\Cart;
use App\Models\Order;
use App\Models\Plan;
use App\Models\PlanSubscriptions;
use App\Models\Webhook;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Resources\Preference;
use App\Facades\Cart as CartFacade;
use MercadoPago\Resources\Preference\Item;
use Illuminate\Support\Facades\Log;

use Session;
use Str;


class PaymentController extends Controller
{

    public $cart_all;
    public $order;

    public function __construct()
    {
        $user = auth()->user();

    }

    public function payments(Request $request)
    {

        MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        try {

            $client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(['X-Idempotency-Key: ' . Str::uuid()->toString() . '']);

            $cart = CartFacade::get();
            $plan = Plan::find($cart['items']['product_id']);
            $external_reference = "MP" . strtoupper(Str::random(10));

            if ($request->payment_method_id != 'bolbradesco') {
                $payment = $client->create([
                    "transaction_amount" => (isset($request->transaction_amount) ? (float) $request->transaction_amount : (float) $request->transaction_amount),
                    "token" => (isset($request->token) ? $request->token : Str::random(32)),
                    "description" => $plan->description,
                    "installments" => (isset($request->installments) ? (float) $request->installments : 1),
                    "payment_method_id" => $request->payment_method_id,
                    "notification_url" => 'https://bridges.devstep.com.br/webhooks/mercadopago?source_news=webhooks',
                    "external_reference" => $external_reference,
                    "issuer_id" => (isset($request->issuer_id) ? $request->issuer_id : 1),
                    "payer" => [
                        "entity_type" => "individual",
                        "type" => "customer",
                        "email" => (isset($request['payer']['email']) ? $request['payer']['email'] : auth()->user()->email),
                    ]
                ], $request_options);
            } else {
                $payment = $client->create([
                    "transaction_amount" => (isset($request->transaction_amount) ? (float) $request->transaction_amount : (float) $request->transaction_amount),
                    "token" => (isset($request->token) ? $request->token : Str::random(32)),
                    "description" => $plan->description,
                    "installments" => (isset($request->installments) ? (float) $request->installments : 1),
                    "payment_method_id" => $request->payment_method_id,
                    "notification_url" => 'https://bridges.devstep.com.br/webhooks/mercadopago?source_news=webhooks',
                    "external_reference" => $external_reference,
                    "issuer_id" => (isset($request->issuer_id) ? $request->issuer_id : 1),
                    "payer" => [
                        "email" => $request['payer']['email'],
                        "first_name" => $request['payer']['first_name'],
                        "last_name" => $request['payer']['last_name'],
                        "identification" => [
                            "type" => $request['payer']['identification']['type'],
                            "number" => $request['payer']['identification']['number'],
                        ]
                    ]
                ], $request_options);
            }


            $order = Order::create([
                'first_name' => auth()->user()->first_name,
                'last_name' => auth()->user()->last_name,
                'phone' => auth()->user()->phone,
                'email' => auth()->user()->email,
                'address' => json_encode(''),
                'order_number' => ('ORD-' . strtoupper(Str::random(10))),
                'user_id' => auth()->user()->id,
                'plan_id' => $plan->id,
                'quantity' => 1,
                'external_reference' => $external_reference,
                'payment_method' => $payment->payment_method_id,
                'payment_order' => $payment->id,
                'payment_status' => $payment->status,
                'sub_total' => 15,
                'total_amount' => 15,
                'status' => "new",
            ]);

            return response()->json([
                'payment_status' => $payment->status,
                'payment_method' => $payment->payment_method_id,
                'payment_id' => $payment->id,
                'redirect' => $order->order_number,
            ]);

        } catch (MPApiException $e) {
            echo "Status code: " . $e->getApiResponse()->getStatusCode() . "\n";
            echo "Content: ";
            var_dump($e->getApiResponse()->getContent());
            echo "\n";
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }



    public function handleWebhook(Request $request)
    {

        $data = $request->all();

        // // Verificar a assinatura do cabeçalho
        // $signature = $request->header('x-signature');
        // if (!$this->isValidSignature($request->getContent(), $signature)) {
        //     Log::warning('Assinatura inválida:', ['signature' => $signature]);
        //     return response()->json(['message' => 'Assinatura inválida'], 403);
        // }

        // Verifique o tipo de notificação
        if (isset($data['type']) && $data['type'] === 'payment') {

            $paymentId = $data['data']['id'];

            // Simula consulta do pagamento (substitua pelo SDK Mercado Pago se necessário)
            $paymentDetails = $this->getPaymentDetails($paymentId);

            // Atualizar o pedido no banco 
            $pedido = Order::where('payment_order', $paymentId)->first();

            if ($pedido) {

                $pedido->update(['payment_status' => 'approved']);

                $this->handleSubscription($pedido, $paymentDetails);

                return response()->json(['message' => 'Pedido atualizado e assinatura criada.'], 200);
            } else {
                Log::error('Order not found for external_reference: ' . $paymentDetails->external_reference);
            }

        }

    }

    private function getPaymentDetails($paymentId)
    {
        MercadoPagoConfig::setAccessToken(env('MERCADO_PAGO_ACCESS_TOKEN'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);

        $client = new PaymentClient();
        $payment = $client->get($paymentId);

        return $payment ? $payment : null;
    }

    private function isValidSignature($payload, $signature)
    {
        $secret =  '2fd9d6692ead2e9b28e00b29b6485ea9237e233e752a70f88b37d902c8cbd0d7'; // Configurar o segredo no .env
        $hash = hash_hmac('sha256', $payload, $secret);

        return hash_equals($hash, $signature);
    }

    private function handleSubscription($order, $paymentInfo)
    {
        $subscription = PlanSubscriptions::updateOrCreate(
            ['id_user' => $order->user_id],
            [
                'id_user' => $order->user_id,
                'id_plan' => $order->plan_id,
                'start_date' => now(),
                'end_date' => now()->addMonth(), // Example: 1-month subscription
                'trial_end_date' => now()->addMonth(), // Exemplo: 1 mês de assinatura 
                'stats' => 'active',
            ]
        );

        Log::info('Subscription updated or created for user ID: ' . $order->user_id, $subscription->toArray());
    }
}
