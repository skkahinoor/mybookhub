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
        Schema::table('vendors', function (Blueprint $table) {
            $table->boolean('whatsapp_opt_in')->default(false)->after('location');
            $table->string('whatsapp_phone', 20)->nullable()->after('whatsapp_opt_in');
            $table->timestamp('whatsapp_opt_in_at')->nullable()->after('whatsapp_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_opt_in',
                'whatsapp_phone',
                'whatsapp_opt_in_at',
            ]);
        });
    }
};
