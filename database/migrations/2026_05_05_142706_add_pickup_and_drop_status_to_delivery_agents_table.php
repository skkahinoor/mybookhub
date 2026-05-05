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
            $table->tinyInteger('pickup_status')->default(0)->after('is_online')->comment('0: Pending, 1: Picked Up');
            $table->tinyInteger('drop_status')->default(0)->after('pickup_status')->comment('0: Pending, 1: Delivered');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->dropColumn(['pickup_status', 'drop_status']);
        });
    }
};
