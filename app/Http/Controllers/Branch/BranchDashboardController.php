<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;

class BranchDashboardController extends Controller
{
    public function index()
{
    // 1. Stats
    $stats = [
        'total_products' => Product::count(),
        'low_stock'      => Product::where('quantity', '<', 10)->count(),
        'today_sales'    => Sale::whereDate('created_at', now())->sum('total_amount'),
        'month_sales'    => Sale::whereMonth('created_at', now()->month)->sum('total_amount'),
    ];

    // 2. THIS IS THE MISSING PART CAUSING YOUR ERROR
    $chartLabels = [];
    $chartData = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = now()->subDays($i);
        $chartLabels[] = $date->format('D d');
        $chartData[] = Sale::whereDate('created_at', $date->format('Y-m-d'))->sum('total_amount');
    }

    // 3. Recent Sales
    $recentSales = Sale::with('user')->latest()->take(5)->get();

    // MAKE SURE 'chartData' IS IN THIS LIST
    return view('branch.dashboard', compact('stats', 'recentSales', 'chartLabels', 'chartData'));
}
}
