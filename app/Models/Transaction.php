<?php

// app/Models/Transaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method',
        'amount',
        'midtrans_id',
        'status',
        'raw_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw_response' => 'array', // Casting JSON ke Array/Collection PHP
    ];

    // Relasi: Transaksi dimiliki oleh satu Order
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
