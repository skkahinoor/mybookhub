<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['vendor_id', 'admin_id', 'admin_type']);
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('vendor_id')->default(0);
            $table->integer('admin_id');
            $table->string('admin_type');
        });
    }
};
