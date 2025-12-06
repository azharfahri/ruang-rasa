<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemDetail extends Model
{
    protected $fillable = [
        'order_item_id',
        'variant_option_id',
        'price_impact'
    ];

    protected $casts = [
        'price_impact' => 'decimal:2',
    ];

    /**
     * Detail ini dimiliki oleh satu OrderItem.
     */
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Detail ini merujuk ke satu VariantOption yang dipilih.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(VariantOption::class, 'variant_option_id');
    }
}
