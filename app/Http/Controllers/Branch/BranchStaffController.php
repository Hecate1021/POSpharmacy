<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class BranchStaffController extends Controller
{
    public function index()
    {
        // 1. Get only staff belonging to THIS branch
        $staff = User::where('branch_id', Auth::user()->branch_id)
                     ->where('role', 'cashier') // Only show cashiers, not the manager
                     ->latest()
                     ->get();

        return view('branch.staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@system.local', // Dummy email
            'password' => Hash::make($request->password),
            'role' => 'cashier', // Important!
            'branch_id' => Auth::user()->branch_id, // Automatically link to THIS branch
        ]);

        return back()->with('success', 'New Cashier account created!');
    }

    public function destroy(User $user)
    {
        // Security Check: Ensure manager can only delete THEIR OWN staff
        if ($user->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $user->delete();
        return back()->with('success', 'Staff account removed.');
    }
}
