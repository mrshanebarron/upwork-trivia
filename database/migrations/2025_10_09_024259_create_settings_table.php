<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type', 50)->default('string'); // string, boolean, integer, json
            $table->string('description', 500)->nullable();
            $table->timestamps();

            $table->index('key');
        });

        // Insert default settings
        DB::table('settings')->insert([
            ['key' => 'contest_active', 'value' => 'true', 'type' => 'boolean', 'description' => 'Master switch for contest system', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'question_rotation_start_hour', 'value' => '10', 'type' => 'integer', 'description' => 'Earliest hour for question rotation (24h format)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'question_rotation_end_hour', 'value' => '20', 'type' => 'integer', 'description' => 'Latest hour for question rotation (24h format)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'require_geolocation', 'value' => 'false', 'type' => 'boolean', 'description' => 'Require geolocation for submissions', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'max_daily_submissions_per_ip', 'value' => '3', 'type' => 'integer', 'description' => 'Max submissions per IP per day (across all questions)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'enable_captcha', 'value' => 'true', 'type' => 'boolean', 'description' => 'Enable CAPTCHA on answer submission', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admin_alert_email', 'value' => 'rick@example.com', 'type' => 'string', 'description' => 'Email for admin alerts', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'admin_alert_sms', 'value' => '', 'type' => 'string', 'description' => 'Phone number for SMS alerts (optional)', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'low_budget_threshold', 'value' => '100', 'type' => 'integer', 'description' => 'Alert when prize pool remaining falls below this amount', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'description' => 'Put contest in maintenance mode', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
