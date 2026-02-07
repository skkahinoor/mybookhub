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
            $table->dropColumn([
                'profile_picture',
                'address',
                'city',
                'block',
                'district',
                'state',
                'pincode',
                'country'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales_executives', function (Blueprint $table) {
            $table->string('profile_picture')->nullable();
            $table->longText('address')->nullable();
            $table->string('city')->nullable();
            $table->string('block')->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('country')->nullable();
        });
    }
};
