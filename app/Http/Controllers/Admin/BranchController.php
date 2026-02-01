<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BranchController extends Controller
{
    public function index()
    {
        // Eager load manager and users to avoid N+1 query issues
        $branches = Branch::with(['manager', 'users'])->get();
        return view('admin.branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        // ... (Keep existing store logic) ...
         $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|min:6',
            'address' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();
            $branch = Branch::create(['name' => $request->name, 'address' => $request->address]);
            User::create([
                'name' => $request->name,
                'username' => $request->username,
                'email' => $request->username . '@system.local',
                'password' => Hash::make($request->password),
                'role' => 'branch_manager',
                'branch_id' => $branch->id,
            ]);
            DB::commit();
            return back()->with('success', 'Branch created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    // --- 1. UPDATE BRANCH & MANAGER ---
    public function update(Request $request, Branch $branch)
    {
        $manager = $branch->manager;

        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'address' => 'nullable|string',
            // Check username uniqueness but ignore the current manager's ID
            'username' => 'required|string|max:50|unique:users,username,' . ($manager ? $manager->id : 'NULL'),
            'password' => 'nullable|min:6', // Optional on update
        ]);

        try {
            DB::beginTransaction();

            // A. Update Branch Info
            $branch->update([
                'name' => $request->name,
                'address' => $request->address,
            ]);

            // B. Update Manager Account
            if ($manager) {
                $manager->name = $request->name; // Sync manager name with branch name
                $manager->username = $request->username;
                if ($request->filled('password')) {
                    $manager->password = Hash::make($request->password);
                }
                $manager->save();
            } else {
                // Determine if we should create a missing manager?
                // For now, let's just create one if missing to fix broken data
                User::create([
                    'name' => $request->name,
                    'username' => $request->username,
                    'email' => $request->username . '@system.local',
                    'password' => Hash::make($request->password ?? 'password123'), // Default if null
                    'role' => 'branch_manager',
                    'branch_id' => $branch->id,
                ]);
            }

            DB::commit();
            return back()->with('success', 'Branch and Manager account updated!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }

    public function storeUser(Request $request)
    {
        // ... (Keep existing storeUser logic) ...
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->username . '@system.local',
            'password' => Hash::make($request->password),
            'role' => 'cashier',
            'branch_id' => $request->branch_id,
        ]);

        return back()->with('success', 'Cashier added!');
    }

    // --- 2. UPDATE CASHIER ACCOUNT ---
    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|min:6',
        ]);

        $data = [
            'name' => $request->name,
            'username' => $request->username,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Cashier account updated!');
    }

    public function destroyUser(User $user)
    {
        $user->delete();
        return back()->with('success', 'Staff removed.');
    }
}
