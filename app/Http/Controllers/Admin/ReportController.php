<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Sale;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // Default to "This Month" if no dates selected
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->endOfMonth()->format('Y-m-d'));

        // Query Sales within date range
       $sales = Sale::with(['user', 'items.product']) // Make sure 'items.product' is here
             ->whereDate('created_at', '>=', $startDate)
             ->whereDate('created_at', '<=', $endDate)
             ->latest()
             ->get();

        // Calculate Summaries
        $totalRevenue = $sales->sum('total_amount');
        $totalTransactions = $sales->count();
        $averageTicket = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        return view('admin.reports.index', compact(
            'sales',
            'startDate',
            'endDate',
            'totalRevenue',
            'totalTransactions',
            'averageTicket'
        ));
    }
}
