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
        'cashier_id',
        'order_type',
        'status',
        'payment_status',
        'pickup_code',
        'total',
    ];

    protected $casts = [
        'total' => 'decimal:2',
        // Kolom enum lainnya bisa di-cast jika diperlukan:
        // 'order_type' => 'string',
        // 'status' => 'string',
        // 'payment_status' => 'string',
    ];

    /**
     * Relasi: Order dimiliki oleh Customer (user).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi: Order diurus oleh Cashier (user).
     * PERBAIKAN: Ganti 'casier' menjadi 'cashier' dan pastikan FK-nya benar.
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id'); // FK: cashier_id
    }

    /**
     * Relasi: Satu Order memiliki banyak OrderItem.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relasi: Satu Order memiliki SATU Transaction UTAMA (sesuai migrasi yang kita sepakati).
     * Jika Anda mengizinkan banyak upaya pembayaran untuk satu order, ganti ke HasMany.
     * Berdasarkan migrasi Langkah 4 yang menggunakan order_id->unique(), kita gunakan HasOne.
     */
    public function transaction(): HasOne
    {
        return $this->hasOne(Transaction::class);
    }
}
