<?php

// app/Models/Transaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_gateway',          // BARU: Gateway yang digunakan (e.g., 'midtrans', 'cash')
        'payment_method',
        'amount',
        'gateway_transaction_id',   // BARU: ID unik dari gateway mana pun
        'status',
        'gateway_details',          // BARU: Gantikan raw_response
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'gateway_details' => 'array', // Casting JSON fleksibel

        // midtrans_id dan raw_response DIHAPUS
    ];

    // --- Relasi ---

    /**
     * Relasi: Transaksi dimiliki oleh satu Order.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
