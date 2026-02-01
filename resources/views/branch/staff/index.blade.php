@extends('layouts.admin')

@section('content')
<div x-data="{ showModal: false }" class="space-y-6">

    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-slate-800">My Staff</h1>
            <p class="text-slate-500">Manage cashiers for {{ auth()->user()->branch->name }}.</p>
        </div>
        <button @click="showModal = true" class="bg-slate-900 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg transition-all flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            Add Cashier
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($staff as $member)
        <div class="bg-white p-6 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between group hover:border-emerald-400 transition-colors">
            <div class="flex items-start justify-between">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-lg">
                        {{ substr($member->name, 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800">{{ $member->name }}</h3>
                        <p class="text-xs font-mono text-emerald-600 font-bold bg-emerald-50 inline-block px-2 py-0.5 rounded mt-1">{{ $member->username }}</p>
                    </div>
                </div>
                <form action="{{ route('branch.staff.destroy', $member->id) }}" method="POST" onsubmit="return confirm('Remove this cashier account?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
            <div class="mt-4 pt-4 border-t border-slate-50 text-xs text-slate-400">
                Created {{ $member->created_at->diffForHumans() }}
            </div>
        </div>
        @empty
        <div class="col-span-full py-12 text-center text-slate-400 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
            <p>No cashiers found. Add one to get started!</p>
        </div>
        @endforelse
    </div>

    <div x-show="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showModal = false"></div>
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl relative z-10 p-8">
            <h2 class="text-xl font-black text-slate-800 mb-6">New Cashier Account</h2>

            <form action="{{ route('branch.staff.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name</label>
                    <input type="text" name="name" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-emerald-500 font-bold text-slate-700">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Username (Login ID)</label>
                    <input type="text" name="username" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-emerald-500 font-mono font-bold text-emerald-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Password</label>
                    <input type="password" name="password" required class="w-full rounded-xl border-slate-200 bg-slate-50 focus:bg-white focus:ring-emerald-500">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" @click="showModal = false" class="flex-1 py-3 rounded-xl font-bold text-slate-500 hover:bg-slate-50">Cancel</button>
                    <button type="submit" class="flex-1 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-200">Create Account</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
