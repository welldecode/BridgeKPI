<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'discount',
        'type',
        'valid_from',
        'valid_to',
        'usage_limit',
        'times_used',
    ];

    public function isValid()
    {
        $now = now();
        return (!$this->valid_from || $this->valid_from <= $now) &&
               (!$this->valid_to || $this->valid_to >= $now) &&
               ($this->usage_limit === null || $this->times_used < $this->usage_limit);
    }

    public function applyDiscount($total)
    {
        if ($this->type === 'fixed') {
            return max(0, $total - $this->discount);
        }

        if ($this->type === 'percentage') {
            return max(0, $total - ($total * $this->discount / 100));
        }

        return $total;
    }
}