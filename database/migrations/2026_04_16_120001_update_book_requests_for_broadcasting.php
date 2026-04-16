<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('book_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('book_requests', 'district_id')) {
                $table->unsignedBigInteger('district_id')->nullable()->after('requested_by_user');
            }
            $table->unsignedBigInteger('vendor_id')->nullable()->change();
        });

        Schema::table('book_request_replies', function (Blueprint $table) {
            if (!Schema::hasColumn('book_request_replies', 'vendor_id')) {
                $table->unsignedBigInteger('vendor_id')->nullable()->after('book_request_id');
            }
        });
    }

    public function down()
    {
        Schema::table('book_requests', function (Blueprint $table) {
            $table->dropColumn('district_id');
        });

        Schema::table('book_request_replies', function (Blueprint $table) {
            $table->dropColumn('vendor_id');
        });
    }
};
