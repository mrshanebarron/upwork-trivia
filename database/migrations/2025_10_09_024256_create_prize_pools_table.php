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
        Schema::create('prize_pools', function (Blueprint $table) {
            $table->id();
            $table->date('month')->unique(); // 2025-10-01
            $table->decimal('budget', 10, 2)->default(0);
            $table->decimal('spent', 10, 2)->default(0);
            // Note: 'remaining' as computed column (budget - spent) will be calculated in app layer
            $table->unsignedBigInteger('sponsor_id')->nullable(); // Future: Purina sponsorship
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('month');
            $table->index('sponsor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prize_pools');
    }
};
