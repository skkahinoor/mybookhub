<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->tinyInteger('document_verify_status')->default(0)->comment('0: Pending, 1: Approved, 2: Rejected');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('delivery_agents', function (Blueprint $table) {
            $table->dropColumn('document_verify_status');
        });
    }
};
