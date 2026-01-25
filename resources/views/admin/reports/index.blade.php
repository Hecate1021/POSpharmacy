@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="reportApp()">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Sales Report</h1>
            <p class="text-sm text-gray-500">Review your financial performance</p>
        </div>

        <form method="GET" action="{{ route('reports.index') }}" class="flex flex-col sm:flex-row gap-2 bg-white p-2 rounded-xl shadow-sm border border-gray-200">
            <div class="flex items-center gap-2 px-2">
                <span class="text-xs font-bold text-gray-500 uppercase">From</span>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="border-none p-0 text-sm font-semibold text-gray-800 focus:ring-0 cursor-pointer">
            </div>
            <div class="w-px bg-gray-200 hidden sm:block"></div>
            <div class="flex items-center gap-2 px-2">
                <span class="text-xs font-bold text-gray-500 uppercase">To</span>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="border-none p-0 text-sm font-semibold text-gray-800 focus:ring-0 cursor-pointer">
            </div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-bold transition-colors">
                Filter
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-6 text-white shadow-lg shadow-emerald-200">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-wider">Total Revenue</p>
                    <h3 class="text-3xl font-black mt-1">₱{{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
            </div>
            <p class="text-emerald-100 text-sm mt-4 opacity-80">Period: {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - {{ \Carbon\Carbon::parse($endDate)->format('M d') }}</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Transactions</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1">{{ $totalTransactions }}</h3>
                </div>
                <div class="bg-blue-50 p-2 rounded-lg text-blue-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-4">Total invoices generated</p>
        </div>

        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Avg. Ticket</p>
                    <h3 class="text-3xl font-black text-gray-800 mt-1">₱{{ number_format($averageTicket, 2) }}</h3>
                </div>
                <div class="bg-orange-50 p-2 rounded-lg text-orange-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
            </div>
            <p class="text-gray-400 text-sm mt-4">Average value per sale</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Transaction Details</h3>
            <button onclick="window.print()" class="text-sm flex items-center gap-1 text-gray-500 hover:text-gray-800 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                Print Report
            </button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 text-gray-500 font-bold border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Invoice #</th>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Cashier</th>
                        <th class="px-6 py-4 text-right">Total Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($sales as $sale)
                    <tr @click="openModal({{ json_encode($sale) }})"
                        class="hover:bg-emerald-50 transition-colors cursor-pointer group">
                        <td class="px-6 py-4 font-mono font-medium text-emerald-600 group-hover:text-emerald-800">{{ $sale->invoice_no }}</td>
                        <td class="px-6 py-4 text-gray-600">
                            <span class="block font-bold text-gray-800">{{ $sale->created_at->format('M d, Y') }}</span>
                            <span class="text-xs">{{ $sale->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-gray-100 text-xs font-medium text-gray-600">
                                {{ $sale->user->name }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right font-black text-emerald-600">
                            ₱{{ number_format($sale->total_amount, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-gray-400">
                            No transactions found for this period.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if($sales->isNotEmpty())
                <tfoot class="bg-gray-50 border-t border-gray-200">
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-600 uppercase">Total Period Revenue</td>
                        <td class="px-6 py-4 text-right font-black text-2xl text-emerald-700">₱{{ number_format($totalRevenue, 2) }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
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
                                <td class="px-6 py-3 text-gray-800 font-medium" x-text="item.product?.name || 'Deleted Item'"></td>
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

<script>
    function reportApp() {
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
</script>
@endsection
