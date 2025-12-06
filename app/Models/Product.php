<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'image',
        'stock',
        'category_id',
    ];

    // --- Relasi Baru untuk Varian Dinamis ---

    /**
     * Relasi: Produk memiliki banyak VariantType (e.g., Ukuran, Level Gula).
     * Menggantikan relasi lama ke ProductVariant.
     */
    public function variantTypes(): HasMany
    {
        return $this->hasMany(VariantType::class);
    }

    /**
     * Relasi: Produk memiliki banyak VariantOption melalui VariantType.
     * Memungkinkan Anda mengambil semua opsi varian dari suatu produk dengan mudah.
     */
    public function variantOptions(): HasManyThrough
    {
        return $this->hasManyThrough(VariantOption::class, VariantType::class);
    }

    // --- Relasi Lama (Dipertahankan) ---

    /**
     * Relasi: Produk dimiliki oleh satu Category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relasi: Produk muncul di banyak OrderItem.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
