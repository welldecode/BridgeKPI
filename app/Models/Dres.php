<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Dres extends Model
{
     use HasFactory;

  
     protected $fillable = [
        'year', 
        'month', 
        'value',
        'type',
        'categoria',
        'periodo',
        'periodo_normalizado',
        'valor',
        'stats',
    ];

    protected $casts = [
        'value' => 'array', // Cast JSON column to array  
    ];
    
    public function dres():HasOne
    {
        return $this->HasOne(CategoryDre::class,  'name',   'categoria');
    }

    
    public function categorydre()
    {
        return $this->belongsTo(CategoryDre::class, 'name'); // Relacionamento com CategoryBP
    }
}
