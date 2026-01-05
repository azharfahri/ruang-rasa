<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VariantOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'variant_type_id',
        'option_name',
        'price_impact',
    ];

    public function variantType(): BelongsTo
    {
        return $this->belongsTo(VariantType::class);
    }

    public function details()
    {
        return $this->hasMany(OrderItemDetail::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
