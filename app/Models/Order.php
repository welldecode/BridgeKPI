<?php

namespace App\Models;

use App\Facades\Cart;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    //
    protected $fillable = ['user_id',  'plan_id','order_number', 'sub_total', 'payment_order', 'quantity', 'status', 'external_reference','total_amount', 'first_name', 'last_name', 'country', 'post_code',  'phone', 'email', 'payment_method', 'payment_status', 'shipping_id', 'coupon'];


    public function cart_info(): HasMany
    {
        return $this->hasMany(Cart::class, 'order_id', 'id');
    }

    public static function getAllOrder($id)
    {
        return Order::with('cart_info')->find($id);
    }

    public function cart(): HasMany {
        return $this->hasMany(Cart::class);
    } 

    public function user(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }
}
