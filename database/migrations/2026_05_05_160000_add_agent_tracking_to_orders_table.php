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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('agent_start_lat')->nullable();
            $table->string('agent_start_lng')->nullable();
            $table->decimal('total_trip_distance', 10, 2)->default(0.00);
            $table->decimal('agent_trip_earning', 10, 2)->default(0.00);
            $table->decimal('agent_rate_at_trip', 10, 2)->default(0.00);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['agent_start_lat', 'agent_start_lng', 'total_trip_distance', 'agent_trip_earning', 'agent_rate_at_trip']);
        });
    }
};
