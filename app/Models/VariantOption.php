<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class VariantOption extends Model
{
    protected $fillable = ['variant_type_id', 'option_name', 'price_impact'];

    // VariantOption dimiliki oleh satu VariantType
    public function type(): BelongsTo
    {
        return $this->belongsTo(VariantType::class);
    }

    // VariantOption muncul di banyak OrderItemDetail
    public function orderItemDetails(): HasMany
    {
        return $this->hasMany(OrderItemDetail::class);
    }
}
