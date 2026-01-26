<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_id',
        'payment_method',
        'amount',
        'status',
        'gateway_response'
    ];

    protected $casts = [
        'gateway_response' => 'array',
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }
}
