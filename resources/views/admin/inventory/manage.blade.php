@extends('layouts.admin')

@section('content')
<div x-data="{
    showModal: false,
    showStockModal: false,
    editMode: false,
    form: { id: null, barcode: '', name: '', category: 'Fast Moving', price: '', quantity: '', low_stock_threshold: 10 },
    stockForm: { id: null, name: '', current_qty: 0, quantity_to_add: '' }
}" class="pb-20">

    <div class="relative mb-6">
        <div class="absolute -top-10 -left-10 w-64 h-64 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative flex flex-col md:flex-row justify-between items-end gap-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-3">
                    <a href="{{ route('inventory.index') }}" class="hover:text-emerald-500 transition-colors flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Branches
                    </a>
                    <span class="text-slate-300">/</span>
                    <span class="text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded text-[10px]">{{ $branch->name }}</span>
                </div>
                <h1 class="text-4xl font-black text-slate-800 tracking-tight">{{ $branch->name }} <span class="text-slate-300 font-light">Stocks</span></h1>
                <p class="text-slate-500 mt-2 font-medium">Manage stock levels, pricing, and product alerts.</p>
            </div>

            <button @click="showModal = true; editMode = false; form = { id: null, barcode: '', name: '', category: 'Fast Moving', price: '', quantity: '', low_stock_threshold: 10 }"
                class="group relative bg-slate-900 hover:bg-emerald-600 text-white px-7 py-4 rounded-2xl font-bold shadow-xl shadow-slate-200 hover:shadow-emerald-500/30 transition-all duration-300 flex items-center gap-3 overflow-hidden transform hover:-translate-y-1 shrink-0">
                <div class="absolute inset-0 bg-white/10 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Add Product</span>
            </button>
        </div>
    </div>

    <div class="mb-8 relative z-10">
        <form method="GET" action="{{ route('inventory.branch', $branch->id) }}" class="relative w-full group">
            <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                <svg class="w-6 h-6 text-slate-400 group-focus-within:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <input type="text"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search product name or scan barcode..."
                   @input.debounce.500ms="$el.form.submit()"
                   class="w-full pl-14 pr-6 py-5 bg-white border border-slate-200 rounded-2xl font-bold text-lg text-slate-700 shadow-sm focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all placeholder:font-medium placeholder:text-slate-400">

            <div class="absolute inset-y-0 right-0 pr-5 flex items-center pointer-events-none opacity-0 group-focus-within:opacity-100 transition-opacity">
                <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">Press Enter</span>
            </div>
        </form>
    </div>

    <div class="w-full">
        <div class="grid grid-cols-12 gap-4 px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">
            <div class="col-span-2">Barcode</div>
            <div class="col-span-3">Product Name</div>
            <div class="col-span-2">Category</div>
            <div class="col-span-2 text-right">Price</div>
            <div class="col-span-2 text-center">Stock Level</div>
            <div class="col-span-1 text-right">Action</div>
        </div>

        <div class="space-y-3">
            @forelse($products as $product)
            <div class="group relative bg-white rounded-2xl p-5 border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-slate-200/50 hover:border-emerald-500/30 transition-all duration-300 grid grid-cols-12 gap-4 items-center">

                <div class="col-span-2">
                    @if($product->barcode)
                        <div class="flex items-center gap-2 text-slate-600 font-mono text-xs font-bold bg-slate-50 px-2 py-1.5 rounded-lg border border-slate-100 w-fit {{ request('search') && str_contains($product->barcode, request('search')) ? 'bg-yellow-100 border-yellow-200 text-yellow-800' : '' }}">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                            {{ $product->barcode }}
                        </div>
                    @else
                        <span class="text-[10px] text-slate-300 italic pl-1">No Barcode</span>
                    @endif
                </div>

                <div class="col-span-3">
                    <div class="flex items-center gap-3">
                        <h3 class="font-bold text-slate-800 text-sm group-hover:text-emerald-700 transition-colors leading-tight line-clamp-2">
                            {{ $product->name }}
                        </h3>
                    </div>
                </div>

                <div class="col-span-2">
                    <span class="inline-flex items-center text-[10px] font-bold uppercase px-2.5 py-1 rounded-lg border tracking-wide whitespace-nowrap
                        {{ $product->category == 'Very Fast Moving' ? 'bg-purple-50 text-purple-600 border-purple-100' :
                          ($product->category == 'Fast Moving' ? 'bg-blue-50 text-blue-600 border-blue-100' : 'bg-slate-50 text-slate-400 border-slate-100') }}">
                        {{ $product->category }}
                    </span>
                </div>

                <div class="col-span-2 text-right">
                    <span class="font-mono font-bold text-slate-600 bg-slate-50 px-2 py-1 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all text-sm">
                        ₱{{ number_format($product->price, 2) }}
                    </span>
                </div>

                <div class="col-span-2">
                    <div class="flex flex-col items-center justify-center gap-1.5">
                        <div class="flex items-center gap-2 bg-slate-50 rounded-xl p-1 pr-3 border border-slate-100 group-hover:border-emerald-200 transition-colors">
                            <span class="bg-white shadow-sm px-3 py-1 rounded-lg text-xs font-bold {{ $product->quantity <= $product->low_stock_threshold ? 'text-red-500' : 'text-emerald-600' }}">
                                {{ $product->quantity }}
                            </span>

                            <button @click="showStockModal = true; stockForm = { id: {{ $product->id }}, name: '{{ $product->name }}', current_qty: {{ $product->quantity }}, quantity_to_add: '' }"
                                class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-600 hover:bg-emerald-500 hover:text-white flex items-center justify-center transition-all" title="Quick Add Stock">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            </button>
                        </div>

                        @if($product->quantity <= $product->low_stock_threshold)
                            <div class="flex items-center gap-1 text-[9px] font-bold text-red-500 bg-red-50 px-2 py-0.5 rounded-full animate-pulse border border-red-100">
                                Low Stock
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-span-1 text-right opacity-40 group-hover:opacity-100 transition-opacity duration-200">
                    <div class="flex items-center justify-end gap-1">
                        <button @click="showModal = true; editMode = true; form = {
                            id: {{ $product->id }},
                            barcode: '{{ $product->barcode }}',
                            name: '{{ $product->name }}',
                            category: '{{ $product->category }}',
                            price: '{{ $product->price }}',
                            quantity: '{{ $product->quantity }}',
                            low_stock_threshold: '{{ $product->low_stock_threshold }}'
                        }" class="w-8 h-8 rounded-xl bg-slate-50 text-slate-400 hover:bg-blue-500 hover:text-white hover:shadow-lg hover:shadow-blue-500/30 flex items-center justify-center transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>

                        <form action="{{ route('inventory.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Delete this item?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-xl bg-slate-50 text-slate-400 hover:bg-red-500 hover:text-white hover:shadow-lg hover:shadow-red-500/30 flex items-center justify-center transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="py-20 text-center bg-white rounded-3xl border-2 border-dashed border-slate-100">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
                <h3 class="text-lg font-bold text-slate-800">No Match Found</h3>
                <p class="text-slate-400 text-sm mt-1">Try searching for a different product or barcode.</p>
                @if(request('search'))
                    <a href="{{ route('inventory.branch', $branch->id) }}" class="inline-block mt-4 text-emerald-600 font-bold hover:underline">Clear Search</a>
                @endif
            </div>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $products->links() }}
        </div>
    </div>

    <div x-show="showStockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" x-transition.opacity @click="showStockModal = false"></div>
        <div class="bg-white w-full max-w-sm rounded-[2rem] shadow-2xl relative z-10 overflow-hidden transform transition-all scale-100"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 px-8 py-8 text-center text-white relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-full bg-white/10 opacity-50" style="background-image: radial-gradient(circle, #fff 1px, transparent 1px); background-size: 20px 20px;"></div>
                <h3 class="text-2xl font-black tracking-tight relative z-10">Add Stock</h3>
                <p class="text-emerald-100 font-medium text-sm mt-1 relative z-10" x-text="stockForm.name"></p>
            </div>
            <form :action="'{{ url('admin/inventory') }}/' + stockForm.id + '/quick-add'" method="POST" class="p-8 space-y-6">
                @csrf
                <div class="flex justify-between items-center bg-slate-50 p-4 rounded-2xl border border-slate-100">
                    <span class="text-xs font-bold text-slate-400 uppercase">Current Stock</span>
                    <span class="text-2xl font-black text-slate-700" x-text="stockForm.current_qty"></span>
                </div>
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-400 uppercase ml-1">Quantity to Add</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                            <span class="text-emerald-500 font-black text-xl">+</span>
                        </div>
                        <input type="number" name="quantity_to_add" x-model="stockForm.quantity_to_add" placeholder="0" required min="1" autofocus
                            class="w-full pl-12 pr-4 py-4 bg-white border-2 border-slate-100 rounded-2xl font-black text-2xl text-slate-800 placeholder-slate-200 focus:border-emerald-500 focus:ring-0 transition-all text-center group-hover:border-slate-200">
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" @click="showStockModal = false" class="flex-1 py-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-[2] bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition-all transform active:scale-95 py-4">Confirm</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity" x-transition.opacity @click="showModal = false"></div>
        <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl relative z-10 overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="translate-y-8 opacity-0" x-transition:enter-end="translate-y-0 opacity-100">

            <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="text-xl font-black text-slate-800" x-text="editMode ? 'Edit Product' : 'New Product'"></h3>
                <button @click="showModal = false" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:bg-red-50 hover:text-red-500 flex items-center justify-center transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <form :action="editMode ? '{{ url('admin/inventory') }}/' + form.id : '{{ route('inventory.store') }}'" method="POST" class="p-8 space-y-5">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>
                <input type="hidden" name="branch_id" value="{{ $branch->id }}">

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Barcode (Optional)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path></svg>
                        </div>
                        <input type="text" name="barcode" x-model="form.barcode" placeholder="Scan or type barcode" class="w-full pl-10 bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-mono font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Product Name</label>
                        <input type="text" name="name" x-model="form.name" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Movement Category</label>
                        <div class="relative">
                            <select name="category" x-model="form.category" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all appearance-none">
                                <option value="Very Fast Moving">Very Fast Moving 🔥</option>
                                <option value="Fast Moving">Fast Moving 🚀</option>
                                <option value="Slow Moving">Slow Moving 🐢</option>
                            </select>
                            <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Price (₱)</label>
                        <input type="number" step="0.01" name="price" x-model="form.price" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-mono font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase mb-1 ml-1">Initial Stock</label>
                        <input type="number" name="quantity" x-model="form.quantity" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-mono font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all">
                    </div>
                </div>

                <div class="bg-red-50/50 p-4 rounded-2xl border border-red-100 flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-500 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </div>
                    <div class="flex-1">
                        <label class="block text-xs font-bold text-red-400 uppercase mb-1">Low Stock Threshold</label>
                        <input type="number" name="low_stock_threshold" x-model="form.low_stock_threshold" required class="w-20 bg-white border-red-200 rounded-lg px-2 py-1 font-mono font-bold text-red-600 focus:ring-2 focus:ring-red-500 text-center">
                    </div>
                    <p class="text-[10px] text-slate-400 font-medium leading-tight text-right">Alert me when<br>stock falls below this.</p>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 py-4 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-[2] bg-slate-900 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-lg shadow-slate-200 hover:shadow-emerald-500/30 transition-all transform active:scale-95 py-4">Save Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
