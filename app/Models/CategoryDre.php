<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CategoryDre extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'relation',
        'nivel', 
        'type', 
    ];
    public function dre()
    {
        return $this->hasMany(Dres::class, 'categoria',   'name');
    }
    public function dres():HasOne
    {
        return $this->HasOne(Dres::class,  'categoria',   'name');
    }
}