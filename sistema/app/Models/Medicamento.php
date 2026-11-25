<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Medicamento extends Model
{
    public const STOCK_BAJO = 10;

    protected $fillable = ['category_id', 'name', 'description', 'price', 'stock'];

    protected function casts(): array
    {
        return ['price' => 'decimal:2'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(Detail::class, 'medicine_id');
    }

    public function scopeStockBajo($query)
    {
        return $query->where('stock', '<=', self::STOCK_BAJO);
    }
}
