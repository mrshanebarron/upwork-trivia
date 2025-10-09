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
        Schema::create('gift_card_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gift_card_id')->constrained()->onDelete('cascade');
            $table->integer('attempt_number')->default(1);
            $table->enum('status', ['success', 'failed', 'pending_retry']);
            $table->text('error_message')->nullable();
            $table->json('api_response')->nullable();
            $table->timestamp('attempted_at')->useCurrent();

            $table->index('gift_card_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_card_delivery_logs');
    }
};
