@extends('layouts.admin')

@section('content')
<div x-data="inventoryApp()" class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 tracking-tight">Inventory</h1>
            <p class="text-gray-500 text-sm">Manage stock levels and pricing.</p>
        </div>
        <button @click="openAdd()"
            class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-3 rounded-xl shadow-lg shadow-emerald-200 flex items-center gap-2 transition-all transform active:scale-95 text-sm font-bold">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            ADD PRODUCT
        </button>
    </div>

    <div class="relative group">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <svg class="h-6 w-6 text-gray-400 group-focus-within:text-emerald-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
        </div>
        <input type="text" x-model="search"
            class="block w-full pl-12 pr-4 py-4 border-gray-200 rounded-2xl shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-base placeholder-gray-400 transition-shadow"
            placeholder="Type to search products instantly...">
    </div>

    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms x-init="setTimeout(() => show = false, 4000)"
             class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl flex items-center gap-3 shadow-sm">
            <svg class="w-5 h-5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Product Name</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Stock Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Manage</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <template x-for="product in filteredProducts" :key="product.id">
                        <tr class="hover:bg-gray-50 transition-colors">

                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-800" x-text="product.name"></span>
                                    <span class="text-xs text-gray-400" x-text="'ID: ' + product.id"></span>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-lg text-sm font-bold font-mono" x-text="'₱' + Number(product.price).toFixed(2)"></span>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="px-3 py-1.5 text-xs font-bold rounded-lg border"
                                          :class="product.quantity <= 10
                                              ? 'bg-red-50 text-red-700 border-red-100'
                                              : 'bg-emerald-50 text-emerald-700 border-emerald-100'">
                                        <span x-text="product.quantity"></span> Left
                                    </span>

                                    <button @click="openStockModal(product)"
                                        class="w-8 h-8 rounded-full flex items-center justify-center border border-gray-200 text-gray-400 hover:text-emerald-600 hover:border-emerald-300 hover:bg-emerald-50 transition-all" title="Add Stock">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex justify-end items-center gap-2">
                                    <button @click="openEdit(product)"
                                        class="text-indigo-600 hover:text-indigo-800 bg-indigo-50 hover:bg-indigo-100 px-3 py-2 rounded-lg text-xs font-bold transition-colors">
                                        EDIT
                                    </button>

                                    <button @click="confirmDelete(product.id, product.name)"
                                        class="text-red-600 hover:text-red-800 bg-red-50 hover:bg-red-100 px-3 py-2 rounded-lg text-xs font-bold transition-colors">
                                        DELETE
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <tr x-show="filteredProducts.length === 0">
                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <p class="text-base font-medium">No products found matching "<span x-text="search"></span>"</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <form id="delete-form" method="POST" style="display: none;">
        @csrf @method('DELETE')
    </form>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showModal = false" x-transition.opacity></div>

        <div class="bg-white w-full max-w-md rounded-2xl shadow-2xl relative z-10 overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" x-text="editMode ? 'Edit Product' : 'Add New Product'"></h3>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
            </div>

            <form method="POST" :action="actionUrl" class="p-6">
                @csrf
                <input type="hidden" name="_method" :value="editMode ? 'PUT' : 'POST'">

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Product Name</label>
                        <input type="text" name="name" x-model="form.name" required
                            class="w-full rounded-xl border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 py-3 text-sm">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Price</label>
                            <input type="number" step="0.01" name="price" x-model="form.price" required
                                class="w-full rounded-xl border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 py-3 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Stock Qty</label>
                            <input type="number" name="quantity" x-model="form.quantity" required
                                class="w-full rounded-xl border-gray-300 focus:ring-emerald-500 focus:border-emerald-500 py-3 text-sm">
                        </div>
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button" @click="showModal = false" class="flex-1 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl font-bold text-sm transition-colors">Cancel</button>
                    <button type="submit" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-md transition-colors">Save Product</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showStockModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" @click="showStockModal = false" x-transition.opacity></div>

        <div class="bg-white w-full max-w-sm rounded-2xl shadow-2xl relative z-10 overflow-hidden transform transition-all"
             x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="p-6 text-center">
                <h3 class="text-xl font-bold text-gray-800">Restock Item</h3>
                <p class="text-sm text-gray-500 mt-1">Adding stock to <span class="font-bold text-emerald-600" x-text="form.name"></span></p>

                <form method="POST" :action="actionUrl" class="mt-6">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">
                    <input type="hidden" name="name" x-model="form.name">
                    <input type="hidden" name="price" x-model="form.price">

                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mb-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-500">Current</span>
                            <span class="font-bold" x-text="form.quantity"></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-bold text-emerald-500">+</span>
                            <input type="number" x-model="tempStockAdd" placeholder="0" autofocus
                                class="w-full text-center text-xl font-bold border-gray-300 rounded-lg focus:ring-emerald-500 focus:border-emerald-500">
                        </div>
                        <div class="flex justify-between text-sm mt-2 pt-2 border-t border-gray-200">
                            <span class="text-gray-500">New Total</span>
                            <span class="font-bold text-emerald-600" x-text="parseInt(form.quantity) + (parseInt(tempStockAdd) || 0)"></span>
                        </div>
                    </div>

                    <input type="hidden" name="quantity" :value="parseInt(form.quantity) + (parseInt(tempStockAdd) || 0)">

                    <button type="submit" class="w-full py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold shadow-lg transition-colors">Confirm Update</button>
                </form>
            </div>
        </div>
    </div>

</div>

<script>
    function inventoryApp() {
        return {
            products: @json($products), // Load ALL products for live search
            search: '',

            showModal: false,
            showStockModal: false,
            editMode: false,

            actionUrl: '',
            tempStockAdd: '',
            form: { id: null, name: '', price: '', quantity: 0 },

            // LIVE SEARCH LOGIC
            get filteredProducts() {
                if (this.search === '') return this.products;
                return this.products.filter(p => {
                    return p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                           p.id.toString().includes(this.search);
                });
            },

            openAdd() {
                this.showModal = true;
                this.editMode = false;
                this.form = { id: null, name: '', price: '', quantity: '' };
                this.actionUrl = '{{ route("inventory.store") }}';
            },

            openEdit(product) {
                this.showModal = true;
                this.editMode = true;
                this.form = { ...product }; // Clone object
                this.actionUrl = '/inventory/' + product.id; // Manual URL construction for client-side
            },

            openStockModal(product) {
                this.showStockModal = true;
                this.tempStockAdd = '';
                this.form = { ...product };
                this.actionUrl = '/inventory/' + product.id;

                setTimeout(() => document.querySelector('[x-model="tempStockAdd"]')?.focus(), 100);
            },

            confirmDelete(id, name) {
                if(confirm('Are you sure you want to delete ' + name + '?')) {
                    let form = document.getElementById('delete-form');
                    form.action = '/inventory/' + id;
                    form.submit();
                }
            }
        }
    }
</script>
@endsection
