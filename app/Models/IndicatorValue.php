<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IndicatorValue extends Model {
    protected $fillable = ['indicator_id', 'year', 'month', 'quarter', 'value', 'comment'];

    public function indicator(): BelongsTo {
        return $this->belongsTo(Indicator::class);
    }
}
