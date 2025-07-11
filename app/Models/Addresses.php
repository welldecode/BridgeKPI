<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{

    use HasFactory; 

    protected $fillable = [
        'user_id',
        'address',
        'zip_code', 
    ];
    protected $casts = [
        'address' => 'array', // Cast JSON column to array  
    ];
     
}
