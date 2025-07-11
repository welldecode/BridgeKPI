<?php

namespace App\Models;

use App\Observers\PlanSubscriptionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[ObservedBy([PlanSubscriptionObserver::class])]
class PlanSubscriptions extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $fillable = [
        'id_user',
        'id_plan',
        'start_date',
        'end_date',
        'trial_end_date', 
        'next_billing_date',
        'stats', 
    ];  
    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'next_billing_date', 'datatime',
        'trial_end_date' => 'datetime'
    ];

    protected $dates = ['next_billing_date', 'end_date', 'start_date'];
    public function plan(): BelongsTo 
    {
        return $this->belongsTo(Plan::class, 'id_plan', 'id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id');
    }

}
