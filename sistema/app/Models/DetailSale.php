<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailSale extends Model
{
    protected $table = 'detail_sales';

    protected $fillable = ['sale_id', 'medicine_id', 'amount', 'price_unit', 'subtotal'];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'price_unit' => 'decimal:2',
            'subtotal' => 'decimal:2',
        ];
    }

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }
}
