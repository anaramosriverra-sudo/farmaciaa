<?php

namespace App\Models;
<<<<<<< Updated upstream
use Iluminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Iluminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class Category extends Model
{
    public function medicines():HasMany{


    return $this->hasMany[Medicine::class];
    }
}
=======

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function medicamentos(): HasMany
    {
        return $this->hasMany(Medicamento::class);
    }
}
>>>>>>> Stashed changes
