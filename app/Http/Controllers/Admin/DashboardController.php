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
        // Filter: Default to current month if not provided
        $filterMonth = $request->input('month', now()->format('Y-m'));
        [$year, $month] = explode('-', $filterMonth);

        // 1. Key Metrics (Filtered by Month)
        $totalProducts = Product::count(); // Products usually aren't filtered by date in this context (inventory is current)
        
        $totalOrders = Order::whereYear('created_at', $year)
                            ->whereMonth('created_at', $month)
                            ->count();
                            
        $totalRevenue = Order::where('payment_status', 'paid')
                             ->whereYear('created_at', $year)
                             ->whereMonth('created_at', $month)
                             ->sum('total_amount');
                             
        $totalCustomers = User::whereYear('created_at', $year)
                              ->whereMonth('created_at', $month)
                              ->count();

        // 2. Sales Chart Data (Daily breakdown for the selected month)
        // Group by Day
        $dailySales = Order::select(
                DB::raw("DATE_FORMAT(created_at, '%d') as day"), 
                DB::raw('sum(total_amount) as revenue')
            )
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->where('payment_status', 'paid')
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%d')"))
            ->orderBy(DB::raw("DATE_FORMAT(created_at, '%d')"))
            ->pluck('revenue', 'day')
            ->toArray();

        // Prepare chart labels (Days of month) and data (Revenue)
        $daysInMonth = Carbon::createFromDate($year, $month)->daysInMonth;
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 1; $i <= $daysInMonth; $i++) {
            $dayStr = str_pad($i, 2, '0', STR_PAD_LEFT);
            $chartLabels[] = $dayStr;
            $chartData[] = $dailySales[$dayStr] ?? 0;
        }

        // 3. Order Status Data (for Pie Chart)
        $orderStatusStats = Order::select('status', DB::raw('count(*) as total'))
                                ->whereYear('created_at', $year)
                                ->whereMonth('created_at', $month)
                                ->groupBy('status')
                                ->get();
                                
        $statusLabels = $orderStatusStats->pluck('status')->map(fn($s) => ucfirst($s));
        $statusData = $orderStatusStats->pluck('total');

        // 4. Recent Orders & Low Stock
        $recentOrders = Order::with('user')->latest()->take(3)->get();
        $lowStockProducts = Product::where('stock', '<', 10)->take(3)->get();

        return view('admin.dashboard', compact(
            'totalProducts', 
            'totalOrders', 
            'totalRevenue', 
            'totalCustomers',
            'recentOrders',
            'lowStockProducts',
            'filterMonth',
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
