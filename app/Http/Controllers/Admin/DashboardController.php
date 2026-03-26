<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Branch;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $salesPerBranch = Order::select('branch_id', DB::raw('SUM(total) as total_sales'))
            ->where('payment_status', 'settlement')
            ->groupBy('branch_id')
            ->with('branch')
            ->get();

        $labels = $salesPerBranch->map(fn($item) => $item->branch->name);
        $data = $salesPerBranch->map(fn($item) => $item->total_sales);
        return view('admin.dashboard', [
            'totalUsers'     => User::count(),
            'totalProducts'  => Product::count(),
            'totalCategories' => Category::count(),
            'totalOrders'    => Order::count(),
            'totalBranches'  => Branch::count(),
            'completedOrder' => Order::where('status', 'completed')->count(),
            'labels' => $labels,
            'data'   => $data,
        ]);
    }
}
