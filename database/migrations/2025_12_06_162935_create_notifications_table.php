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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('sales_executive_registration'); // Type of notification
            $table->string('title'); // Notification title
            $table->text('message'); // Notification message
            $table->unsignedBigInteger('related_id')->nullable(); // ID of related entity (e.g., sales_executive_id)
            $table->string('related_type')->nullable(); // Type of related entity (e.g., 'App\Models\SalesExecutive')
            $table->boolean('is_read')->default(false); // Whether notification is read
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
