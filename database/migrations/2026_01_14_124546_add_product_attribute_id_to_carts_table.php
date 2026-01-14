<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {

            $table->unsignedBigInteger('product_attribute_id')
                  ->after('user_id')
                  ->nullable();

            $table->index('product_attribute_id');

        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['product_attribute_id']);
            $table->dropColumn('product_attribute_id');
        });
    }
};
