@extends('layouts.admin')

@section('content')
<div x-data="branchApp()" class="space-y-8 pb-10">

    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Branch Management</h1>
            <p class="text-slate-500 mt-2">Manage locations, manager credentials, and staff.</p>
        </div>
        <button @click="openBranchModal()"
            class="group flex items-center gap-2 bg-slate-900 hover:bg-emerald-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-slate-200 hover:shadow-emerald-200 transition-all duration-300 transform active:scale-95">
            <svg class="w-5 h-5 text-slate-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
            <span>Add New Branch</span>
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
        @foreach($branches as $branch)
        <div class="group bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-xl hover:shadow-emerald-500/10 hover:border-emerald-500/20 transition-all duration-300 relative overflow-hidden flex flex-col h-full">

            <button @click="openEditBranch({{ $branch }}, '{{ $branch->manager->username ?? '' }}')"
                class="absolute top-4 right-4 p-2 rounded-full bg-slate-50 text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-colors z-10" title="Edit Branch & Manager">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
            </button>

            <div class="flex items-start gap-4 mb-6">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200 shrink-0">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-xl text-slate-800 leading-tight group-hover:text-emerald-700 transition-colors">{{ $branch->name }}</h3>
                    <div class="flex items-center gap-1 text-xs font-medium text-slate-400 mt-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        {{ $branch->address ?? 'Location not set' }}
                    </div>
                </div>
            </div>

            <div class="bg-slate-50/80 rounded-2xl p-4 border border-slate-100 group-hover:bg-emerald-50/30 group-hover:border-emerald-100 transition-colors mb-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Manager Login</span>
                    <span class="bg-white text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded border border-slate-200 shadow-sm">ADMIN</span>
                </div>
                <div class="font-mono font-bold text-slate-700 text-sm flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    {{ $branch->manager->username ?? 'Not Assigned' }}
                </div>
            </div>

            <div class="mt-auto pt-4 border-t border-slate-50 flex items-center justify-between">
                <div class="text-xs font-bold text-slate-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    {{ $branch->users->count() }} Staff
                </div>
                <button @click="openTeamModal({{ $branch }}, {{ $branch->users }})" class="text-xs bg-emerald-100 text-emerald-700 px-3 py-2 rounded-lg font-bold hover:bg-emerald-600 hover:text-white transition-colors flex items-center gap-1">
                    Manage Team
                </button>
            </div>
        </div>
        @endforeach
    </div>

    <div x-show="showBranchModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showBranchModal = false" x-transition.opacity></div>
        <div class="bg-white w-full max-w-md rounded-3xl shadow-2xl relative z-10 overflow-hidden" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-slate-50 px-8 py-6 border-b border-slate-100">
                <h3 class="text-xl font-black text-slate-800" x-text="editMode ? 'Edit Branch & Manager' : 'New Branch'"></h3>
            </div>
            <form :action="actionUrl" method="POST" class="p-8 space-y-5">
                @csrf
                <template x-if="editMode"><input type="hidden" name="_method" value="PUT"></template>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Branch Name</label>
                        <input type="text" name="name" x-model="form.name" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Location</label>
                        <input type="text" name="address" x-model="form.address" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500">
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs font-black text-slate-800 uppercase tracking-wide mb-3">Manager Credentials</p>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Username</label>
                                <input type="text" name="username" x-model="form.username" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-mono font-bold text-emerald-600 focus:ring-2 focus:ring-emerald-500">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Password</label>
                                <input type="password" name="password" :required="!editMode" placeholder="••••••" class="w-full bg-slate-50 border-slate-200 rounded-xl px-4 py-3 font-bold text-slate-700 focus:ring-2 focus:ring-emerald-500">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-4 flex gap-3">
                    <button type="button" @click="showBranchModal = false" class="flex-1 py-3.5 rounded-xl font-bold text-slate-500 hover:bg-slate-50 transition-colors">Cancel</button>
                    <button type="submit" class="flex-[2] bg-slate-900 hover:bg-emerald-600 text-white rounded-xl font-bold shadow-lg transition-all py-3.5">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <div x-show="showTeamModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="display: none;">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showTeamModal = false" x-transition.opacity></div>
        <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl relative z-10 overflow-hidden flex flex-col md:flex-row h-[500px]" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">

            <div class="w-full md:w-1/2 bg-slate-50 border-r border-slate-100 flex flex-col">
                <div class="p-6 border-b border-slate-200/50">
                    <h3 class="font-black text-slate-800 text-lg">Staff List</h3>
                    <p class="text-xs text-slate-500 font-bold" x-text="selectedBranchName"></p>
                </div>
                <div class="flex-1 overflow-y-auto p-4 space-y-2 custom-scrollbar">
                    <template x-for="staff in staffList" :key="staff.id">
                        <div class="bg-white p-3 rounded-xl border border-slate-200 flex justify-between items-center group hover:border-emerald-300 transition-colors">
                            <div>
                                <div class="font-bold text-slate-700 text-sm" x-text="staff.name"></div>
                                <div class="text-[10px] font-mono text-slate-400" x-text="staff.username"></div>
                            </div>
                            <div class="flex gap-1">
                                <button @click="editStaff(staff)" class="p-1.5 rounded-lg text-slate-400 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                </button>
                                <form :action="'{{ url('admin/users') }}/' + staff.id" method="POST" onsubmit="return confirm('Remove staff?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </template>
                </div>
                <div class="p-4 border-t border-slate-200/50 text-center">
                    <button @click="resetStaffForm()" class="text-xs font-bold text-emerald-600 hover:underline">+ Add New Staff</button>
                </div>
            </div>

            <div class="w-full md:w-1/2 bg-white flex flex-col">
                <div class="p-6 border-b border-slate-100">
                    <h3 class="font-black text-slate-800 text-lg" x-text="isEditingStaff ? 'Edit Staff' : 'Add New Staff'"></h3>
                </div>
                <form :action="staffActionUrl" method="POST" class="p-6 space-y-4 flex-1">
                    @csrf
                    <template x-if="isEditingStaff"><input type="hidden" name="_method" value="PUT"></template>
                    <input type="hidden" name="branch_id" x-model="selectedBranchId">

                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Name</label>
                        <input type="text" name="name" x-model="staffForm.name" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700 text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Username</label>
                        <input type="text" name="username" x-model="staffForm.username" required class="w-full bg-slate-50 border-slate-200 rounded-xl px-3 py-2 font-mono font-bold text-emerald-600 text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="space-y-1">
                        <label class="text-[10px] font-bold text-slate-400 uppercase">Password</label>
                        <input type="password" name="password" :required="!isEditingStaff" placeholder="••••••" class="w-full bg-slate-50 border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-700 text-sm focus:ring-2 focus:ring-emerald-500">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-slate-900 hover:bg-emerald-600 text-white rounded-xl font-bold py-3 transition-colors shadow-lg">
                            <span x-text="isEditingStaff ? 'Update Staff' : 'Create Account'"></span>
                        </button>
                        <button type="button" @click="showTeamModal = false" class="w-full mt-2 text-xs font-bold text-slate-400 hover:text-slate-600">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function branchApp() {
        return {
            showBranchModal: false,
            editMode: false,
            actionUrl: '',
            form: { name: '', address: '', username: '' },

            showTeamModal: false,
            selectedBranchId: null,
            selectedBranchName: '',
            staffList: [],
            isEditingStaff: false,
            staffActionUrl: '',
            staffForm: { id: null, name: '', username: '' },

            openBranchModal() {
                this.editMode = false;
                this.form = { name: '', address: '', username: '' };
                this.actionUrl = "{{ route('branches.store') }}";
                this.showBranchModal = true;
            },

            openEditBranch(branch, username) {
                this.editMode = true;
                this.form.name = branch.name;
                this.form.address = branch.address;
                this.form.username = username;
                this.actionUrl = "{{ url('admin/branches') }}/" + branch.id;
                this.showBranchModal = true;
            },

            openTeamModal(branch, users) {
                this.selectedBranchId = branch.id;
                this.selectedBranchName = branch.name;
                this.staffList = users;
                this.resetStaffForm();
                this.showTeamModal = true;
            },

            resetStaffForm() {
                this.isEditingStaff = false;
                this.staffForm = { id: null, name: '', username: '' };
                this.staffActionUrl = "{{ route('users.store') }}";
            },

            editStaff(staff) {
                this.isEditingStaff = true;
                this.staffForm.id = staff.id;
                this.staffForm.name = staff.name;
                this.staffForm.username = staff.username;
                this.staffActionUrl = "{{ url('admin/users') }}/" + staff.id;
            }
        }
    }
</script>
@endsection
