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
        Schema::table('daily_questions', function (Blueprint $table) {
            $table->string('source')->nullable()->after('explanation'); // 'manual', 'trivia_api', etc.
            $table->string('external_id')->nullable()->after('source'); // API question ID for deduplication
            $table->string('category')->nullable()->after('external_id'); // API category
            $table->string('difficulty')->nullable()->after('category'); // easy, medium, hard

            $table->index('source');
            $table->index('external_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('daily_questions', function (Blueprint $table) {
            $table->dropIndex(['source']);
            $table->dropIndex(['external_id']);
            $table->dropColumn(['source', 'external_id', 'category', 'difficulty']);
        });
    }
};
