<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cashier_id',
        'branch_id',
        'order_type',
        'status',
        'payment_status',
        'pickup_code',
        'total',
    ];

    // customer
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // kasir
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    // cabang
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    // item order
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // transaksi
    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
