<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'type',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    protected static function booted()
    {
        static::deleting(function ($category) {
            if ($category->products()->exists()) {
                throw new \Exception(
                    "Kategori masih digunakan oleh produk, tidak bisa dihapus."
                );
            }
        });
    }
}
