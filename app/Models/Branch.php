<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'latitude',
        'longitude',
        'open_time',
        'close_time',
    ];

    // 🔗 RELASI

    /**
     * Branch punya banyak user (admin / cashier / staff)
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Branch punya banyak order
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function branchProducts(): HasMany
    {
        return $this->hasMany(BranchProduct::class);
    }

    /**
     * Branch punya banyak produk melalui branch_products
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(
            Product::class,
            'branch_products'
        )->withPivot([
            'stock',
            'price_override',
            'status'
        ])->withTimestamps();
    }

    // 🧠 Helper (opsional tapi cakep)

    public function isOpen(): bool
    {
        if (!$this->open_time || !$this->close_time) {
            return true;
        }

        $now = now()->format('H:i');

        return $now >= $this->open_time && $now <= $this->close_time;
    }
}
