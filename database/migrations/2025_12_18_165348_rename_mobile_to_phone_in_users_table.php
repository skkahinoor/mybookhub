<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // mobile -> phone (MariaDB compatible)
            $table->string('phone', 15)->after('pincode');
            $table->dropColumn('mobile');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('mobile', 15)->after('pincode');
            $table->dropColumn('phone');
        });
    }
};
