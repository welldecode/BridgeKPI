<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use \Staudenmeir\EloquentJsonRelations\HasJsonRelationships;

class BalancoPatrimonial extends Model
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
     
    public function categoryBP()
    {
        return $this->belongsTo(CategoryBP::class, 'category_bp_id'); // Relacionamento com CategoryBP
    }
    public function balancopatrimonial()
{
    return $this->hasMany(BalancoPatrimonial::class, 'categoria', 'name');
}
}
