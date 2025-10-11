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
        Schema::table('submissions', function (Blueprint $table) {
            // Drop unique constraint to allow guests to submit
            $table->dropUnique(['user_id', 'daily_question_id']);

            // Drop foreign key constraint
            $table->dropForeign(['user_id']);

            // Make user_id nullable
            $table->foreignId('user_id')->nullable()->change()->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            // Make user_id required again
            $table->dropForeign(['user_id']);
            $table->foreignId('user_id')->nullable(false)->change()->constrained()->onDelete('cascade');

            // Recreate unique constraint
            $table->unique(['user_id', 'daily_question_id']);
        });
    }
};
