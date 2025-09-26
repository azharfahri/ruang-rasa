<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'type',
        'name',
        'price_impact',
    ];

    /**
     * The attributes that should be cast.
     * Pastikan price_impact di-cast ke desimal
     */
    protected $casts = [
        'price_impact' => 'decimal:2',
    ];

    // Relasi: Varian dimiliki oleh satu Product
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
