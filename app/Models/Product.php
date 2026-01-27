<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function variantTypes(): HasMany
    {
        return $this->hasMany(VariantType::class);
    }

    public function variantOptions(): HasMany
    {
        return $this->hasMany(VariantOption::class);
    }

    public function branchProducts(): HasMany
    {
        return $this->hasMany(BranchProduct::class);
    }

    protected static function booted()
    {
        static::deleting(function ($product) {
            if (
                $product->variantTypes()->exists() ||
                $product->variantOptions()->exists() ||
                $product->branchProducts()->exists()
            ) {
                throw new \Exception(
                    "Produk masih digunakan (variant atau cabang), tidak bisa dihapus."
                );
            }
        });
    }
}
