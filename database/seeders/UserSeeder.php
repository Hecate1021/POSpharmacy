<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   public function run(): void
    {
        // 1. Create the Admin Account
        User::create([
            'name' => 'Owner Admin',
            'username' => 'admin',          // Login with this
            'email' => 'admin@pharma.com',  // Optional fallback
            'password' => Hash::make('admin123'), // Password
            'role' => 1,                    // 1 = Admin
        ]);

        // 2. Create the Sales Staff Account
        User::create([
            'name' => 'Cashier Staff',
            'username' => 'cashier',        // Login with this
            'email' => 'sales@pharma.com',  // Optional fallback
            'password' => Hash::make('sales123'), // Password
            'role' => 0,                    // 0 = Sales
        ]);
    }
}
