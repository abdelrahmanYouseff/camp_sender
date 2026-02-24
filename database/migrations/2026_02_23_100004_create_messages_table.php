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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('conversation_id')->constrained('conversations')->cascadeOnDelete();
            $table->string('sender_type'); // customer / agent / bot
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('message_type')->default('text'); // text / image / document
            $table->text('message_body');
            $table->string('meta_message_id')->nullable();
            $table->string('status')->default('sent'); // sent / delivered / read
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
