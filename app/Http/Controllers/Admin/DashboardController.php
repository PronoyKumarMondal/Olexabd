<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Filter: Date Range (Default to current month)
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // 1. Key Metrics (Filtered by Range)
        $totalProducts = Product::count();
        
        $totalOrders = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();
                            
        $totalRevenue = Order::where('payment_status', 'paid')
                             ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                             ->sum('total_amount');
                             
        $totalCustomers = User::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])->count();

        // 2. Sales Chart Data (Daily breakdown)
        $dailySales = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as day"), 
                DB::raw('sum(total_amount) as revenue')
            )
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->where('payment_status', 'paid')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->pluck('revenue', 'day')
            ->toArray();

        // Prepare chart labels (All days in range)
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        $chartLabels = [];
        $chartData = [];
        
        foreach ($period as $date) {
            $dayStr = $date->format('Y-m-d');
            $chartLabels[] = $date->format('d M'); 
            $chartData[] = $dailySales[$dayStr] ?? 0;
        }

        // 3. Order Status Data
        $orderStatusStats = Order::select('status', DB::raw('count(*) as total'))
                                ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
                                ->groupBy('status')
                                ->get();
                                
        $statusLabels = $orderStatusStats->pluck('status')->map(fn($s) => ucfirst($s));
        $statusData = $orderStatusStats->pluck('total');

        // 4. Recent Orders & Low Stock
        $recentOrders = Order::with('user')->latest()->take(5)->get();
        $lowStockProducts = Product::where('stock', '<', 10)->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalOrders', 
            'totalRevenue', 
            'totalCustomers',
            'recentOrders',
            'lowStockProducts',
            'startDate',
            'endDate',
            'chartLabels',
            'chartData',
            'statusLabels',
            'statusData'
        ));
    }
    public function serveStorage(Request $request, $path)
    {
        // Sanitize path to prevent directory traversal
        if (str_contains($path, '..')) {
            abort(403);
        }

        $fullPath = storage_path('app/public/' . $path);

        if (!file_exists($fullPath)) {
            abort(404);
        }

        return response()->file($fullPath);
    }
}
