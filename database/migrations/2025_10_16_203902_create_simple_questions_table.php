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
        Schema::create('simple_questions', function (Blueprint $table) {
            $table->id();
            $table->text('question');
            $table->text('answer');
            $table->date('active_date')->index(); // When this question is active
            $table->integer('display_order')->default(1); // 1-5 for daily display
            $table->boolean('is_active')->default(true);
            $table->string('period_type')->default('daily'); // 'daily' or 'weekly'
            $table->timestamps();

            // Index for quick lookups
            $table->index(['active_date', 'is_active', 'display_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simple_questions');
    }
};
