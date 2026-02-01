<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Branch;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // ==========================================
        // 1. DETERMINE SCOPE (Admin vs Branch)
        // ==========================================
        if ($user->role === 'admin') {
            // ADMIN: Can see all, can select specific branch
            $branches = Branch::all();
            $selectedBranchId = $request->branch_id;
            $selectedBranchName = $selectedBranchId
                ? (Branch::find($selectedBranchId)->name ?? 'Unknown')
                : 'All Branches';
        } else {
            // BRANCH MANAGER: Locked to their own branch
            $branches = collect([]); // Empty list (Dropdown hidden in view)
            $selectedBranchId = $user->branch_id;
            $selectedBranchName = $user->branch->name ?? 'My Branch';
        }

        // ==========================================
        // 2. BUILD BASE QUERIES
        // ==========================================

        // Sales Query
        $salesQuery = Sale::query();

        // If a specific ID is set (either by Admin Filter or Manager Role)
        if ($selectedBranchId) {
            $salesQuery->whereHas('user', function($q) use ($selectedBranchId) {
                $q->where('branch_id', $selectedBranchId);
            });
        }

        // Product/Stock Query
        $productQuery = Product::with('branch')->whereRaw('quantity <= low_stock_threshold');

        if ($selectedBranchId) {
            $productQuery->where('branch_id', $selectedBranchId);
        }

        // ==========================================
        // 3. CALCULATE METRICS
        // ==========================================

        $totalSalesToday = (clone $salesQuery)->whereDate('created_at', Carbon::today())->sum('total_amount');
        $totalSalesMonth = (clone $salesQuery)->whereMonth('created_at', Carbon::now()->month)->sum('total_amount');
        $totalTransactions = (clone $salesQuery)->whereDate('created_at', Carbon::today())->count();
        $totalBranches = Branch::count(); // Only really useful for Admin view

        // Chart Data (Last 7 Days)
        $chartLabels = [];
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $chartLabels[] = $date->format('M d');
            $dailySales = (clone $salesQuery)->whereDate('created_at', $date->format('Y-m-d'))->sum('total_amount');
            $chartData[] = $dailySales;
        }

        // Recent Transactions
        $recentSales = (clone $salesQuery)->with(['user', 'branch'])->latest()->take(6)->get();

        // Low Stock Items
        $lowStockItems = $productQuery->take(5)->get();

        // ==========================================
        // 4. RETURN VIEW BASED ON ROLE
        // ==========================================

        if ($user->role === 'admin') {
            // Admin needs the Leaderboard
            $topBranches = Branch::with(['sales' => function($query) {
                $query->whereDate('sales.created_at', Carbon::today());
            }])->get()->map(function($branch) {
                $branch->today_sales = $branch->sales->sum('total_amount');
                return $branch;
            })->sortByDesc('today_sales')->take(5);

            return view('admin.dashboard', compact(
                'branches', 'selectedBranchId', 'selectedBranchName',
                'totalSalesToday', 'totalSalesMonth', 'totalTransactions', 'totalBranches',
                'chartLabels', 'chartData', 'topBranches', 'recentSales', 'lowStockItems'
            ));
        }

        else {
            // Branch Manager View (Variable names mapped to what branch.dashboard expects)
            $branchName = $selectedBranchName; // View expects $branchName

            return view('branch.dashboard', compact(
                'branchName',
                'totalSalesToday', 'totalSalesMonth', 'totalTransactions',
                'chartLabels', 'chartData', 'recentSales', 'lowStockItems'
            ));
        }
    }
}
