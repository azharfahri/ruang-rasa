<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RefundItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'refund_id',
        'order_item_detail_id',
        'qty',
        'amount'
    ];

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function orderItemDetail()
    {
        return $this->belongsTo(OrderItemDetail::class);
    }
}
