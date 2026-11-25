<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
<<<<<<< Updated upstream

class Customer extends Model
{
    //
=======
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = ['name', 'dni', 'phone', 'age'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
>>>>>>> Stashed changes
}
