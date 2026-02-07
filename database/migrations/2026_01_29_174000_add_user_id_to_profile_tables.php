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
        // Add user_id to vendors table
        if (Schema::hasTable('vendors') && !Schema::hasColumn('vendors', 'user_id')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
                // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Add user_id to sales_executives table
        if (Schema::hasTable('sales_executives') && !Schema::hasColumn('sales_executives', 'user_id')) {
            Schema::table('sales_executives', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
        Schema::table('sales_executives', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
