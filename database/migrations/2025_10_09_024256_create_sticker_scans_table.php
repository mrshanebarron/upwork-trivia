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
        Schema::create('sticker_scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sticker_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->decimal('scan_latitude', 10, 8)->nullable();
            $table->decimal('scan_longitude', 11, 8)->nullable();
            $table->timestamp('scanned_at')->useCurrent();

            $table->index('sticker_id');
            $table->index('user_id');
            $table->index('scanned_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sticker_scans');
    }
};
