<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. CREATE SUPER ADMIN (Global Access)
        // ==========================================
        User::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@pharmaco.local',
            'password' => Hash::make('admin123'), // Default password
            'role' => 'admin',
            'branch_id' => null, // Admin does not belong to a specific branch
        ]);

        // ==========================================
        // 2. CREATE BRANCH A (Main Branch)
        // ==========================================
        $branchA = Branch::create([
            'name' => 'PharmaCo Iulan',
            'address' => 'Kalawag I, Isulan Sultan Kudarat',
        ]);

        // Manager for Branch A
        User::create([
            'name' => 'Manager Main',
            'username' => 'isulan_branch',
            'email' => 'manager_main@pharmaco.local',
            'password' => Hash::make('isulan123'),
            'role' => 'branch_manager',
            'branch_id' => $branchA->id,
        ]);

        // Cashier for Branch A
        User::create([
            'name' => 'Cashier ',
            'username' => 'isulan_cashier',
            'email' => 'cashier_main@pharmaco.local',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier',
            'branch_id' => $branchA->id,
        ]);

        // ==========================================
        // 3. CREATE BRANCH B (North Branch)
        // ==========================================
        $branchB = Branch::create([
            'name' => 'PharmaCo Koronadal',
            'address' => 'Koronadal City, South Cotabato',
        ]);

        // Manager for Branch B
        User::create([
            'name' => 'Manager North',
            'username' => 'koronadal_branch',
            'email' => 'manager_north@pharmaco.local',
            'password' => Hash::make('koronadal123'),
            'role' => 'branch_manager',
            'branch_id' => $branchB->id,
        ]);

        // Cashier for Branch B
        User::create([
            'name' => 'Cashier North',
            'username' => 'koronadal_cashier',
            'email' => 'cashier_north@pharmaco.local',
            'password' => Hash::make('cashier123'),
            'role' => 'cashier',
            'branch_id' => $branchB->id,
        ]);
    }
}
