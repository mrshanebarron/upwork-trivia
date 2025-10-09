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
        Schema::create('winners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('daily_question_id')->constrained()->onDelete('cascade');
            $table->foreignId('submission_id')->nullable()->constrained()->onDelete('cascade');
            $table->decimal('prize_amount', 8, 2);
            $table->timestamp('created_at')->useCurrent();

            $table->index('user_id');
            $table->index('daily_question_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('winners');
    }
};
