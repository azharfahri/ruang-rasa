<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        Carbon::setLocale('id');

        $user = auth()->user();
        $branchId = $user->branch_id;
        $filter = $request->get('filter', 'day');

        $totalOrders = Order::where('branch_id', $branchId)->count();
        $todayOrders = Order::where('branch_id', $branchId)->whereDate('created_at', today())->count();
        $totalIncome = Order::where('branch_id', $branchId)->where('payment_status', 'settlement')->sum('total');
        $todayIncome = Order::where('branch_id', $branchId)->whereDate('created_at', today())->where('payment_status', 'settlement')->sum('total');

        $salesLabels = [];
        $salesValues = [];

        $startOfWeek = now()->startOfWeek();
        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $salesLabels[] = $date->translatedFormat('l');
            $salesValues[] = Order::where('branch_id', $branchId)
                ->whereDate('created_at', $date->format('Y-m-d'))
                ->where('payment_status', 'settlement')->sum('total');
        }

        $queryStatus = Order::where('branch_id', $branchId)->whereDate('created_at', today());

        $statusStats = [
            (clone $queryStatus)->where('status', 'completed')->count(),
            (clone $queryStatus)->where('status', 'processing')->count(),
            (clone $queryStatus)->where('status', 'pending')->count(),
            (clone $queryStatus)->where('status', 'ready')->count(),
        ];

        $topProducts = OrderItem::whereHas('order', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)->where('payment_status', 'settlement');
        })
            ->select('product_id', DB::raw('SUM(quantity) as total_qty'))
            ->with('product:id,name')
            ->groupBy('product_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('kasir.dashboard', compact(
            'totalOrders', 'todayOrders', 'totalIncome', 'todayIncome',
            'salesLabels', 'salesValues', 'statusStats', 'topProducts'
        ));
    }

    public function getChartData(Request $request)
    {
        $branchId = auth()->user()->branch_id;
        $type = $request->get('type');
        $filter = $request->get('filter', 'day');

        Carbon::setLocale('id');

        if ($type == 'sales') {
            $labels = [];
            $values = [];

            if ($filter == 'day') {
                $startOfWeek = now()->startOfWeek();
                for ($i = 0; $i < 7; $i++) {
                    $date = $startOfWeek->copy()->addDays($i);
                    $labels[] = $date->translatedFormat('l');
                    $values[] = Order::where('branch_id', $branchId)
                        ->whereDate('created_at', $date->format('Y-m-d'))
                        ->where('payment_status', 'settlement')->sum('total');
                }
            } elseif ($filter == 'month') {
                for ($i = 1; $i <= 12; $i++) {
                    $month = now()->month($i);
                    $labels[] = $month->translatedFormat('F');
                    $values[] = Order::where('branch_id', $branchId)
                        ->whereMonth('created_at', $i)
                        ->whereYear('created_at', now()->year)
                        ->where('payment_status', 'settlement')->sum('total');
                }
            } elseif ($filter == 'year') {
                $years = Order::where('branch_id', $branchId)
                    ->selectRaw('YEAR(created_at) as year')
                    ->distinct()
                    ->orderBy('year', 'asc')
                    ->pluck('year');

                foreach ($years as $year) {
                    $labels[] = $year;
                    $values[] = Order::where('branch_id', $branchId)
                        ->whereYear('created_at', $year)
                        ->where('payment_status', 'settlement')->sum('total');
                }
            }
            return response()->json(['labels' => $labels, 'values' => $values]);
        }

        if ($type == 'status') {
            $query = Order::where('branch_id', $branchId);

            if ($filter == 'day') $query->whereDate('created_at', today());
            elseif ($filter == 'month') $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
            elseif ($filter == 'year') $query->whereYear('created_at', now()->year);

            $stats = [
                (clone $query)->where('status', 'completed')->count(),
                (clone $query)->where('status', 'processing')->count(),
                (clone $query)->where('status', 'pending')->count(),
                (clone $query)->where('status', 'ready')->count(),
            ];
            return response()->json(['stats' => $stats]);
        }
    }
}
