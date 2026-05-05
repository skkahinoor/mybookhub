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
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('status');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('delivery_agent_id')->nullable()->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->dropColumn('is_online');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('delivery_agent_id');
        });
    }
};
