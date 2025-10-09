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
        Schema::create('gift_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('winner_id')->constrained()->onDelete('cascade');
            $table->string('order_id')->unique(); // Tremendous order ID
            $table->string('reward_id')->unique(); // Tremendous reward ID
            $table->decimal('amount', 8, 2);
            $table->string('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'delivered', 'redeemed', 'failed'])->default('pending');
            $table->text('redemption_link')->nullable();
            $table->string('delivery_method', 50)->default('EMAIL');
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('redeemed_at')->nullable();
            $table->string('provider', 50)->default('tremendous');
            $table->json('provider_response')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('winner_id');
            $table->index('status');
            $table->index('delivered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
