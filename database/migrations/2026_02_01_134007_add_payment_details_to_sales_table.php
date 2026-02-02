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
            // Add these two columns
            $table->decimal('cash_received', 10, 2)->default(0)->after('total_amount');
            $table->decimal('change_amount', 10, 2)->default(0)->after('cash_received');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['cash_received', 'change_amount']);
        });
    }
};
