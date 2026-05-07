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
        Schema::create('delivery_agent_contact_query_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('query_id');
            $table->enum('sender_type', ['agent', 'admin']);
            $table->text('message');
            $table->timestamps();
            
            $table->foreign('query_id', 'dacq_msg_query_fk')->references('id')->on('delivery_agent_contact_queries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_agent_contact_query_messages');
    }
};
