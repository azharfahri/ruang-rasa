<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::where('branch_id', auth()->user()->branch_id)
            ->where('payment_status', 'settlement')
            ->with(['items.product']);

        // Filter berdasarkan Search (ID atau Nama Pelanggan)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%$search%")
                    ->orWhere('customer_name', 'like', "%$search%");
            });
        }

        // Filter berdasarkan Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Tentukan Limit (Default 10)
        $limit = $request->get('limit', 10);

        $orders = $query->latest()->paginate($limit)->withQueryString();

        return view('kasir.orders.history', compact('orders'));
    }

    public function showDetail(Order $order)
    {
        // Pastikan order milik cabang kasir tersebut
        if ($order->branch_id !== auth()->user()->branch_id) return response('Unauthorized', 403);

        return view('kasir.orders.partials.detail-modal', compact('order'));
    }
}
