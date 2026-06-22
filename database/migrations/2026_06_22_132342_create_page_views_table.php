<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('url', 500);
            $table->string('page_title', 255)->nullable();
            $table->string('module', 50)->default('frontend'); // frontend, student, vendor, sales
            $table->string('ip_address', 45);
            $table->string('country', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // One record per IP per URL (unique view tracking)
            $table->unique(['url', 'ip_address'], 'unique_ip_url');
            $table->index('module');
            $table->index('country');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_views');
    }
};

