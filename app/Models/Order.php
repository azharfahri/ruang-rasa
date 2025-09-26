<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'casier_id',
        'order_type',
        'status',
        'payment_status',
        'midtrans_booking_code',
        'midtrans_expiry_time',
        'pickup_code',
        'total',
    ];

    protected $casts = [
        'midtrans_expiry_time' => 'datetime',
        'total' => 'decimal:2',
    ];

    // Relasi: Order dimiliki oleh Customer (user)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi: Order diurus oleh Casier (user)
    public function casier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'casier_id');
    }

    // Relasi: Satu Order memiliki banyak OrderItem
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi: Satu Order memiliki banyak Transaction (upaya pembayaran)
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}
