<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use App\Models\Branch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with(['user', 'branch']);

        // 1. Branch Filtering Logic
        $selectedBranchId = null;
        $selectedBranchName = 'All Branches';

        if (Auth::user()->role === 'admin') {
            if ($request->has('branch_id') && $request->branch_id != '') {
                $selectedBranchId = $request->branch_id;
                $selectedBranchName = Branch::find($selectedBranchId)->name;

                $query->whereHas('user', function($q) use ($selectedBranchId) {
                    $q->where('branch_id', $selectedBranchId);
                });
            }
        } else {
            // Branch Manager is locked to their own branch
            $query->whereHas('user', function($q) {
                $q->where('branch_id', Auth::user()->branch_id);
            });
            $selectedBranchName = Auth::user()->branch->name;
        }

        // 2. Date Filtering
        $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay();
        $endDate   = $request->end_date   ? Carbon::parse($request->end_date)->endOfDay()   : Carbon::today()->endOfDay();

        $query->whereBetween('created_at', [$startDate, $endDate]);

        // 3. Summaries
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalTransactions = (clone $query)->count();
        $averageTicket = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // 4. Data
        $sales = $query->latest()->paginate(15)->withQueryString();
        $branches = Branch::all();

        return view('admin.reports.index', compact(
            'sales', 'branches', 'totalRevenue', 'totalTransactions', 'averageTicket',
            'startDate', 'endDate', 'selectedBranchId', 'selectedBranchName'
        ));
    }

    public function show(Sale $sale)
{
    // Eager load the items and the product details for each item
    // Note: Ensure your Sale model has "public function items() { return $this->hasMany(SaleItem::class); }"
    $sale->load(['user', 'branch', 'items.product']);

    return response()->json($sale);
}
}
