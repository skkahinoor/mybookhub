<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales_executives', function (Blueprint $table) {
            $table->decimal('income_per_vendor', 10, 2)->default(0)->after('income_per_target');
            $table->decimal('income_per_pro_vendor', 10, 2)->default(0)->after('income_per_vendor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_executives', function (Blueprint $table) {
            $table->dropColumn(['income_per_vendor', 'income_per_pro_vendor']);
        });
    }
};
