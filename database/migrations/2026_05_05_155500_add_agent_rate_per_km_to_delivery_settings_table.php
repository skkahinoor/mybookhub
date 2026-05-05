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
        Schema::table('delivery_settings', function (Blueprint $table) {
            $table->decimal('agent_rate_per_km', 10, 2)->default(10.00)->after('delivery_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_settings', function (Blueprint $table) {
            $table->dropColumn('agent_rate_per_km');
        });
    }
};
