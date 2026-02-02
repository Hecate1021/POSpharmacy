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
            // 1. Invoice Number (Unique String)
            if (!Schema::hasColumn('sales', 'invoice_no')) {
                $table->string('invoice_no')->unique()->nullable()->after('id');
            }
            // 2. Cash Received
            if (!Schema::hasColumn('sales', 'cash_received')) {
                $table->decimal('cash_received', 10, 2)->default(0)->after('total_amount');
            }
            // 3. Change Amount
            if (!Schema::hasColumn('sales', 'change_amount')) {
                $table->decimal('change_amount', 10, 2)->default(0)->after('cash_received');
            }
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['invoice_no', 'cash_received', 'change_amount']);
        });
    }
};
