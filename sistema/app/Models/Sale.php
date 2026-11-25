<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    /** Tasa de IGV (Perú). Los precios de los medicamentos ya incluyen IGV. */
    public const IGV_RATE = 0.18;

    protected $fillable = ['customer_id', 'user_id', 'date', 'total', 'igv'];

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'total' => 'decimal:2',
            'igv' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(DetailSale::class);
    }

    public function getCodeAttribute(): string
    {
        return 'V-' . str_pad((string) $this->id, 6, '0', STR_PAD_LEFT);
    }

    public function getSubtotalAttribute(): string
    {
        return number_format((float) $this->total - (float) $this->igv, 2, '.', '');
    }
}
