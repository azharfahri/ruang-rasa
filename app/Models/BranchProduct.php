<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'product_id',
        'stock',
        'price_override',
        'status',
    ];

    // ==========================
    //        RELASI
    // ==========================

    // Cabangnya
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // Produknya
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
