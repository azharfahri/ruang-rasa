<?php
namespace App\Models;

use App\Models\Product;
use App\Models\VariantOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VariantType extends Model
{
    protected $fillable = ['product_id', 'name', 'input_type'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(VariantOption::class);
    }
}
