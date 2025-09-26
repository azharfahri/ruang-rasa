<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'variant_details', // JSON
        'notes', // TEXT
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'variant_details' => 'array', // Casting JSON ke Array/Collection PHP
    ];

    // Relasi: Item dimiliki oleh satu Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    // Relasi: Item merujuk ke satu Product (untuk mendapatkan nama/gambar)
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
