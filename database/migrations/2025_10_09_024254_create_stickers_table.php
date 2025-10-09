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
        Schema::create('stickers', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code', 20)->unique();
            $table->string('location_name')->nullable();
            $table->string('property_name')->nullable();
            $table->unsignedBigInteger('property_manager_id')->nullable(); // Future: multi-client tracking
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->date('installed_at')->nullable();
            $table->enum('status', ['active', 'inactive', 'damaged', 'removed'])->default('active');
            $table->integer('scan_count')->default(0);
            $table->timestamps();

            $table->index('unique_code');
            $table->index('status');
            $table->index('property_manager_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stickers');
    }
};
