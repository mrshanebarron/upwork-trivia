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
        Schema::create('bag_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trivia_code_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('answer');
            $table->string('ip_address');
            $table->text('user_agent')->nullable();
            $table->timestamps();

            // Index for querying submissions by code
            $table->index('trivia_code_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_submissions');
    }
};
