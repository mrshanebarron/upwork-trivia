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
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daily_question_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('selected_answer', ['A', 'B', 'C', 'D']);
            $table->boolean('is_correct');
            $table->string('ip_address', 45)->nullable();
            $table->string('device_fingerprint')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->foreignId('sticker_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('submitted_at', 6)->useCurrent(); // Microsecond precision for tie-breaking
            $table->unsignedInteger('random_tiebreaker'); // Random 1-1000000 for tie-breaking
            $table->timestamp('created_at')->useCurrent();

            $table->index('daily_question_id');
            $table->index('user_id');
            $table->index('submitted_at');
            $table->index('is_correct');
            $table->index(['ip_address', 'daily_question_id']);
            $table->index(['device_fingerprint', 'daily_question_id']);
            $table->unique(['user_id', 'daily_question_id']); // One submission per user per question
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};
