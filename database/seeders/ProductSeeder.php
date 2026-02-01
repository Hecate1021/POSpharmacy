<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Branch;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Get all branches (Main & North)
        $branches = Branch::all();

        // 2. Define your Master List of Medicines
        // Add all your SQL items here
        $masterInventory = [
            ['name' => 'Allopurinol 100mg', 'price' => 3.00],
            ['name' => 'Allopurinol 300mg', 'price' => 5.00],
            ['name' => 'Ambroxol 30mg', 'price' => 3.50],
            ['name' => 'Amlodipine 5mg', 'price' => 3.00],
            ['name' => 'Amlodipine 10mg', 'price' => 6.00],
            ['name' => 'Amoxicillin 500mg', 'price' => 5.00],
            ['name' => 'Ascorbic Acid (Vitamin C)', 'price' => 2.00],
            ['name' => 'Atorvastatin 20mg', 'price' => 12.00],
            ['name' => 'Biogesic 500mg', 'price' => 4.50],
            ['name' => 'Celecoxib 200mg', 'price' => 15.00],
            ['name' => 'Cetirizine 10mg', 'price' => 5.00],
            ['name' => 'Co-Amoxiclav 625mg', 'price' => 25.00],
            ['name' => 'Dolfenal 500mg', 'price' => 35.00],
            ['name' => 'Ferrous Sulfate', 'price' => 3.00],
            ['name' => 'Gaviscon Sachet', 'price' => 28.00],
            ['name' => 'Ibuprofen 400mg', 'price' => 6.00],
            ['name' => 'Loperamide 2mg', 'price' => 4.00],
            ['name' => 'Losartan 50mg', 'price' => 8.00],
            ['name' => 'Mefenamic Acid 500mg', 'price' => 5.00],
            ['name' => 'Metformin 500mg', 'price' => 4.00],
            ['name' => 'Multivitamins Enervon', 'price' => 7.00],
            ['name' => 'Neozep Tablet', 'price' => 5.50],
            ['name' => 'Omeprazole 20mg', 'price' => 10.00],
            ['name' => 'Paracetamol 500mg', 'price' => 3.00],
            ['name' => 'Robitussin Syrup', 'price' => 120.00],
            ['name' => 'Simvastatin 20mg', 'price' => 10.00],
            ['name' => 'Solmux Capsule', 'price' => 11.00],
            ['name' => 'Tempra Syrup', 'price' => 115.00],
            ['name' => 'Vitamin B Complex', 'price' => 5.00],
            ['name' => 'Nanz Liniment oil 30ml', 'price' => 110.00],
        ];

        // 3. Loop through EACH branch and add the inventory
        foreach ($branches as $branch) {
            foreach ($masterInventory as $item) {
                Product::create([
                    'branch_id' => $branch->id,
                    // Generate a fake barcode based on branch ID + random number
                    'barcode'   => $branch->id . '-' . str_pad(rand(1, 99999), 6, '0', STR_PAD_LEFT),
                    'name'      => $item['name'],
                    'category'  => 'Fast Moving', // Default category
                    'price'     => $item['price'],
                    'quantity'  => 200, // Force quantity to 200
                    'low_stock_threshold' => 20,
                ]);
            }
        }
    }
}
