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
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('product_id')->comment('Seller ID (Student/User)');
            $table->unsignedBigInteger('old_book_condition_id')->nullable()->after('user_id')->comment('Mapped to old_book_conditions table');
            
            // Adding status column if it doesn't exist to handle admin verification
            if (!Schema::hasColumn('products_attributes', 'admin_approved')) {
                $table->tinyInteger('admin_approved')->default(0)->after('status')->comment('0=Pending, 1=Approved, 2=Rejected');
            }
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
            $table->dropColumn(['user_id', 'old_book_condition_id', 'admin_approved']);
        });
    }
};
