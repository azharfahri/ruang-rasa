<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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

    // ==========================
    //       RELASI
    // ==========================

    // Satu cabang punya banyak user (cashier / admin cabang)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Satu cabang punya banyak produk (dari branch_products)
    public function branchProducts()
    {
        return $this->hasMany(BranchProduct::class);
    }

    // Relasi ke orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relasi melalui pivot branch_products → products
    public function products()
    {
        return $this->belongsToMany(Product::class, 'branch_products')
                    ->withPivot(['stock', 'price_override', 'status'])
                    ->withTimestamps();
    }
}
