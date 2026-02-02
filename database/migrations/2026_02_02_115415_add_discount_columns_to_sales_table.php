<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Add these new columns after 'user_id'
            $table->decimal('subtotal', 10, 2)->default(0)->after('user_id');
            $table->decimal('discount_rate', 5, 2)->default(0)->after('subtotal'); // Stores 0.10, 0.20
            $table->decimal('discount_amount', 10, 2)->default(0)->after('discount_rate');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['subtotal', 'discount_rate', 'discount_amount']);
        });
    }
};
