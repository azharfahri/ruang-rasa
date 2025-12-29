<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'variant_option_id',
        'price_impact',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function variantOption(): BelongsTo
    {
        return $this->belongsTo(VariantOption::class);
    }
}
