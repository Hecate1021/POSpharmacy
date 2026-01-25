@extends('layouts.admin')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Account Settings</h1>
            <p class="text-gray-500 mt-1">Manage your access credentials and profile details.</p>
        </div>
        @if (session('status') === 'profile-updated' || session('status') === 'password-updated')
            <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                 class="bg-emerald-100 border border-emerald-400 text-emerald-700 px-4 py-2 rounded-lg flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span>Changes saved successfully.</span>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-12 w-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Profile Details</h2>
                        <p class="text-xs text-gray-500">Update your public name and ID.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Display Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors">
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Username / Login ID</label>
                        <input type="text" name="username" value="{{ old('username', $user->username) }}" required
                            class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors bg-gray-50">
                        <x-input-error class="mt-2" :messages="$errors->get('username')" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                             class="w-full rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 shadow-sm transition-colors">
                         <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-medium py-2 px-4 rounded-lg transition-colors shadow-md shadow-emerald-200">
                            Save Profile
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-8">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
                 <div class="flex items-center gap-4 mb-6">
                    <div class="h-12 w-12 rounded-full bg-orange-100 flex items-center justify-center text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Security</h2>
                        <p class="text-xs text-gray-500">Ensure your account is using a strong password.</p>
                    </div>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @csrf
                    @method('put')

                    <div class="col-span-2 md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                        <input type="password" name="current_password" autocomplete="current-password"
                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" autocomplete="new-password"
                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password"
                            class="w-full rounded-lg border-gray-300 focus:border-orange-500 focus:ring-orange-500 shadow-sm">
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>

                    <div class="col-span-2 flex justify-end">
                        <button type="submit" class="bg-gray-800 hover:bg-black text-white font-medium py-2 px-6 rounded-lg transition-colors">
                            Update Password
                        </button>
                    </div>
                </form>
            </div>

            {{-- <div class="bg-red-50 p-6 rounded-2xl border border-red-100 opacity-80 hover:opacity-100 transition-opacity">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-red-800">Danger Zone</h3>
                        <p class="text-sm text-red-600 mt-1">Once you delete your account, there is no going back.</p>
                    </div>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="bg-white border border-red-200 text-red-600 hover:bg-red-600 hover:text-white font-medium py-2 px-4 rounded-lg transition-colors text-sm">
                        Delete Account
                    </button>
                </div>
            </div> --}}

            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-medium text-gray-900">Are you sure you want to delete your account?</h2>
                    <p class="mt-1 text-sm text-gray-600">Please enter your password to confirm you would like to permanently delete your account.</p>

                    <div class="mt-6">
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" class="w-full rounded-lg border-gray-300" placeholder="Password" />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="button" x-on:click="$dispatch('close')" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-lg mr-3">Cancel</button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg">Delete Account</button>
                    </div>
                </form>
            </x-modal>

        </div>
    </div>
@endsection
