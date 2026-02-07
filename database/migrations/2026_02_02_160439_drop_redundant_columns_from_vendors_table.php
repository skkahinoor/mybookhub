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
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['district_id']);
            $table->dropForeign(['block_id']);

            $table->dropColumn([
                'name',
                'address',
                'country_id',
                'state_id',
                'district_id',
                'block_id',
                'pincode',
                'mobile',
                'email',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('name')->after('user_id');
            $table->string('address')->nullable()->after('location'); // location is staying, so after location, or after name if location wasn't there (but we are skipping location in drop)
            $table->foreignId('country_id')->nullable()->after('address');
            $table->foreignId('state_id')->nullable()->after('country_id');
            $table->foreignId('district_id')->nullable()->after('state_id');
            $table->foreignId('block_id')->nullable()->after('district_id');
            $table->string('pincode')->nullable()->after('block_id');
            $table->string('mobile')->after('pincode');
            $table->string('email')->after('mobile');
        });
    }
};
