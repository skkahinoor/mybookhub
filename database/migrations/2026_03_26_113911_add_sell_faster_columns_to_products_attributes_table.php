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
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->tinyInteger('show_contact')->default(0)->after('admin_approved');
            $table->tinyInteger('contact_details_paid')->default(0)->after('show_contact');
            $table->decimal('platform_charge', 10, 2)->default(0)->after('contact_details_paid');
            $table->tinyInteger('is_sold')->default(0)->after('platform_charge');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->dropColumn(['show_contact', 'contact_details_paid', 'platform_charge', 'is_sold']);
        });
    }
};
