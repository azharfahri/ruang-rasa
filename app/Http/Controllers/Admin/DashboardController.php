<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\Branch;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'totalUsers'     => User::count(),
            'totalProducts'  => Product::count(),
            'totalCategories'=> Category::count(),
            'totalOrders'    => Order::count(),
            'totalBranches'  => Branch::count(),
            'completedOrder' => Order::where('status', 'completed')->count(),
        ]);
    }
}
