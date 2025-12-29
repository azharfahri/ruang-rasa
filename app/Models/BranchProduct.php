<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchProduct extends Model
{
    use HasFactory;

    protected $table = 'branch_products';

    protected $fillable = [
        'branch_id',
        'product_id',
        'stock',
        'price_override',
        'status',
    ];


    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    // 🧠 HELPER

    public function getFinalPriceAttribute(): float
    {
        return $this->price_override ?? $this->product->price;
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->stock > 0;
    }
}
