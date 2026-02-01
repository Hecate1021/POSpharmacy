@extends('layouts.admin')

@section('content')
<div class="space-y-6">

  <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-gray-500 text-sm">Welcome back, {{ auth()->user()->name }}</p>
        </div>
        <div class="text-sm font-medium text-gray-500 bg-white px-4 py-2 rounded-lg shadow-sm border border-gray-100">
            {{ now()->format('l, F j, Y') }}
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase">Sales Today</p>
            <p class="text-3xl font-black text-emerald-600 mt-1">₱{{ number_format($stats['today_sales'], 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase">Monthly Revenue</p>
            <p class="text-3xl font-black text-slate-800 mt-1">₱{{ number_format($stats['month_sales'], 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm">
            <p class="text-xs font-bold text-slate-400 uppercase">Total Items</p>
            <p class="text-3xl font-black text-blue-600 mt-1">{{ number_format($stats['total_products']) }}</p>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-red-100 shadow-sm">
            <p class="text-xs font-bold text-red-400 uppercase">Low Stock</p>
            <p class="text-3xl font-black text-red-500 mt-1">{{ $stats['low_stock'] }}</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
        <div class="flex justify-between items-end mb-6">
            <div>
                <h3 class="font-bold text-lg text-slate-800">Sales Trend</h3>
                <p class="text-sm text-slate-500">Revenue performance over the last 7 days.</p>
            </div>
            @php
                $lastVal = end($chartData) ?: 1;
                $prevVal = prev($chartData) ?: 1;
                $percentChange = (($lastVal - $prevVal) / $prevVal) * 100;
            @endphp
            <div class="{{ $percentChange >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }} px-3 py-1 rounded-full text-xs font-bold">
                {{ $percentChange >= 0 ? '▲' : '▼' }} {{ number_format(abs($percentChange), 1) }}% vs Yesterday
            </div>
        </div>

        <div class="relative h-64 w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="{{ route('inventory.index') }}" class="group bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-400 hover:shadow-md transition-all flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-lg text-slate-800">Manage Inventory</h3>
                <p class="text-sm text-slate-500">Add stock, update prices, audit items.</p>
            </div>
        </a>
        <a href="{{ route('pos.terminal') }}" class="group bg-white p-6 rounded-2xl border border-slate-100 shadow-sm hover:border-emerald-400 hover:shadow-md transition-all flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
            </div>
            <div>
                <h3 class="font-bold text-lg text-slate-800">Open POS Terminal</h3>
                <p class="text-sm text-slate-500">Go to the Point of Sale screen.</p>
            </div>
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('salesChart').getContext('2d');

    // Gradient Fill for the Chart
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald-500 low opacity
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartLabels), // Dates passed from Controller
            datasets: [{
                label: 'Sales Revenue (₱)',
                data: @json($chartData), // Amounts passed from Controller
                borderColor: '#10b981', // Emerald-500
                backgroundColor: gradient,
                borderWidth: 3,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#10b981',
                pointBorderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4 // Smooth curves
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    titleFont: { size: 13 },
                    bodyFont: { size: 14, weight: 'bold' },
                    callbacks: {
                        label: function(context) {
                            return ' ₱ ' + context.parsed.y.toLocaleString('en-US', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: '#f1f5f9', borderDash: [5, 5] },
                    ticks: {
                        color: '#94a3b8',
                        font: { size: 11 },
                        callback: function(value) { return '₱' + value; }
                    },
                    border: { display: false }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11, weight: 'bold' } },
                    border: { display: false }
                }
            }
        }
    });
</script>
@endsection
