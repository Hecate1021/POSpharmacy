<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pharma POS Terminal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="//unpkg.com/alpinejs" defer></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #cbd5e1; border-radius: 20px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background-color: #94a3b8; }
        .fade-in { animation: fadeIn 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .scale-in { animation: scaleIn 0.2s cubic-bezier(0.4, 0, 0.2, 1); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes scaleIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    </style>
</head>
<body class="bg-slate-100 h-screen w-screen overflow-hidden font-sans text-slate-700 select-none" x-data="posSystem()" x-cloak>

    <header class="bg-white h-16 shrink-0 flex items-center justify-between px-6 shadow-sm border-b border-slate-200 z-40 relative">
        <div class="flex items-center gap-4">
            <div class="bg-emerald-600 w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.384-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path></svg>
            </div>
            <div class="leading-tight">
                <h1 class="text-xl font-black tracking-tight text-slate-800">Pharmacy<span class="text-emerald-600">Pro</span></h1>
                <p class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Point of Sale</p>
            </div>
        </div>
        <div class="flex items-center gap-6">
            <div class="text-right hidden sm:block">
                <div class="text-sm font-bold text-slate-700">{{ auth()->user()->name ?? 'Cashier' }}</div>
                <div class="text-[10px] text-emerald-600 font-bold flex items-center justify-end gap-1.5 uppercase tracking-wide">
                    <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                    Online
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-10 h-10 rounded-full hover:bg-red-50 text-slate-400 hover:text-red-500 transition-all duration-200"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg></button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden h-[calc(100vh-64px)] w-full">
        <div class="flex-1 w-full lg:w-[65%] flex flex-col h-full bg-slate-100 relative z-0">
            <div class="px-6 py-4 bg-slate-100 z-10 shrink-0">
                <div class="relative group max-w-2xl mx-auto">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none"><svg class="h-5 w-5 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg></span>
                    <input type="text" x-model="search" placeholder="Search products..." class="w-full pl-12 pr-4 py-3.5 rounded-full border-0 bg-white ring-1 ring-slate-200 focus:ring-2 focus:ring-emerald-500 focus:outline-none transition-all shadow-sm text-slate-700 placeholder-slate-400 font-medium" autofocus>
                    <div class="absolute inset-y-0 right-3 flex items-center" x-show="search.length > 0"><button @click="search = ''" class="text-slate-400 hover:text-red-500"><svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button></div>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto px-6 pb-24 lg:pb-6 custom-scrollbar">
                <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <button @click="openQtyModal(product)" class="bg-white p-4 rounded-2xl border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-emerald-100/50 hover:border-emerald-500/30 hover:-translate-y-1 active:scale-95 transition-all duration-200 text-left flex flex-col justify-between group h-[11rem] relative overflow-hidden">
                            <div class="absolute top-3 right-3 z-10">
                                <span class="text-[9px] font-bold uppercase tracking-wide px-2 py-0.5 rounded-full border shadow-sm backdrop-blur-sm" :class="product.quantity < 10 ? 'bg-red-50 text-red-600 border-red-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'"><span x-text="product.quantity"></span> Left</span>
                            </div>
                            <div class="z-10 relative mt-4 pr-1">
                                <h3 class="font-extrabold text-slate-800 leading-tight text-lg line-clamp-2" x-text="product.name"></h3>
                            </div>
                            <div class="flex items-end justify-between mt-2 z-10 relative">
                                <span class="block font-black text-slate-800 text-xl tracking-tight" x-text="'₱' + Number(product.price).toFixed(2)"></span>
                                <div class="w-10 h-10 rounded-xl bg-slate-50 text-emerald-600 border border-slate-100 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors shadow-sm"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg></div>
                            </div>
                            <div class="absolute -bottom-6 -right-6 w-20 h-20 bg-emerald-50 rounded-full group-hover:scale-150 transition-transform duration-500 ease-out z-0"></div>
                        </button>
                    </template>
                </div>
                <div x-show="filteredProducts.length === 0" class="flex flex-col items-center justify-center h-full min-h-[300px] text-slate-400 fade-in">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-sm mb-4"><svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg></div>
                    <p class="text-lg font-bold text-slate-500">No products found</p>
                </div>
            </div>
        </div>

        <div class="hidden lg:flex lg:w-[35%] bg-white flex-col h-full shadow-[0_0_40px_-10px_rgba(0,0,0,0.1)] z-20 relative border-l border-slate-200">
            <div class="p-5 border-b border-dashed border-slate-200 flex justify-between items-center shrink-0 bg-white/80 backdrop-blur-md z-10">
                <div>
                    <h2 class="font-black text-xl text-slate-800 flex items-center gap-2">Current Order <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2 py-0.5 rounded-full" x-text="cart.length"></span></h2>
                    <p class="text-xs text-slate-400 font-medium mt-0.5">Order #{{ rand(1000,9999) }}</p>
                </div>
                <button @click="cart = []" x-show="cart.length > 0" class="text-xs font-bold text-red-500 hover:text-red-600 hover:bg-red-50 px-3 py-1.5 rounded-lg transition-colors">Clear All</button>
            </div>
            <div class="flex-1 overflow-y-auto p-5 space-y-3 custom-scrollbar bg-slate-50/30">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="bg-white rounded-xl p-4 border border-slate-100 shadow-sm flex flex-col gap-3 group fade-in hover:border-emerald-300 transition-all relative overflow-hidden">
                        <div class="flex justify-between items-start z-10">
                            <div class="flex-1 pr-2">
                                <h4 class="font-bold text-slate-700 text-sm leading-tight" x-text="item.name"></h4>
                                <div class="text-xs text-slate-400 mt-1 flex items-center gap-2">
                                    <span x-text="'@ ₱' + Number(item.price).toFixed(2)"></span>
                                    <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                                    <span x-text="'Sub: ₱' + (item.price * item.qty).toFixed(2)"></span>
                                </div>
                            </div>
                            <span class="font-black text-slate-800 text-lg" x-text="'₱' + (item.price * item.qty).toFixed(2)"></span>
                        </div>
                        <div class="flex items-center justify-between pt-2 z-10">
                            <div class="flex items-center bg-slate-100/80 rounded-lg p-1 border border-slate-200">
                                <button @click="updateQty(index, -1)" class="w-8 h-8 flex items-center justify-center text-slate-500 hover:bg-white hover:text-red-500 rounded-md transition-all font-bold">-</button>
                                <span class="w-10 text-center font-bold text-slate-700 text-sm" x-text="item.qty"></span>
                                <button @click="updateQty(index, 1)" class="w-8 h-8 flex items-center justify-center text-emerald-600 hover:bg-white hover:shadow-sm rounded-md transition-all font-bold">+</button>
                            </div>
                            <button @click="removeFromCart(index)" class="group/del flex items-center gap-1 text-xs font-bold text-slate-400 hover:text-red-500 transition-colors px-2 py-1 rounded-md hover:bg-red-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg><span class="group-hover/del:inline hidden">Remove</span></button>
                        </div>
                    </div>
                </template>
                <div x-show="cart.length === 0" class="h-full flex flex-col items-center justify-center text-slate-400 gap-4 mt-10">
                    <img src="https://cdn-icons-png.flaticon.com/512/2038/2038854.png" class="w-24 opacity-20 grayscale" alt="Empty">
                    <div class="text-center"><p class="text-base font-bold text-slate-500">Cart is empty</p><p class="text-xs text-slate-400 mt-1 px-10">Select products from the left to start a transaction.</p></div>
                </div>
            </div>
            <div class="p-6 bg-white border-t border-slate-200 shrink-0 shadow-[0_-10px_30px_rgba(0,0,0,0.03)] z-30">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm text-slate-500"><span>Subtotal</span><span class="font-bold" x-text="'₱' + cartTotal.toFixed(2)"></span></div>
                    <div class="flex justify-between text-sm text-slate-500"><span>VAT (12%)</span><span class="font-bold" x-text="'₱' + (cartTotal * 0.12).toFixed(2)"></span></div>
                    <div class="flex justify-between items-end mt-4 pt-4 border-t border-dashed border-slate-200"><span class="text-slate-800 font-bold text-lg">Total</span><span class="text-4xl font-black text-slate-900 tracking-tighter" x-text="'₱' + grandTotal.toFixed(2)"></span></div>
                </div>
                <button @click="openPaymentModal()" :disabled="cart.length === 0" class="w-full bg-slate-900 hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 text-white font-bold py-4 rounded-xl shadow-lg disabled:shadow-none active:translate-y-0.5 transition-all flex justify-center items-center gap-3 text-lg tracking-wide group">
                    <span class="flex items-center gap-2">PAY NOW <span class="bg-white/20 px-2 py-0.5 rounded text-sm group-hover:bg-white/30 transition-colors" x-text="'₱' + grandTotal.toFixed(2)"></span></span>
                </button>
            </div>
        </div>
    </div>

    <div x-show="cart.length > 0 && !mobileCartOpen" class="fixed bottom-4 left-4 right-4 lg:hidden z-50 fade-in">
        <button @click="mobileCartOpen = true" class="w-full bg-slate-900 text-white rounded-xl shadow-2xl py-4 px-6 flex justify-between items-center transform active:scale-95 transition-transform border border-slate-800">
            <div class="flex items-center gap-3"><span class="bg-emerald-500 text-white text-xs font-bold w-6 h-6 flex items-center justify-center rounded-full" x-text="cart.length"></span><span class="font-bold">View Cart</span></div>
            <span class="font-bold text-xl" x-text="'₱' + grandTotal.toFixed(2)"></span>
        </button>
    </div>

    <div class="lg:hidden fixed inset-0 z-50 flex flex-col transition-all duration-300" x-show="mobileCartOpen" style="display: none;">
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="mobileCartOpen = false" x-transition.opacity></div>
        <div class="absolute bottom-0 left-0 right-0 bg-white rounded-t-3xl shadow-2xl h-[85vh] flex flex-col transform transition-transform" x-transition:enter="translate-y-full" x-transition:enter-end="translate-y-0" x-transition:leave="translate-y-0" x-transition:leave-end="translate-y-full">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center shrink-0">
                <h2 class="font-bold text-xl text-slate-800">Your Cart</h2>
                <button @click="mobileCartOpen = false" class="bg-slate-100 p-2 rounded-full text-slate-500 hover:bg-slate-200"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-slate-50">
                <template x-for="(item, index) in cart" :key="item.id">
                    <div class="bg-white rounded-xl p-4 flex justify-between items-center border border-slate-200 shadow-sm">
                        <div><h4 class="font-bold text-slate-800" x-text="item.name"></h4><div class="text-emerald-600 font-bold" x-text="'₱' + (item.price * item.qty).toFixed(2)"></div></div>
                        <div class="flex items-center bg-slate-100 rounded-lg p-1"><button @click="updateQty(index, -1)" class="w-8 h-8 font-bold text-slate-500">-</button><span class="w-8 text-center text-slate-800 font-bold" x-text="item.qty"></span><button @click="updateQty(index, 1)" class="w-8 h-8 font-bold text-emerald-600">+</button></div>
                    </div>
                </template>
            </div>
            <div class="p-5 border-t border-slate-200 bg-white pb-8">
                 <button @click="mobileCartOpen = false; openPaymentModal()" class="w-full bg-emerald-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-200 text-lg flex justify-between px-6"><span>Proceed to Pay</span><span x-text="'₱' + grandTotal.toFixed(2)"></span></button>
            </div>
        </div>
    </div>

    <div x-show="showQtyModal" class="fixed inset-0 z-[60] flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" x-transition.opacity @click="closeQtyModal"></div>
        <div class="bg-white w-full max-w-md rounded-3xl overflow-hidden shadow-2xl transform transition-all scale-in relative z-10">
            <div class="bg-slate-50 p-6 text-center border-b border-slate-100">
                <h3 class="font-black text-2xl text-slate-800 leading-tight" x-text="selectedProduct?.name"></h3>
                <div class="inline-flex items-center gap-2 mt-2 bg-white px-3 py-1 rounded-full border border-slate-200 shadow-sm"><span class="w-2 h-2 rounded-full bg-emerald-500"></span><p class="text-slate-500 text-xs font-bold uppercase tracking-wide">Stock: <span x-text="selectedProduct?.quantity" class="text-emerald-600"></span></p></div>
            </div>
            <div class="p-8">
                <div class="flex justify-center items-center gap-6">
                    <button @click="inputQty > 1 ? inputQty-- : null" class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center transition-colors shadow-sm active:scale-95"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path></svg></button>
                    <div class="relative"><input type="number" x-model="inputQty" x-ref="qtyInput" @keydown.enter="confirmAddToCart" class="w-32 text-center text-5xl font-black border-0 focus:ring-0 text-slate-800 p-0 bg-transparent" placeholder="1"><span class="block text-center text-xs font-bold text-slate-400 mt-1">QUANTITY</span></div>
                    <button @click="inputQty < (selectedProduct?.quantity || 999) ? inputQty++ : null" class="w-14 h-14 rounded-2xl bg-emerald-100 text-emerald-600 hover:bg-emerald-200 flex items-center justify-center transition-colors shadow-sm active:scale-95"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg></button>
                </div>
                <p x-show="qtyError" class="text-red-500 text-center mt-4 text-sm font-bold animate-bounce" x-text="qtyError"></p>
            </div>
            <div class="p-4 flex gap-3 bg-white border-t border-slate-100">
                <button @click="closeQtyModal" class="flex-1 py-3.5 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button @click="confirmAddToCart" class="flex-1 py-3.5 rounded-xl bg-emerald-600 text-white font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-200 active:translate-y-0.5 transition-all">Add to Cart</button>
            </div>
        </div>
    </div>

    <div x-show="showPaymentModal" class="fixed inset-0 z-[70] flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-md transition-opacity" x-transition.opacity @click="showPaymentModal = false"></div>
        <div class="bg-white w-full max-w-sm rounded-3xl overflow-hidden shadow-2xl transform transition-all scale-in relative z-10">

            <div class="bg-slate-50 p-6 text-center border-b border-slate-200">
                <h3 class="font-bold text-slate-500 text-sm uppercase tracking-widest mb-1">Total Amount Due</h3>
                <div class="text-4xl font-black text-slate-800 tracking-tight" x-text="'₱' + grandTotal.toFixed(2)"></div>
            </div>

            <div class="p-8 space-y-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wide mb-2 text-center">Cash Received</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-2xl font-bold text-slate-400">₱</span>
                        <input type="number" x-model="cashReceived" x-ref="cashInput" @keydown.enter="submitSale"
                            class="w-full bg-slate-100 border-2 border-slate-200 focus:border-emerald-500 focus:ring-0 rounded-2xl py-4 pl-10 pr-4 text-center text-3xl font-black text-slate-800 transition-all placeholder-slate-300"
                            placeholder="0.00">
                    </div>
                </div>

                <div class="bg-emerald-50 rounded-2xl p-4 text-center border border-emerald-100 transition-colors"
                     :class="change < 0 ? 'bg-red-50 border-red-100' : 'bg-emerald-50 border-emerald-100'">
                    <span class="block text-xs font-bold uppercase tracking-wide mb-1"
                          :class="change < 0 ? 'text-red-400' : 'text-emerald-600'">Change Due</span>
                    <span class="text-3xl font-black tracking-tight"
                          :class="change < 0 ? 'text-red-500' : 'text-emerald-600'"
                          x-text="change < 0 ? 'Insufficient' : '₱' + change.toFixed(2)"></span>
                </div>
            </div>

            <div class="p-4 flex gap-3 bg-white border-t border-slate-100">
                <button @click="showPaymentModal = false" class="flex-1 py-4 rounded-xl border border-slate-300 font-bold text-slate-600 hover:bg-slate-50 transition-colors">Cancel</button>
                <button
                    @click="submitSale"
                    :disabled="change < 0 || cashReceived === '' || processing"
                    class="flex-1 py-4 rounded-xl bg-slate-900 text-white font-bold hover:bg-emerald-600 disabled:bg-slate-200 disabled:text-slate-400 shadow-lg disabled:shadow-none transition-all flex justify-center items-center gap-2">
                    <span x-show="!processing">COMPLETE SALE</span>
                    <svg x-show="processing" class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <script>
        function posSystem() {
            return {
                search: '',
                products: @json($products ?? []),
                cart: [],
                processing: false,
                mobileCartOpen: false,

                // Qty Modal Logic
                showQtyModal: false,
                selectedProduct: null,
                inputQty: 1,
                qtyError: '',

                // Payment Modal Logic
                showPaymentModal: false,
                cashReceived: '',

                // Computed Properties
                get filteredProducts() {
                    if (this.search === '') return this.products;
                    const s = this.search.toLowerCase();
                    return this.products.filter(p => p.name.toLowerCase().includes(s));
                },
                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },
                get grandTotal() {
                    return this.cartTotal * 1.12; // VAT
                },
                get change() {
                    const cash = parseFloat(this.cashReceived);
                    if (isNaN(cash)) return -this.grandTotal;
                    return cash - this.grandTotal;
                },

                // Functions
                openQtyModal(product) {
                    if (product.quantity <= 0) return alert('Out of Stock');
                    this.selectedProduct = product;
                    this.inputQty = 1;
                    this.qtyError = '';
                    this.showQtyModal = true;
                    setTimeout(() => { if(this.$refs.qtyInput) this.$refs.qtyInput.select(); }, 100);
                },
                closeQtyModal() { this.showQtyModal = false; this.selectedProduct = null; },
                confirmAddToCart() {
                    const qty = parseInt(this.inputQty);
                    const product = this.selectedProduct;
                    if (!qty || qty <= 0) { this.qtyError = 'Invalid Quantity'; return; }

                    const existing = this.cart.find(i => i.id === product.id);
                    const currentQty = existing ? existing.qty : 0;

                    if (currentQty + qty > product.quantity) {
                        this.qtyError = 'Only ' + product.quantity + ' in stock!';
                        return;
                    }

                    if (existing) { existing.qty += qty; }
                    else { this.cart.push({ id: product.id, name: product.name, price: product.price, qty: qty, max: product.quantity }); }
                    this.closeQtyModal();
                },
                updateQty(index, change) {
                    const item = this.cart[index];
                    const newQty = item.qty + change;
                    if (newQty > 0 && newQty <= item.max) item.qty = newQty;
                },
                removeFromCart(index) {
                    this.cart.splice(index, 1);
                    if (this.cart.length === 0) this.mobileCartOpen = false;
                },

                // New Payment Functions
                openPaymentModal() {
                    if (this.cart.length === 0) return;
                    this.cashReceived = ''; // Reset cash input
                    this.showPaymentModal = true;
                    // Auto focus the input field
                    setTimeout(() => { if(this.$refs.cashInput) this.$refs.cashInput.focus(); }, 100);
                },

                async submitSale() {
                    if (this.change < 0 || this.cashReceived === '') {
                        return; // Prevent submit if insufficient funds
                    }

                    if (!confirm('Finalize Sale?')) return;

                    this.processing = true;
                    try {
                        const res = await fetch('{{ route("pos.process") }}', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                            body: JSON.stringify({
                                cart: this.cart,
                                cash_received: this.cashReceived,
                                change_amount: this.change,
                                total_amount: this.grandTotal
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            alert('Transaction Successful! Change: ₱' + this.change.toFixed(2));
                            window.location.reload();
                        } else {
                            alert('Error: ' + data.message);
                        }
                    } catch (err) { alert('Connection Error'); }
                    finally { this.processing = false; }
                }
            }
        }
    </script>
</body>
</html>
