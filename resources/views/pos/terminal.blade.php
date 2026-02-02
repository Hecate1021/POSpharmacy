<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>PharmaCo POS</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .slide-in-left { animation: slideInLeft 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes slideInLeft { from { transform: translateX(-100%); } to { transform: translateX(0); } }

        /* PRINT STYLES */
        @media print {
            @page { margin: 0; size: auto; }
            html, body { margin: 0 !important; padding: 0 !important; width: 100%; background-color: #fff; }
            body > *:not(#receipt-print) { display: none !important; }
            #receipt-print { display: block !important; width: 58mm; margin: 0; padding: 0 2mm 5mm 2mm; font-family: monospace; font-size: 10px; color: black; }
            .print-flex { display: flex; justify-content: space-between; }
            .print-center { text-align: center; }
            .print-line { border-bottom: 1px dashed #000; margin: 5px 0; }
            .print-bold { font-weight: bold; }
        }
    </style>
</head>
<body class="bg-slate-100 h-screen w-screen overflow-hidden font-sans text-slate-700 select-none" x-data="posSystem()" x-cloak>

    <div id="receipt-print" class="hidden">
        <div class="print-center" style="margin-bottom: 5px; padding-top: 5px;">
            <h1 class="print-bold" style="font-size: 14px; margin: 0;">PharmaCo</h1>
            <h2 class="print-bold" style="font-size: 10px; margin: 0;">{{ auth()->user()->branch->name ?? 'Pharmacy' }}</h2>
            <p style="font-size: 9px; margin: 0;">{{ auth()->user()->branch->address ?? 'Main Branch' }}</p>
        </div>

        <div class="print-line"></div>
        <div class="print-flex"><span>Date:</span> <span x-text="receiptData.date"></span></div>
        <div class="print-flex"><span>Inv #:</span> <span x-text="receiptData.invoice_no"></span></div>
        <div class="print-flex"><span>Staff:</span> <span>{{ auth()->user()->name }}</span></div>
        <div class="print-line"></div>

        <table style="width: 100%; border-collapse: collapse; font-size: 10px;">
            <thead>
                <tr>
                    <th style="text-align: left; width: 50%;">Item</th>
                    <th style="text-align: center; width: 15%;">Qty</th>
                    <th style="text-align: right; width: 35%;">Amt</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in receiptData.items" :key="item.id || item.product_id">
                    <tr>
                        <td style="padding: 2px 0;" x-text="item.name || (item.product ? item.product.name : 'Unknown')"></td>
                        <td style="padding: 2px 0; text-align: center;" x-text="item.qty || item.quantity"></td>
                        <td style="padding: 2px 0; text-align: right;" x-text="formatMoney((item.price) * (item.qty || item.quantity))"></td>
                    </tr>
                </template>
            </tbody>
        </table>

        <div class="print-line"></div>

        <div class="print-flex">
            <span>Subtotal:</span>
            <span x-text="formatMoney(receiptData.subtotal)"></span>
        </div>

        <template x-if="receiptData.discount_amount > 0">
            <div class="print-flex">
                <span>Disc (<span x-text="(receiptData.discount_rate * 100) + '%'"></span>):</span>
                <span x-text="'-' + formatMoney(receiptData.discount_amount)"></span>
            </div>
        </template>

        <div class="print-flex print-bold" style="font-size: 12px; margin-top: 5px;">
            <span>TOTAL:</span>
            <span x-text="formatMoney(receiptData.total)"></span>
        </div>

        <div class="print-flex"><span>Cash:</span> <span x-text="formatMoney(receiptData.cash)"></span></div>
        <div class="print-flex"><span>Change:</span> <span x-text="formatMoney(receiptData.change)"></span></div>
        <div class="print-line"></div>

        <div class="print-center" style="margin-top: 5px;">
            <p class="print-bold">Thank You!</p>
            <p style="font-size: 8px;">"Your Health, Our Priority"</p>
        </div>
    </div>
    <header class="bg-white h-16 shrink-0 flex items-center justify-between px-6 shadow-sm border-b border-slate-200 z-40 relative print:hidden">
        <div class="flex items-center gap-4">
            <button @click="openHistory()" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </button>
            <div class="flex items-center gap-3">
                <div class="bg-emerald-600 w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-800 leading-none">Pharma<span class="text-emerald-600">Co</span></h1>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">POS Terminal</span>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-bold text-slate-700">{{ auth()->user()->name ?? 'Cashier' }}</div>

                <div x-show="isOnline" class="text-xs text-emerald-600 font-bold flex items-center justify-end gap-1.5 uppercase tracking-wide">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Online
                </div>

                <div x-show="!isOnline" class="text-xs text-amber-600 font-bold flex items-center justify-end gap-1.5 uppercase tracking-wide" style="display: none;">
                    <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    Offline
                </div>

                <div x-show="offlineQueue.length > 0" class="text-[10px] text-slate-400 mt-0.5 font-bold transition-all" style="display: none;">
                    <span class="text-amber-600" x-text="offlineQueue.length"></span> Unsynced Txns
                    <svg class="w-3 h-3 inline-block ml-1 animate-spin text-amber-600" fill="none" viewBox="0 0 24 24" x-show="isOnline"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-10 h-10 rounded-full bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-600 transition-all duration-200 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden h-[calc(100vh-64px)] w-full print:hidden">

        <div class="flex-1 w-full lg:w-[65%] flex flex-col h-full bg-slate-100 relative z-0">
            <div class="px-6 py-4 bg-slate-100 z-10 shrink-0">
    <div class="relative group max-w-2xl mx-auto">
        <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
        </span>

        <input type="text"
               x-model="search"
               x-ref="searchInput"
               @keydown.enter.prevent="checkBarcode"
               placeholder="Scan Barcode or Search Products..."
               class="w-full pl-12 pr-4 py-3.5 rounded-full border-0 bg-white ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all shadow-sm text-slate-700 font-medium"
               autofocus>
    </div>
</div>

            <div class="flex-1 overflow-y-auto px-6 pb-24 lg:pb-6 custom-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <button @click="openQtyModal(product)" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:-translate-y-1 active:scale-95 transition-all text-left flex flex-col justify-between group h-[11rem] relative overflow-hidden">
                            <div class="absolute top-3 right-3 z-10">
                                <span class="text-[9px] font-bold uppercase px-2 py-0.5 rounded-full border" :class="product.quantity < 10 ? 'bg-red-50 text-red-600 border-red-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'"><span x-text="product.quantity"></span> Left</span>
                            </div>
                            <div class="z-10 relative mt-4">
                                <h3 class="font-extrabold text-slate-800 leading-tight text-lg line-clamp-2" x-text="product.name"></h3>
                            </div>
                            <div class="flex items-end justify-between mt-2 z-10 relative">
                                <span class="block font-black text-slate-800 text-xl" x-text="formatMoney(product.price)"></span>
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></div>
                            </div>
                        </button>
                    </template>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex lg:w-[35%] bg-white flex-col h-full shadow-[0_0_40px_-10px_rgba(0,0,0,0.1)] z-20 relative border-l border-slate-200">
            <div class="p-5 border-b border-dashed border-slate-200 flex justify-between items-center shrink-0 bg-white z-10">
                <div><h2 class="font-black text-xl text-slate-800">Current Order <span class="bg-emerald-100 text-emerald-700 text-xs px-2 py-0.5 rounded-full" x-text="cart.length"></span></h2></div>
                <button @click="cart = []" x-show="cart.length > 0" class="text-xs font-bold text-red-500 hover:text-red-600">Clear All</button>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-3 custom-scrollbar bg-slate-50/30">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex flex-col gap-3 group relative overflow-hidden">
                        <div class="flex justify-between items-start z-10">
                            <div class="flex-1 pr-2">
                                <h4 class="font-bold text-slate-700 text-sm leading-tight" x-text="item.name"></h4>
                                <div class="text-xs text-slate-400 mt-1" x-text="formatMoney(item.price) + ' x ' + item.qty"></div>
                            </div>
                            <span class="font-black text-slate-800 text-lg" x-text="formatMoney(item.price * item.qty)"></span>
                        </div>
                        <div class="flex items-center justify-between pt-2 z-10">
                            <div class="flex items-center bg-slate-100 rounded-lg p-1">
                                <button @click="updateQty(index, -1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-white rounded-md font-bold">-</button>
                                <span class="w-10 text-center font-bold text-slate-700 text-sm" x-text="item.qty"></span>
                                <button @click="updateQty(index, 1)" class="w-8 h-8 flex items-center justify-center text-emerald-600 hover:bg-white rounded-md font-bold">+</button>
                            </div>
                            <button @click="removeFromCart(index)" class="text-xs font-bold text-red-400 hover:text-red-500">Remove</button>
                        </div>
                    </div>
                </template>
            </div>

            <div class="p-6 bg-white border-t border-slate-200 shrink-0 shadow-[0_-10px_30px_rgba(0,0,0,0.03)] z-30">

                <div class="mb-4 bg-slate-50 p-3 rounded-xl border border-slate-100">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <div class="relative">
                            <input type="checkbox" x-model="applyDiscount" class="peer sr-only">
                            <div class="w-10 h-6 bg-slate-300 rounded-full peer peer-checked:bg-emerald-500 peer-checked:after:translate-x-full after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all"></div>
                        </div>
                        <span class="text-sm font-bold text-slate-700">Senior / PWD Discount</span>
                    </label>

                    <div x-show="applyDiscount" class="mt-3" x-transition>
                        <select x-model="discountRate" class="w-full bg-white border border-slate-300 rounded-lg px-3 py-2 text-sm font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                            <option value="0.10">10% Discount</option>
                            <option value="0.15">15% Discount</option>
                            <option value="0.20">20% Discount</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-slate-500 text-xs font-bold">
                        <span>Subtotal</span>
                        <span x-text="formatMoney(cartSubtotal)"></span>
                    </div>

                    <div class="flex justify-between text-emerald-600 text-xs font-bold" x-show="applyDiscount">
                        <span>Discount (<span x-text="(discountRate*100)+'%'"></span>)</span>
                        <span x-text="'-' + formatMoney(discountAmount)"></span>
                    </div>

                    <div class="flex justify-between items-end mt-4 pt-4 border-t border-dashed border-slate-200">
                        <span class="text-slate-800 font-bold text-lg">Total Payable</span>
                        <span class="text-4xl font-black text-slate-900 tracking-tighter" x-text="formatMoney(cartTotal)"></span>
                    </div>
                </div>

                <button @click="openPaymentModal()" :disabled="cart.length === 0" class="w-full bg-slate-900 hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold py-4 rounded-xl shadow-lg disabled:shadow-none active:translate-y-0.5 transition-all flex justify-center items-center gap-3 text-lg tracking-wide">
                    PAY NOW
                </button>
            </div>
        </div>
    </div>

    <div x-show="showStartCashModal" class="fixed inset-0 z-[90] flex items-center justify-center p-4 print:hidden" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/90 backdrop-blur-md"></div>
        <div class="bg-white w-full max-w-sm rounded-3xl p-8 text-center shadow-2xl relative z-10">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h2 class="text-2xl font-black text-slate-800 mb-2">Opening Register</h2>
            <p class="text-slate-500 text-sm mb-6 font-medium">Please enter the starting cash amount for today to begin transactions.</p>

            <div class="relative mb-6">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xl font-bold text-slate-400">₱</span>
                <input type="number" x-model="startingCashInput" class="w-full text-center text-3xl font-black border-2 border-slate-200 rounded-xl py-4 pl-8 focus:border-emerald-500 focus:ring-0 text-slate-800" placeholder="0.00">
            </div>

            <button @click="setStartingCash()" :disabled="!startingCashInput" class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl shadow-lg hover:bg-blue-700 disabled:bg-slate-200 disabled:text-slate-400 transition-all">Open Register</button>
        </div>
    </div>

    <div x-show="showHistoryPanel" class="fixed inset-0 z-[80] flex print:hidden" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" x-transition.opacity @click="showHistoryPanel = false"></div>
        <div class="relative w-full max-w-md bg-white h-full shadow-2xl flex flex-col slide-in-left">
            <div class="p-5 border-b border-slate-100 flex flex-col bg-slate-50">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-black text-xl text-slate-800">History</h2>
                    <button @click="showHistoryPanel = false" class="bg-white p-2 rounded-lg border border-slate-200 text-slate-400 hover:text-red-500 shadow-sm"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <input type="date" x-model="historyDate" @change="openHistory()" class="w-full pl-4 pr-4 py-2.5 rounded-xl border border-slate-200 bg-white text-sm font-bold text-slate-700 shadow-sm focus:ring-2 focus:ring-emerald-500 outline-none">
            </div>

            <div class="grid grid-cols-2 gap-3 p-4 bg-slate-100/50 border-b border-slate-100">
                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Starting Cash</p>
                    <p class="text-lg font-black text-slate-700" x-text="formatMoney(historyStats.starting_cash)"></p>
                </div>
                <div class="bg-white p-3 rounded-xl border border-slate-200 shadow-sm">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-wide">Total Sales</p>
                    <p class="text-lg font-black text-emerald-600" x-text="formatMoney(historyStats.total_sales)"></p>
                </div>
                <div class="col-span-2 bg-slate-800 p-4 rounded-xl shadow-lg">
                    <div class="flex justify-between items-center">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Total Cash On Hand</p>
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <p class="text-3xl font-black text-white mt-1" x-text="formatMoney(historyStats.cash_on_hand)"></p>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-3 custom-scrollbar bg-slate-50/50">
                <template x-if="loadingHistory">
                    <div class="flex justify-center items-center h-40"><svg class="animate-spin h-8 w-8 text-emerald-500" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg></div>
                </template>

                <template x-for="txn in transactions" :key="txn.id">
                    <div class="bg-white p-4 rounded-xl border border-slate-100 shadow-sm mb-2 hover:border-emerald-200 transition-colors group">
                        <div class="flex justify-between items-start mb-1">
                            <div>
                                <span class="font-mono font-bold text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded" x-text="txn.invoice_no"></span>
                                <div class="text-[10px] font-bold text-slate-400 mt-1" x-text="new Date(txn.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})"></div>
                            </div>
                            <span class="font-black text-emerald-600 text-lg" x-text="formatMoney(txn.total_amount)"></span>
                        </div>

                        <div x-show="txn.discount_amount > 0" class="flex justify-between items-center mt-1 bg-red-50 px-2 py-1 rounded border border-red-100">
                            <span class="text-[10px] font-bold text-red-400 uppercase">Discount Applied</span>
                            <span class="text-xs font-bold text-red-500" x-text="'-' + formatMoney(txn.discount_amount)"></span>
                        </div>

                        <div class="flex justify-between items-center border-t border-slate-50 pt-2 mt-2">
                            <span class="text-xs text-slate-500 font-medium" x-text="(txn.items ? txn.items.length : 0) + ' Items'"></span>
                            <button @click="reprintTransaction(txn)" class="text-xs font-bold text-slate-500 hover:text-emerald-600 flex items-center gap-1 bg-slate-50 hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition-colors">Reprint</button>
                        </div>
                    </div>
                </template>

                <template x-if="!loadingHistory && transactions.length === 0">
                    <div class="text-center py-10 text-slate-400 font-bold text-sm">No transactions found</div>
                </template>
            </div>
        </div>
    </div>

    <div x-show="showQtyModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4 print:hidden" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" x-transition.opacity @click="closeQtyModal"></div>
        <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl transform transition-all scale-in relative z-10">
            <div class="bg-slate-50 p-6 text-center border-b border-slate-100">
                <h3 class="font-black text-2xl text-slate-800 leading-tight" x-text="selectedProduct?.name"></h3>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wide">Stock: <span x-text="selectedProduct?.quantity" class="text-emerald-600"></span></p>
            </div>
            <div class="p-8">
                <div class="flex justify-center items-center gap-6">
                    <button @click="inputQty > 1 ? inputQty-- : null" class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors shadow-sm active:scale-95 text-2xl font-bold">-</button>
                    <div class="relative"><input type="number" x-model="inputQty" x-ref="qtyInput" @keydown.enter="confirmAddToCart" class="w-32 text-center text-5xl font-black border-0 focus:ring-0 text-slate-800 p-0 bg-transparent" placeholder="1"></div>
                    <button @click="inputQty < (selectedProduct?.quantity || 999) ? inputQty++ : null" class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 hover:bg-emerald-200 flex items-center justify-center transition-colors shadow-sm active:scale-95 text-2xl font-bold">+</button>
                </div>
            </div>
            <div class="p-4 flex gap-3 bg-white border-t border-slate-100">
                <button @click="closeQtyModal" class="flex-1 py-3.5 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button @click="confirmAddToCart" class="flex-1 py-3.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-200 active:translate-y-0.5 transition-all">Add to Cart</button>
            </div>
        </div>
    </div>

    <div x-show="showPaymentModal" class="fixed inset-0 z-[70] flex items-center justify-center p-4 print:hidden" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" x-transition.opacity @click="showPaymentModal = false"></div>
        <div class="bg-white w-full max-w-sm rounded-3xl overflow-hidden shadow-2xl transform transition-all scale-in relative z-10">
            <div class="bg-slate-50 p-6 text-center border-b border-slate-200">
                <h3 class="font-bold text-slate-500 text-sm uppercase tracking-widest mb-1">Total Amount Due</h3>
                <div class="text-4xl font-black text-slate-800 tracking-tight" x-text="formatMoney(cartTotal)"></div>
            </div>
            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2 text-center">Cash Received</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-slate-400">₱</span>
                        <input type="number" x-model="cashReceived" x-ref="cashInput" @keydown.enter="submitSale" class="w-full bg-slate-100 border-2 border-slate-200 focus:border-emerald-500 focus:ring-0 rounded-2xl py-4 pl-10 pr-4 text-center text-3xl font-black text-slate-800 transition-all placeholder-slate-300" placeholder="0.00">
                    </div>
                </div>
                <div class="bg-emerald-50 rounded-2xl p-4 text-center border border-emerald-100 transition-colors" :class="change < 0 ? 'bg-red-50 border-red-100' : 'bg-emerald-50 border-emerald-100'">
                    <span class="block text-xs font-bold uppercase tracking-wide mb-1" :class="change < 0 ? 'text-red-400' : 'text-emerald-600'">Change Due</span>
                    <span class="text-3xl font-black tracking-tight" :class="change < 0 ? 'text-red-500' : 'text-emerald-600'" x-text="change < 0 ? 'Insufficient' : formatMoney(change)"></span>
                </div>
            </div>
            <div class="p-4 flex gap-3 bg-white border-t border-slate-100">
                <button @click="showPaymentModal = false" class="flex-1 py-4 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button @click="submitSale()" :disabled="change < 0 || !cashReceived || processing" class="flex-1 py-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 shadow-lg disabled:shadow-none transition-all flex justify-center items-center gap-2">
                    <span x-show="!processing">COMPLETE SALE</span>
                    <svg x-show="processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
        </div>
    </div>

<script>
        function posSystem() {
            return {
                products: @json($products ?? []),
                registerOpen: @json($register_open ?? false),
                startingCash: @json($starting_cash ?? 0),

                cart: [],
                search: '',
                cashReceived: '',
                processing: false,
                isOnline: navigator.onLine,

                // --- Offline & Sync Logic ---
                offlineQueue: [],
                syncing: false, // Prevents double-sync crash

                // --- Discount & History State ---
                applyDiscount: false,
                discountRate: '0.20',
                transactions: [],
                historyDate: new Date().toLocaleDateString('en-CA'),
                showHistoryPanel: false,
                loadingHistory: false,
                historyStats: { starting_cash: 0, total_sales: 0, cash_on_hand: 0 },

                // --- Modals ---
                showPaymentModal: false,
                showQtyModal: false,
                showStartCashModal: false,
                startingCashInput: '',
                selectedProduct: null,
                inputQty: 1,
                receiptData: {},

                init() {
                    // Load saved offline transactions
                    const savedQ = localStorage.getItem('pos_queue');
                    if (savedQ) this.offlineQueue = JSON.parse(savedQ);

                    // Listen for internet connection changes
                    window.addEventListener('online', () => {
                        this.isOnline = true;
                        setTimeout(() => this.sync(), 1000); // Wait 1s before syncing
                    });
                    window.addEventListener('offline', () => { this.isOnline = false; });

                    // Check for register status
                    if (!this.registerOpen) {
                        this.showStartCashModal = true;
                    }

                    // Auto-sync periodically if online and have items
                    setInterval(() => {
                        if(this.isOnline && this.offlineQueue.length > 0 && !this.syncing) {
                            this.sync();
                        }
                    }, 5000);
                },

                // --- Computed Properties ---
                get filteredProducts() {
    if (!this.search) return this.products;

    const s = this.search.toLowerCase();

    return this.products.filter(p => {
        // Search by Name
        const nameMatch = p.name.toLowerCase().includes(s);

        // Search by Barcode (checks if barcode exists and matches)
        const barcodeMatch = p.barcode && p.barcode.toLowerCase().includes(s);

        return nameMatch || barcodeMatch;
    });
},
                get cartSubtotal() { return this.cart.reduce((a, b) => a + (b.price * b.qty), 0); },
                get discountAmount() { return this.applyDiscount ? (this.cartSubtotal * parseFloat(this.discountRate)) : 0; },
                get cartTotal() { return this.cartSubtotal - this.discountAmount; },
                get change() { return (parseFloat(this.cashReceived) || 0) - this.cartTotal; },

                formatMoney(amount) { return '₱' + (parseFloat(amount) || 0).toFixed(2); },

                // --- Register ---
                async setStartingCash() {
                    try {
                        let res = await fetch('{{ route("pos.open_register") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({ amount: this.startingCashInput })
                        });
                        if (res.ok) {
                            this.startingCash = parseFloat(this.startingCashInput);
                            this.registerOpen = true;
                            this.showStartCashModal = false;
                        }
                    } catch(e) { alert('Connection error.'); }
                },

                // --- Cart Actions ---
                openQtyModal(product) {
                    if(product.quantity <= 0) return alert('Out of stock!');
                    this.selectedProduct = product;
                    this.inputQty = 1;
                    this.showQtyModal = true;
                    setTimeout(() => { if(this.$refs.qtyInput) this.$refs.qtyInput.select(); }, 100);
                },
                closeQtyModal() { this.showQtyModal = false; this.selectedProduct = null; },
                confirmAddToCart() {
                    if (!this.selectedProduct) return;
                    const qty = parseInt(this.inputQty);
                    if (!qty || qty <= 0) return;
                    const existing = this.cart.find(i => i.id === this.selectedProduct.id);
                    if ((existing ? existing.qty : 0) + qty > this.selectedProduct.quantity) return alert('Stock limit reached');
                    if (existing) existing.qty += qty;
                    else this.cart.push({ ...this.selectedProduct, qty: qty });
                    this.closeQtyModal();
                },
                updateQty(index, change) {
                    const item = this.cart[index];
                    if (item.qty + change > 0) item.qty += change;
                    else this.cart.splice(index, 1);
                },
                removeFromCart(index) { this.cart.splice(index, 1); },
                openPaymentModal() {
                    if (this.cart.length === 0) return;
                    this.cashReceived = '';
                    this.showPaymentModal = true;
                    setTimeout(() => this.$refs.cashInput.focus(), 100);
                },

                // --- CORE SALES LOGIC ---
                async submitSale() {
                    if(this.change < 0) return;
                    this.processing = true;

                    const payload = {
                        cart: JSON.parse(JSON.stringify(this.cart)),
                        discount_rate: this.applyDiscount ? parseFloat(this.discountRate) : 0,
                        cash_received: parseFloat(this.cashReceived),
                        change_amount: this.change
                    };

                    // If Offline, save locally immediately
                    if (!this.isOnline) {
                        this.saveOffline(payload);
                        return;
                    }

                    try {
                        const res = await fetch('{{ route("pos.process") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify(payload)
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.finalize(data.sale);
                        } else {
                            alert(data.message);
                            this.processing = false;
                        }
                    } catch (err) {
                        // If server fails (500 error), save offline
                        this.saveOffline(payload);
                    }
                },

                saveOffline(payload) {
                    // Create offline invoice data
                    payload.invoice_no = 'OFF-' + Date.now().toString().slice(-6);
                    payload.items = payload.cart;
                    payload.subtotal = payload.cart.reduce((a, b) => a + (b.price * b.qty), 0);
                    payload.discount_amount = payload.subtotal * payload.discount_rate;
                    payload.total_amount = payload.subtotal - payload.discount_amount;
                    payload.amount_paid = payload.cash_received;
                    payload.change = payload.change_amount;
                    payload.created_at = new Date().toISOString();
                    payload.is_offline = true;

                    // Add to Queue
                    this.offlineQueue.push(payload);
                    localStorage.setItem('pos_queue', JSON.stringify(this.offlineQueue));

                    this.finalize(payload);
                },

                finalize(sale) {
                    this.processing = false;
                    this.showPaymentModal = false;

                    // Deduct stock visually
                    sale.items.forEach(cItem => {
                        // Find the product in the list
                        const p = this.products.find(x => x.id === (cItem.product_id || cItem.id));

                        if(p) {
                            // THE FIX: Check 'qty' (cart/offline) FIRST, then 'quantity' (server/online)
                            // This prevents using the copied stock amount by mistake.
                            const soldAmount = cItem.qty || cItem.quantity;
                            p.quantity -= parseInt(soldAmount);
                        }
                    });

                    this.cart = [];
                    this.applyDiscount = false;

                    // Prepare Receipt Data
                    this.receiptData = {
                        invoice_no: sale.invoice_no,
                        date: new Date().toLocaleString(),
                        items: sale.items,
                        subtotal: sale.subtotal,
                        discount_rate: sale.discount_rate,
                        discount_amount: sale.discount_amount,
                        total: sale.total_amount,
                        cash: sale.amount_paid,
                        change: sale.change
                    };

                    this.$nextTick(() => window.print());
                },

                // --- ROBUST SYNC FUNCTION (Fixes Crash) ---
                async sync() {
                    // 1. Safety Checks
                    if (this.offlineQueue.length === 0 || this.syncing) return;

                    this.syncing = true; // Lock the function
                    const item = this.offlineQueue[0];

                    try {
                        const res = await fetch('{{ route("pos.process") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(item)
                        });

                        // 2. Success or "Invalid Data" (Remove from queue)
                        if (res.ok || res.status === 422) {

                            // Remove first item safely
                            let newQueue = [...this.offlineQueue];
                            newQueue.shift();
                            this.offlineQueue = newQueue;

                            localStorage.setItem('pos_queue', JSON.stringify(this.offlineQueue));

                            // Sync next item quickly
                            if (this.offlineQueue.length > 0) {
                                setTimeout(() => {
                                    this.syncing = false;
                                    this.sync();
                                }, 500);
                                return; // Stop here, let the timeout call sync again
                            }
                        }
                    } catch (e) {
                        console.log("Sync failed, retrying later...");
                    }

                    this.syncing = false; // Unlock
                },

                async openHistory() {
                    this.showHistoryPanel = true;
                    this.loadingHistory = true;
                    this.transactions = [];

                    const offline = this.offlineQueue.filter(x => x.created_at.startsWith(this.historyDate));

                    try {
                        const res = await fetch('{{ route("pos.history") }}' + '?date=' + this.historyDate);
                        const data = await res.json();
                        this.transactions = [...offline, ...data.transactions];
                        this.historyStats = data.summary;
                    } catch(e) {
                        this.transactions = offline;
                    } finally {
                        this.loadingHistory = false;
                    }
                },

                reprintTransaction(txn) {
                    this.receiptData = {
                        invoice_no: txn.invoice_no || txn.temp_id,
                        date: new Date(txn.created_at).toLocaleString(),
                        items: txn.items || [],
                        subtotal: txn.subtotal || txn.total_amount,
                        discount_rate: txn.discount_rate || 0,
                        discount_amount: txn.discount_amount || 0,
                        total: txn.total_amount,
                        cash: txn.amount_paid,
                        change: txn.change
                    };
                    this.$nextTick(() => window.print());
                }
            }
        }

    </script>
</body>
</html>
