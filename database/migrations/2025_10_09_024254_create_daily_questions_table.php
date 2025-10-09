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
        Schema::create('daily_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question_text');
            $table->string('option_a', 500);
            $table->string('option_b', 500);
            $table->string('option_c', 500);
            $table->string('option_d', 500);
            $table->enum('correct_answer', ['A', 'B', 'C', 'D']);
            $table->text('explanation')->nullable(); // "Did you know..." educational content
            $table->decimal('prize_amount', 8, 2)->default(10.00);
            $table->timestamp('scheduled_for');
            $table->foreignId('winner_id')->nullable()->constrained('users')->onDelete('set null');
            $table->boolean('is_active')->default(false);
            $table->integer('submission_count')->default(0);
            $table->integer('correct_submission_count')->default(0);
            $table->timestamps();

            $table->index('scheduled_for');
            $table->index('is_active');
            $table->index('winner_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_questions');
    }
};
