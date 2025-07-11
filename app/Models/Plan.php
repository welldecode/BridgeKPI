<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration',
        'trial_period',
        'monthly_prices',
        'year_prices',
    ];

    public function getPrice(array $optionIds = [])
    {

        return number_format($this->monthly_prices, 2);
    }

}
