@extends('layouts.admin')

@section('content')
<div class="space-y-8 pb-10">

    <div class="relative">
        <div class="absolute -top-10 -right-10 w-64 h-64 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Overview</p>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight">
                    {{ $selectedBranchName }} <span class="text-slate-300 font-light">Dashboard</span>
                </h1>
            </div>

            <form method="GET" action="{{ route('admin.dashboard') }}" class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100 relative z-10">
                <div class="pl-3">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </div>
                <select name="branch_id" onchange="this.form.submit()" class="border-none text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer bg-transparent py-2 pr-8">
                    <option value="">View All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        <div class="group bg-gradient-to-br from-emerald-500 to-teal-600 rounded-3xl p-6 text-white shadow-xl shadow-emerald-200 relative overflow-hidden transition-transform hover:-translate-y-1">
            <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path d="M2 10a8 8 0 018-8v8h8a8 8 0 11-16 0z"></path><path d="M12 2.252A8.014 8.014 0 0117.748 8H12V2.252z"></path></svg>
            </div>
            <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest">Sales Today</p>
            <h3 class="text-3xl font-black mt-2">₱{{ number_format($totalSalesToday, 2) }}</h3>
            <p class="text-emerald-100 text-xs mt-2 font-medium bg-white/20 inline-block px-2 py-1 rounded-lg">{{ $totalTransactions }} Transactions</p>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-lg transition-all relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-6 text-slate-100 group-hover:text-emerald-50 transition-colors">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd"></path></svg>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Revenue (This Month)</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">₱{{ number_format($totalSalesMonth, 2) }}</h3>
            <div class="mt-4 w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                <div class="bg-emerald-500 h-full rounded-full" style="width: 65%"></div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-lg transition-all relative overflow-hidden group">
             <div class="absolute right-0 top-0 p-6 text-slate-100 group-hover:text-blue-50 transition-colors">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10.496 2.132a1 1 0 00-.992 0l-7 4A1 1 0 003 8v7a1 1 0 100 2h14a1 1 0 100-2V8a1 1 0 00.504-1.868l-7-4zM6 9a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1zm3 1a1 1 0 012 0v3a1 1 0 11-2 0v-3zm5-1a1 1 0 00-1 1v3a1 1 0 102 0v-3a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Active Branches</p>
            <h3 class="text-3xl font-black text-slate-800 mt-2">{{ $totalBranches }}</h3>
            <a href="{{ route('branches.index') }}" class="text-blue-500 text-xs font-bold mt-2 inline-block hover:underline">Manage Locations &rarr;</a>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-lg transition-all relative overflow-hidden group">
            <div class="absolute right-0 top-0 p-6 text-slate-100 group-hover:text-red-50 transition-colors">
                 <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>
            </div>
            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Low Stock Alerts</p>
            <h3 class="text-3xl font-black {{ $lowStockItems->count() > 0 ? 'text-red-500' : 'text-slate-800' }} mt-2">{{ $lowStockItems->count() }}</h3>
            <p class="text-slate-400 text-xs mt-1">Items below threshold</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-2 space-y-8">

            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Revenue Trend</h3>
                        <p class="text-xs text-slate-400">Showing data for: <span class="font-bold text-emerald-600">{{ $selectedBranchName }}</span></p>
                    </div>
                    <span class="bg-slate-50 text-xs font-bold text-slate-500 rounded-lg py-1 px-3">Last 7 Days</span>
                </div>
                <div class="h-64 w-full">
                    <canvas id="adminChart"></canvas>
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-slate-800 text-lg">Recent Transactions</h3>
                        <p class="text-xs text-slate-400">Latest sales from <span class="font-bold text-emerald-600">{{ $selectedBranchName }}</span></p>
                    </div>
                    <a href="{{ route('reports.index') }}" class="text-emerald-600 text-xs font-bold hover:underline">View All Reports</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase">Branch</th>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase">Cashier</th>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase text-right">Amount</th>
                                <th class="p-4 text-xs font-bold text-slate-400 uppercase text-right">Time</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentSales as $sale)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="p-4">
                                    <span class="font-bold text-slate-700 text-sm">{{ $sale->branch->name ?? 'Unknown' }}</span>
                                </td>
                                <td class="p-4 text-sm text-slate-500">
                                    {{ $sale->user->name ?? 'System' }}
                                </td>
                                <td class="p-4 text-right font-mono font-bold text-emerald-600">
                                    ₱{{ number_format($sale->total_amount, 2) }}
                                </td>
                                <td class="p-4 text-right text-xs text-slate-400 font-bold">
                                    {{ $sale->created_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="p-6 text-center text-slate-400 text-sm">No sales found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-8">

            <div class="bg-slate-900 rounded-3xl p-6 text-white shadow-xl shadow-slate-300">
                <h3 class="font-bold text-lg mb-1">Leaderboard</h3>
                <p class="text-slate-400 text-xs mb-6">Top performing branches today</p>

                <div class="space-y-4">
                    @forelse($topBranches as $index => $branch)
                    <div class="flex items-center gap-4 group">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm
                            {{ $index == 0 ? 'bg-yellow-400 text-yellow-900' :
                              ($index == 1 ? 'bg-slate-300 text-slate-800' :
                              ($index == 2 ? 'bg-orange-400 text-orange-900' : 'bg-slate-800 text-slate-400')) }}">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-sm">{{ $branch->name }}</h4>
                            <div class="w-full bg-slate-800 h-1 rounded-full mt-1.5 overflow-hidden">
                                @php
                                    $maxSales = $topBranches->first()->today_sales;
                                    $percent = $maxSales > 0 ? ($branch->today_sales / $maxSales) * 100 : 0;
                                @endphp
                                <div class="bg-emerald-500 h-full rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>
                        <span class="font-mono text-sm font-bold text-emerald-400">₱{{ number_format($branch->today_sales, 0) }}</span>
                    </div>
                    @empty
                    <p class="text-slate-500 text-sm">No sales data yet.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6">
                <h3 class="font-bold text-slate-800 text-lg mb-4">Critical Stock</h3>
                <div class="space-y-3">
                    @forelse($lowStockItems as $item)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 border border-red-100">
                        <div>
                            <h4 class="font-bold text-slate-700 text-sm">{{ $item->name }}</h4>
                            <p class="text-[10px] text-slate-500 uppercase font-bold">{{ $item->branch->name }}</p>
                        </div>
                        <div class="text-center">
                            <span class="block text-xl font-black text-red-500 leading-none">{{ $item->quantity }}</span>
                            <span class="text-[9px] text-red-400 font-bold uppercase">Left</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-6">
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-600">Inventory Healthy</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('inventory.index') }}" class="block w-full text-center mt-4 text-xs font-bold text-slate-400 hover:text-slate-600 transition-colors">View Full Inventory</a>
            </div>

        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('adminChart').getContext('2d');

    // Gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)');
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Revenue',
                data: @json($chartData),
                borderColor: '#10b981',
                backgroundColor: gradient,
                borderWidth: 3,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#10b981',
                pointRadius: 4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { borderDash: [5, 5] },
                    ticks: { callback: function(value) { return '₱' + value; } }
                },
                x: { grid: { display: false } }
            }
        }
    });
</script>
@endsection
