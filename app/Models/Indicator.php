<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Indicator extends Model {
    protected $fillable = ['name', 'frequency', 'unit', 'user_id', 'show_arrow'];

    public function values(): HasMany {
        return $this->hasMany(IndicatorValue::class);
    }
}
