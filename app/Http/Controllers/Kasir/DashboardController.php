<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('kasir.dashboard', [
            'totalOrders' => Order::where('branch_id', $user->branch_id)->count(),

            'todayOrders' => Order::where('branch_id', $user->branch_id)
                ->whereDate('created_at', today())
                ->count(),

            'totalIncome' => Order::where('branch_id', $user->branch_id)
                ->where('payment_status', 'paid')
                ->sum('total'),
        ]);
    }
}
