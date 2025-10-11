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
        // Add site content settings to the existing settings table
        DB::table('settings')->insert([
            [
                'key' => 'about_content',
                'value' => '<h2>Welcome to Poop Bag Trivia!</h2><p>Edit this content from the admin panel.</p>',
                'type' => 'string',
                'description' => 'HTML content for the About page',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'termly_terms_code',
                'value' => '',
                'type' => 'string',
                'description' => 'Termly.io embed code for Terms of Service page',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'key' => 'termly_privacy_code',
                'value' => '',
                'type' => 'string',
                'description' => 'Termly.io embed code for Privacy Policy page',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove the site content settings
        DB::table('settings')->whereIn('key', [
            'about_content',
            'termly_terms_code',
            'termly_privacy_code',
        ])->delete();
    }
};
