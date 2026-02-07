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
        // Drop foreign keys that point to admins table
        if (Schema::hasTable('products_attributes')) {
            Schema::table('products_attributes', function (Blueprint $table) {
                // We use an array for dropForeign to let Laravel guess the name or we try to be safe
                try {
                    $table->dropForeign(['admin_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }

        if (Schema::hasTable('ebooks')) {
            Schema::table('ebooks', function (Blueprint $table) {
                try {
                    $table->dropForeign(['admin_id']);
                } catch (\Exception $e) {
                    // Foreign key might not exist
                }
            });
        }

        // Remove redundant columns from sales_executives if they exist
        if (Schema::hasTable('sales_executives')) {
            Schema::table('sales_executives', function (Blueprint $table) {
                $columns = ['name', 'email', 'password', 'phone'];
                foreach ($columns as $column) {
                    if (Schema::hasColumn('sales_executives', $column)) {
                        $table->dropColumn($column);
                    }
                }
            });
        }

        // Drop the admins table
        Schema::dropIfExists('admins');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-create admins table
        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type');
                $table->integer('vendor_id');
                $table->string('mobile');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('image')->nullable();
                $table->enum('confirm', ['No', 'Yes'])->default('No');
                $table->tinyInteger('status');
                $table->timestamps();
            });
        }

        // Re-add columns to sales_executives
        if (Schema::hasTable('sales_executives')) {
            Schema::table('sales_executives', function (Blueprint $table) {
                if (!Schema::hasColumn('sales_executives', 'name')) $table->string('name')->nullable();
                if (!Schema::hasColumn('sales_executives', 'email')) $table->string('email')->nullable();
                if (!Schema::hasColumn('sales_executives', 'password')) $table->string('password')->nullable();
                if (!Schema::hasColumn('sales_executives', 'phone')) $table->string('phone')->nullable();
            });
        }

        // Re-add foreign keys
        if (Schema::hasTable('products_attributes')) {
            Schema::table('products_attributes', function (Blueprint $table) {
                $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            });
        }

        if (Schema::hasTable('ebooks')) {
            Schema::table('ebooks', function (Blueprint $table) {
                $table->foreign('admin_id')->references('id')->on('admins')->onDelete('cascade');
            });
        }
    }
};
