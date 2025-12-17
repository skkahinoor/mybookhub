<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vendor_id')) {
                $table->dropColumn('vendor_id');
            }
            if (Schema::hasColumn('products', 'admin_id')) {
                $table->dropColumn('admin_id');
            }
            if (Schema::hasColumn('products', 'admin_type')) {
                $table->dropColumn('admin_type');
            }
        });

        Schema::table('products_attributes', function (Blueprint $table) {
            $table->unsignedBigInteger('vendor_id')->nullable()->after('id');
            $table->unsignedBigInteger('admin_id')->nullable()->after('vendor_id');
            $table->string('admin_type')->nullable()->after('admin_id');
        });

        DB::table('products_attributes')
            ->where('vendor_id', 0)
            ->update(['vendor_id' => null]);

        Schema::table('products_attributes', function (Blueprint $table) {
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('products_attributes', function (Blueprint $table) {
            $table->dropForeign(['vendor_id']);
            $table->dropForeign(['admin_id']);

            $table->dropColumn(['vendor_id', 'admin_id', 'admin_type']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->integer('vendor_id')->default(0);
            $table->integer('admin_id');
            $table->string('admin_type');
        });
    }
};
