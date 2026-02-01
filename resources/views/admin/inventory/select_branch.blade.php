@extends('layouts.admin')

@section('content')
<div class="space-y-8">

    <div>
        <h1 class="text-3xl font-black text-slate-800 tracking-tight">Inventory Management</h1>
        <p class="text-slate-500 mt-2">Select a branch to manage its stock levels.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($branches as $branch)
        <a href="{{ route('inventory.branch', $branch->id) }}" class="group bg-white p-8 rounded-3xl border border-slate-100 shadow-sm hover:shadow-2xl hover:shadow-emerald-500/10 hover:border-emerald-500/30 transition-all duration-300 relative overflow-hidden">

            <div class="flex items-start justify-between mb-8">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div class="w-10 h-10 rounded-full bg-slate-50 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    <svg class="w-5 h-5 text-slate-300 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </div>
            </div>

            <h3 class="text-2xl font-bold text-slate-800 mb-2 group-hover:text-emerald-700 transition-colors">{{ $branch->name }}</h3>
            <p class="text-sm text-slate-400 font-medium flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                {{ $branch->address ?? 'No address set' }}
            </p>

            <div class="mt-8 pt-6 border-t border-slate-50 flex items-center justify-between">
                <div>
                    <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Items</span>
                    <span class="text-xl font-black text-slate-800">{{ $branch->products_count }}</span>
                </div>
                <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1.5 rounded-lg group-hover:bg-emerald-600 group-hover:text-white transition-colors">Manage Stock</span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endsection
