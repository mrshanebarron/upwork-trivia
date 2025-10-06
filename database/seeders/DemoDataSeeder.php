<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo trivia code
        $triviaCode = \App\Models\TriviaCode::create([
            'code' => '1234',
            'title' => 'General Knowledge Quiz',
            'description' => 'Test your knowledge with these fun trivia questions!',
            'is_active' => true,
        ]);

        // Add answers
        $triviaCode->answers()->createMany([
            ['order' => 1, 'answer' => 'The capital of France is Paris'],
            ['order' => 2, 'answer' => 'The Great Wall of China is approximately 13,000 miles long'],
            ['order' => 3, 'answer' => 'Mount Everest is the tallest mountain at 29,032 feet'],
            ['order' => 4, 'answer' => 'The Pacific Ocean is the largest ocean on Earth'],
            ['order' => 5, 'answer' => 'The speed of light is 186,282 miles per second'],
        ]);

        // Create demo ad boxes
        \App\Models\AdBox::create([
            'title' => 'Visit Our Website',
            'url' => 'https://example.com',
            'html_content' => '<div class="bg-blue-500 text-white p-4 rounded"><h3 class="font-bold">Visit Our Website</h3><p>Click here to learn more</p></div>',
            'order' => 1,
            'is_active' => true,
        ]);

        \App\Models\AdBox::create([
            'title' => 'Special Offer',
            'url' => 'https://example.com/offer',
            'html_content' => '<div class="bg-green-500 text-white p-4 rounded"><h3 class="font-bold">Special Offer</h3><p>Get 20% off today</p></div>',
            'order' => 2,
            'is_active' => true,
        ]);
    }
}
