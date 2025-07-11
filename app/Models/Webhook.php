<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Webhook extends Model
{
    //
    use HasFactory; 
    
    protected $fillable = [
        'event_type',
        'data', 
    ];

    
    protected $casts = [
        'data' => 'array', // Cast JSON column to array  
    ];
}
