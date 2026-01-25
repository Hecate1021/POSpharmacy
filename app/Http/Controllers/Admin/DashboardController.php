<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB; // Don't forget this import!

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Financial Stats
        $salesToday = Sale::whereDate('created_at', $today)->sum('total_amount');
        $transactionsToday = Sale::whereDate('created_at', $today)->count();
        $salesThisMonth = Sale::whereMonth('created_at', Carbon::now()->month)->sum('total_amount');

        // ADDED: Weekly Sales (For your "Day, Week, Month" request)
        $salesThisWeek = Sale::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_amount');

        // 2. Chart Data (Last 30 Days)
        // This groups sales by date to create the graph points
        $salesData = Sale::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(total_amount) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date', 'asc')
        ->get();

        // Prepare arrays for Chart.js
        $labels = $salesData->pluck('date')->map(function($date) {
            return Carbon::parse($date)->format('M d'); // e.g., "Oct 25"
        });
        $data = $salesData->pluck('total');

        // 3. Inventory Stats
        $totalProducts = Product::count();
        $lowStockItems = Product::where('quantity', '<=', 20)->get();

        // 4. Recent Activity
        $recentSales = Sale::with(['user', 'items.product'])
                    ->latest()
                    ->take(5)
                    ->get();

        return view('admin.dashboard', compact(
            'salesToday',
            'transactionsToday',
            'salesThisMonth',
            'salesThisWeek',
            'totalProducts',
            'lowStockItems',
            'recentSales',
            'labels',
            'data'
        ));
    }
}
