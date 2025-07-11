<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CategoryBP extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'relation',
        'nivel', 
        'type', 
    ];
  
    public function bp():HasOne
    {
        return $this->HasOne(BalancoPatrimonial::class,  'categoria',   'name');
    }
    
    public function balancopatrimonial()
    {
        return $this->hasMany(BalancoPatrimonial::class, 'categoria', 'name');
    }
    
}