<?php

namespace App\Helper;

use App\Models\Cart;
use App\Models\Plan;
use App\Models\Products;
use App\Models\User;
use App\Models\Variations;
use Arr;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Cookie;

class CartItems
{


    public function addToCart(int $productId)
    {
        $cart = $this->get();

        $cart['items'] = [
            'product_id' => $productId,
            'quantity' => [
                'number' => 1,
                'price' => '10'
            ],

        ];
        request()->session()->put('cart', $cart);
    }

    public function get()
    {
        return request()->session()->get('cart') ?: [];
    }

    public function getCoupon()
    {

        return request()->session()->get('coupon') ?: [];
    }

    public function empty()
    {
        request()->session()->put('cart', []);
    }

    public function remove(int $productId)
    {
        $cart = $this->get();

        array_splice($cart, array_search($productId, array_column($cart, 'product_id')), 1);
        request()->session()->put('cart', $cart);
    }

    public function changeQuantity(int $quantity)
    {
        $cart = $this->get();

        if (isset($cart['items'])) {
            $cart['items']['quantity']['number'] = $quantity;
        }

        session()->put('cart', $cart);
    }


    public function getTotalProducts()
    {
        return count($this->get());
    }

    public function getTotalPrice()
    {
        $total = 0;
        
        foreach ($this->get() as $item) {
            $product = Plan::find(id: $item['product_id']);

            $total_quantity = $item['quantity']['number'] * $item['quantity']['price'];
            $price = $product->getPrice();

            $total = $price += $total_quantity;
        }
        return $total;
    }

    public function getPrice()
    {
        $total = 0;
        
        foreach ($this->get() as $item) {
            $product = Plan::find(id: $item['product_id']); 

            $total = $product->monthly_prices;
        }
        return $total;
    }

    public function getTotalPriceCoupon()
    {
        $coupon = $this->getCoupon();
        $total = 0;
        if (!empty($coupon)) {
            foreach ($this->get() as $item) {
                $product = Plan::find(id: $item['product_id']);
                $total_quantity = $item['quantity']['number'] * $item['quantity']['price'];
                $price = $product->getPrice();
    
                $total = $price += $total_quantity;
            }
            return $total -= $coupon['value'];
        }
    }
}