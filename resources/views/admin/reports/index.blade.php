@extends('layouts.admin')

@section('content')
<div x-data="reportApp()" class="space-y-8 pb-20">

    <form method="GET" action="{{ route('reports.index') }}">
        <div class="relative mb-8">
            <div class="absolute -top-10 -right-10 w-64 h-64 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="flex flex-col md:flex-row justify-between items-end gap-4 relative z-10">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">Financial Analysis</p>
                    <h1 class="text-4xl font-black text-slate-800 tracking-tight">
                        {{ $selectedBranchName }} <span class="text-slate-300 font-light">Reports</span>
                    </h1>
                </div>

                <div class="flex gap-2">
                    @if(auth()->user()->role === 'admin')
                    <div class="bg-white p-1.5 rounded-2xl shadow-sm border border-slate-100 flex items-center">
                        <div class="pl-3 pr-2 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <select name="branch_id" onchange="this.form.submit()" class="border-none text-sm font-bold text-slate-700 focus:ring-0 cursor-pointer bg-transparent py-2 pr-8 rounded-xl hover:bg-slate-50 transition-colors">
                            <option value="">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $selectedBranchId == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
            <div class="lg:col-span-1 bg-white p-5 rounded-[2rem] border border-slate-100 shadow-sm h-full flex flex-col justify-center gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">From Date</label>
                    <input type="date" name="start_date" value="{{ $startDate->format('Y-m-d') }}" onchange="this.form.submit()" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2 font-bold text-slate-700 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-bold text-slate-400 uppercase ml-2">To Date</label>
                    <input type="date" name="end_date" value="{{ $endDate->format('Y-m-d') }}" onchange="this.form.submit()" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-2 font-bold text-slate-700 text-sm">
                </div>
            </div>

            <div class="lg:col-span-3 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-[2rem] p-6 text-white shadow-xl shadow-emerald-200 relative overflow-hidden group">
                    <p class="text-emerald-100 text-xs font-bold uppercase tracking-widest">Total Revenue</p>
                    <h3 class="text-3xl font-black mt-1">₱{{ number_format($totalRevenue, 2) }}</h3>
                </div>
                <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm relative">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Transactions</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">{{ $totalTransactions }}</h3>
                </div>
                <div class="bg-white rounded-[2rem] p-6 border border-slate-100 shadow-sm relative">
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Avg. Ticket</p>
                    <h3 class="text-3xl font-black text-slate-800 mt-1">₱{{ number_format($averageTicket, 2) }}</h3>
                </div>
            </div>
        </div>
    </form>

    <div class="bg-white rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-50/50 border-b border-slate-100">
                    <tr>
                        <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider pl-8">Order ID</th>
                        <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Date & Time</th>
                        <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Branch</th>
                        <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider">Cashier</th>
                        <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right">Total</th>
                        <th class="p-5 text-xs font-bold text-slate-400 uppercase tracking-wider text-right pr-8">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($sales as $sale)
                    <tr class="hover:bg-slate-50/80 transition-colors group">
                        <td class="p-5 pl-8">
                            <span class="font-mono font-bold text-slate-600 bg-slate-100 px-2 py-1 rounded-lg text-xs">#{{ $sale->id }}</span>
                        </td>
                        <td class="p-5">
                            <div class="font-bold text-slate-700 text-sm">{{ $sale->created_at->format('M d, Y') }}</div>
                            <div class="text-xs text-slate-400 font-bold">{{ $sale->created_at->format('h:i A') }}</div>
                        </td>
                        <td class="p-5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg border border-slate-100 bg-white text-xs font-bold text-slate-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> {{ $sale->branch->name ?? 'Unknown' }}
                            </span>
                        </td>
                        <td class="p-5">
                            <span class="text-sm font-bold text-slate-600">{{ $sale->user->name ?? 'System' }}</span>
                        </td>
                        <td class="p-5 text-right">
                            <span class="font-mono font-black text-emerald-600 text-lg">₱{{ number_format($sale->total_amount, 2) }}</span>
                        </td>
                        <td class="p-5 text-right pr-8">
                            <button @click="viewInvoice({{ $sale->id }})" class="bg-slate-900 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-emerald-600 transition-colors shadow-lg shadow-slate-200 hover:shadow-emerald-200">
                                View Invoice
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center text-slate-400">No transactions found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5 border-t border-slate-50 bg-white">{{ $sales->links() }}</div>
    </div>

    <div x-show="showInvoiceModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showInvoiceModal = false" x-transition.opacity></div>

        <div class="bg-white w-full max-w-lg rounded-3xl shadow-2xl relative z-10 overflow-hidden flex flex-col max-h-[90vh]"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-8 opacity-0" x-transition:enter-end="translate-y-0 opacity-100">

            <div class="bg-slate-50 px-8 py-6 border-b border-slate-100 flex justify-between items-center">
                <div>
                    <h3 class="text-xl font-black text-slate-800">Invoice Details</h3>
                    <p class="text-xs text-slate-500 font-mono mt-1" x-text="'Order #' + (invoice.id || 'Loading...')"></p>
                </div>
                <button @click="showInvoiceModal = false" class="text-slate-400 hover:text-slate-800">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <div class="p-8 overflow-y-auto flex-1 custom-scrollbar">

                <div class="flex justify-between items-start mb-6 pb-6 border-b border-dashed border-slate-200">
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase">Branch</p>
                        <p class="font-bold text-slate-700" x-text="invoice.branch ? invoice.branch.name : 'Unknown'"></p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-400 uppercase">Date</p>
                        <p class="font-bold text-slate-700" x-text="formatDate(invoice.created_at)"></p>
                    </div>
                </div>

                <table class="w-full text-left mb-6">
                    <thead>
                        <tr>
                            <th class="pb-2 text-xs font-bold text-slate-400 uppercase">Item</th>
                            <th class="pb-2 text-xs font-bold text-slate-400 uppercase text-center">Qty</th>
                            <th class="pb-2 text-xs font-bold text-slate-400 uppercase text-right">Price</th>
                            <th class="pb-2 text-xs font-bold text-slate-400 uppercase text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-sm">
                        <template x-for="item in invoice.items" :key="item.id">
                            <tr class="border-b border-slate-50">
                                <td class="py-3 font-bold text-slate-700" x-text="item.product ? item.product.name : 'Unknown Item'"></td>
                                <td class="py-3 text-center text-slate-500" x-text="item.quantity"></td>
                                <td class="py-3 text-right text-slate-500" x-text="'₱' + formatNumber(item.price)"></td>
                                <td class="py-3 text-right font-bold text-slate-800" x-text="'₱' + formatNumber(item.quantity * item.price)"></td>
                            </tr>
                        </template>
                    </tbody>
                </table>

                <div class="flex justify-between items-center pt-2">
                    <span class="text-lg font-bold text-slate-500">Total Amount</span>
                    <span class="text-3xl font-black text-emerald-600" x-text="'₱' + formatNumber(invoice.total_amount)"></span>
                </div>
            </div>

            <div class="p-6 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" @click="printInvoice()" class="px-6 py-3 rounded-xl font-bold bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Print
                </button>
                <button type="button" @click="showInvoiceModal = false" class="px-6 py-3 rounded-xl font-bold bg-slate-900 text-white hover:bg-emerald-600 transition-colors">
                    Close
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function reportApp() {
        return {
            showInvoiceModal: false,
            invoice: { items: [], total_amount: 0 },

            async viewInvoice(id) {
                // Reset and open modal immediately for responsiveness
                this.invoice = { items: [], total_amount: 0, id: '...' };
                this.showInvoiceModal = true;

                try {
                    let response = await fetch(`/admin/reports/${id}`);
                    this.invoice = await response.json();
                } catch (error) {
                    alert('Error loading invoice details.');
                    this.showInvoiceModal = false;
                }
            },

            printInvoice() {
                window.print(); // Simple browser print (can be enhanced with specific print styles)
            },

            formatNumber(num) {
                return Number(num).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            formatDate(dateString) {
                if(!dateString) return '';
                const options = { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' };
                return new Date(dateString).toLocaleDateString('en-US', options);
            }
        }
    }
</script>
@endsection
