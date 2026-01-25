@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="dashboardApp()">

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
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Today</p>
                <h3 class="text-2xl font-black text-emerald-600 mt-1">₱{{ number_format($salesToday, 2) }}</h3>
                <p class="text-xs text-gray-400 mt-1">{{ $transactionsToday }} transactions</p>
            </div>
            <div class="bg-emerald-50 p-3 rounded-xl text-emerald-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">This Week</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">₱{{ number_format($salesThisWeek, 2) }}</h3>
                <p class="text-xs text-emerald-500 mt-1 font-bold">Mon - Sun</p>
            </div>
            <div class="bg-indigo-50 p-3 rounded-xl text-indigo-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">This Month</p>
                <h3 class="text-2xl font-black text-gray-800 mt-1">₱{{ number_format($salesThisMonth, 2) }}</h3>
                <p class="text-xs text-blue-500 mt-1 font-bold">Accumulated</p>
            </div>
            <div class="bg-blue-50 p-3 rounded-xl text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm border border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Low Stock</p>
                <h3 class="text-2xl font-black {{ $lowStockItems->count() > 0 ? 'text-red-500' : 'text-gray-800' }} mt-1">{{ $lowStockItems->count() }}</h3>
                <p class="text-xs text-gray-400 mt-1">Items Alert</p>
            </div>
            <div class="bg-red-50 p-3 rounded-xl text-red-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-gray-800">Sales Trend (Last 30 Days)</h3>
        </div>
        <div class="relative h-64 w-full">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Recent Transactions</h3>
                <span class="text-xs text-gray-400">Click row to view details</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 font-medium">
                        <tr>
                            <th class="px-5 py-3">Invoice</th>
                            <th class="px-5 py-3">Cashier</th>
                            <th class="px-5 py-3">Amount</th>
                            <th class="px-5 py-3">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($recentSales as $sale)
                        <tr @click="openModal({{ json_encode($sale) }})"
                            class="hover:bg-emerald-50 transition-colors cursor-pointer group">
                            <td class="px-5 py-3 font-medium text-emerald-600 group-hover:text-emerald-800">
                                {{ $sale->invoice_no }}
                            </td>
                            <td class="px-5 py-3 text-gray-500">{{ $sale->user->name }}</td>
                            <td class="px-5 py-3 font-bold text-gray-800">₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-5 py-3 text-gray-400">{{ $sale->created_at->diffForHumans() }}</td>
                        </tr>
                        @endforeach

                        @if($recentSales->isEmpty())
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">No sales today.</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 border-b border-gray-100">
                <h3 class="font-bold text-red-600">Critical Stock</h3>
            </div>
            <div class="p-0">
                @foreach($lowStockItems->take(5) as $item)
                <div class="flex justify-between items-center p-4 border-b border-gray-50 last:border-0 hover:bg-red-50/50 transition-colors">
                    <div>
                        <div class="font-bold text-gray-800 text-sm">{{ $item->name }}</div>
                        <div class="text-xs text-gray-400">Stock: <span class="text-red-600 font-bold">{{ $item->quantity }}</span></div>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="text-xs bg-white border border-gray-200 px-2 py-1 rounded text-gray-500 hover:text-emerald-600">Restock</a>
                </div>
                @endforeach
                @if($lowStockItems->isEmpty())
                <div class="p-8 text-center text-gray-400 text-sm">All stocks good!</div>
                @endif
            </div>
        </div>
    </div>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false" x-transition.opacity></div>

        <div class="bg-white w-full max-w-lg rounded-2xl shadow-2xl relative z-10 overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-bold text-gray-800" x-text="selectedSale?.invoice_no"></h3>
                    <p class="text-xs text-gray-500" x-text="formatDate(selectedSale?.created_at)"></p>
                </div>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-0 max-h-[60vh] overflow-y-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-gray-50 text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="px-6 py-2 font-medium">Item</th>
                            <th class="px-6 py-2 font-medium text-center">Qty</th>
                            <th class="px-6 py-2 font-medium text-right">Price</th>
                            <th class="px-6 py-2 font-medium text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="item in selectedSale?.items" :key="item.id">
                            <tr>
                                <td class="px-6 py-3 text-gray-800 font-medium" x-text="item.product.name"></td>
                                <td class="px-6 py-3 text-center text-gray-600" x-text="item.quantity"></td>
                                <td class="px-6 py-3 text-right text-gray-600" x-text="'₱' + Number(item.price).toFixed(2)"></td>
                                <td class="px-6 py-3 text-right font-bold text-gray-800" x-text="'₱' + (item.price * item.quantity).toFixed(2)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <div class="bg-gray-50 px-6 py-4 border-t border-gray-100 flex justify-between items-center">
                <span class="text-sm text-gray-500">Processed by: <span class="font-bold text-gray-700" x-text="selectedSale?.user?.name"></span></span>
                <div class="text-right">
                    <span class="block text-xs text-gray-500 uppercase font-bold">Total Paid</span>
                    <span class="text-2xl font-black text-emerald-600" x-text="'₱' + Number(selectedSale?.total_amount).toFixed(2)"></span>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Alpine Component for Modal Logic
    function dashboardApp() {
        return {
            showModal: false,
            selectedSale: null,

            openModal(sale) {
                this.selectedSale = sale;
                this.showModal = true;
            },

            formatDate(dateString) {
                if(!dateString) return '';
                const date = new Date(dateString);
                return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            }
        }
    }

    // Chart.js Logic
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('salesChart').getContext('2d');

        // Gradient for the chart line
        let gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald-500
        gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($labels), // Passed from Controller
                datasets: [{
                    label: 'Daily Sales (₱)',
                    data: @json($data), // Passed from Controller
                    borderColor: '#10b981', // Emerald-500
                    backgroundColor: gradient,
                    borderWidth: 2,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#10b981',
                    pointRadius: 4,
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
                        backgroundColor: '#064e3b',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [2, 4], color: '#e5e7eb' },
                        ticks: { callback: function(value) { return '₱' + value; } }
                    },
                    x: {
                        grid: { display: false }
                    }
                }
            }
        });
    });
</script>
@endsection
