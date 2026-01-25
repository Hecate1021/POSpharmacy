<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SALES (The Receipt Header)
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique(); // e.g., INV-10001
            $table->foreignId('user_id')->constrained(); // Who sold it?

            $table->decimal('total_amount', 10, 2);
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('change', 10, 2);
            $table->string('payment_method')->default('Cash'); // Cash, G-Cash, Card

            $table->timestamps(); // Created_at is the Sale Date
        });

        // 2. SALE ITEMS (The Medicines in the Receipt)
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained();

            $table->integer('quantity');
            $table->decimal('price', 10, 2); // Price AT THE TIME of sale
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('sales');
    }
};
