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
        Schema::create('daily_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date')->unique();
            $table->integer('total_scans')->default(0);
            $table->integer('total_submissions')->default(0);
            $table->integer('total_winners')->default(0);
            $table->decimal('total_spent', 10, 2)->default(0);
            $table->decimal('conversion_rate', 5, 2)->default(0); // submissions/scans * 100
            $table->decimal('avg_submissions_per_question', 8, 2)->default(0);
            $table->timestamps();

            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_analytics');
    }
};
