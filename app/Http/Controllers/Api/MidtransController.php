<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Midtrans\Notification;

class MidtransController extends Controller
{


    public function callback(Request $request)
    {
        \Log::info($request->all());

        $data = $request->all();

        $transaction = $data['transaction_status'] ?? null;
        $orderId = $data['order_id'] ?? null;

        if (!$transaction || !$orderId) {
            return response()->json([
                'error' => 'Invalid payload'
            ], 400);
        }

        $order = Order::where('midtrans_order_id', $orderId)->first();

        if (!$order) {
            return response()->json([
                'message' => 'Order not found'
            ], 404);
        }

        switch ($transaction) {
            case 'settlement':
            case 'capture':
                $order->update([
                    'payment_status' => 'paid',
                    'status' => 'processing'
                ]);
                break;

            case 'pending':
                $order->update([
                    'payment_status' => 'pending'
                ]);
                break;

            case 'expire':
            case 'cancel':
            case 'deny':
                $order->update([
                    'payment_status' => 'failed',
                    'status' => 'cancelled'
                ]);
                break;
        }

        return response()->json(['message' => 'OK']);
    }
}
