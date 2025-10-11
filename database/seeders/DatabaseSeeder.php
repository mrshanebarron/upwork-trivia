<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\DailyQuestion;
use App\Models\Sticker;
use App\Models\Submission;
use App\Models\Winner;
use App\Models\GiftCard;
use App\Models\PrizePool;
use App\Models\StickerScan;
use App\Models\AdBox;
use App\Models\TriviaCode;
use App\Models\Answer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user for Rick
        $this->call(AdminUserSeeder::class);

        $this->command->info('Seeding Users...');
        // Create regular test users
        $testUser = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'birthdate' => '1990-01-01',
        ]);

        $users = User::factory(20)->create();
        $allUsers = collect([$testUser])->merge($users);

        $this->command->info('Seeding Prize Pools...');
        // Create prize pools
        PrizePool::create([
            'month' => now()->startOfMonth(),
            'budget' => 500.00,
            'spent' => 150.00,
            'is_active' => true,
        ]);

        PrizePool::create([
            'month' => now()->addMonth()->startOfMonth(),
            'budget' => 600.00,
            'spent' => 0.00,
            'is_active' => false,
        ]);

        $this->command->info('Seeding Stickers...');
        // Create stickers at various locations
        $locations = [
            ['name' => 'Dog Park North Entrance', 'property' => 'Sunset Hills Apartments'],
            ['name' => 'Main Dog Station', 'property' => 'Riverside Community'],
            ['name' => 'South Park Entrance', 'property' => 'Green Valley Estates'],
            ['name' => 'Walking Trail Station', 'property' => 'Oakwood Manor'],
            ['name' => 'Pet Area Station 1', 'property' => 'The Meadows'],
            ['name' => 'Pet Area Station 2', 'property' => 'The Meadows'],
            ['name' => 'Central Park Dog Run', 'property' => 'Downtown Plaza'],
            ['name' => 'North Dog Station', 'property' => 'Lakeview Condos'],
        ];

        $stickers = collect();
        foreach ($locations as $location) {
            $sticker = Sticker::create([
                'location_name' => $location['name'],
                'property_name' => $location['property'],
                'latitude' => fake()->latitude(34, 35),
                'longitude' => fake()->longitude(-118, -117),
                'installed_at' => now()->subDays(rand(1, 30)),
                'status' => 'active',
                'scan_count' => rand(5, 150),
            ]);
            $stickers->push($sticker);
        }

        $this->command->info('Seeding Daily Questions...');
        // Create daily questions (past, present, future)
        $questions = [
            // Past questions with winners
            [
                'question_text' => 'What breed of dog is known for its distinctive blue-black tongue?',
                'option_a' => 'Siberian Husky',
                'option_b' => 'Chow Chow',
                'option_c' => 'Alaskan Malamute',
                'option_d' => 'Akita',
                'correct_answer' => 'B',
                'explanation' => 'The Chow Chow is famous for its blue-black tongue, a unique characteristic of the breed.',
                'prize_amount' => 10.00,
                'scheduled_for' => now()->subDays(5)->setHour(14)->setMinute(0),
                'is_active' => false,
                'category' => 'Dog Breeds',
                'difficulty' => 'medium',
            ],
            [
                'question_text' => 'Which dog breed holds the record for the fastest land speed?',
                'option_a' => 'Whippet',
                'option_b' => 'Saluki',
                'option_c' => 'Greyhound',
                'option_d' => 'Afghan Hound',
                'correct_answer' => 'C',
                'explanation' => 'Greyhounds can reach speeds up to 45 mph, making them the fastest dog breed.',
                'prize_amount' => 10.00,
                'scheduled_for' => now()->subDays(4)->setHour(12)->setMinute(30),
                'is_active' => false,
                'category' => 'Dog Facts',
                'difficulty' => 'easy',
            ],
            [
                'question_text' => 'What is the smallest dog breed recognized by major kennel clubs?',
                'option_a' => 'Yorkshire Terrier',
                'option_b' => 'Pomeranian',
                'option_c' => 'Chihuahua',
                'option_d' => 'Toy Poodle',
                'correct_answer' => 'C',
                'explanation' => 'The Chihuahua is officially the smallest dog breed, typically weighing 2-6 pounds.',
                'prize_amount' => 10.00,
                'scheduled_for' => now()->subDays(3)->setHour(16)->setMinute(15),
                'is_active' => false,
                'category' => 'Dog Breeds',
                'difficulty' => 'easy',
            ],

            // Today's active question
            [
                'question_text' => 'Which dog breed was originally bred to hunt badgers in Germany?',
                'option_a' => 'Beagle',
                'option_b' => 'Dachshund',
                'option_c' => 'Jack Russell Terrier',
                'option_d' => 'Cairn Terrier',
                'correct_answer' => 'B',
                'explanation' => 'Dachshund means "badger dog" in German, and they were specifically bred for this purpose.',
                'prize_amount' => 10.00,
                'scheduled_for' => now()->subHours(2), // 2 hours ago to ensure it's active
                'is_active' => true,
                'category' => 'Dog History',
                'difficulty' => 'medium',
            ],

            // Future questions
            [
                'question_text' => 'What percentage of dog owners say they talk to their dogs like they\'re human?',
                'option_a' => 'About 50%',
                'option_b' => 'About 70%',
                'option_c' => 'About 90%',
                'option_d' => 'About 30%',
                'correct_answer' => 'C',
                'explanation' => 'Studies show approximately 90% of dog owners talk to their dogs as if they were human!',
                'prize_amount' => 10.00,
                'scheduled_for' => now()->addDay()->setHour(13)->setMinute(45),
                'is_active' => false,
                'category' => 'Dog Facts',
                'difficulty' => 'medium',
            ],
            [
                'question_text' => 'Which breed is known as the "King of Terriers"?',
                'option_a' => 'Irish Terrier',
                'option_b' => 'Airedale Terrier',
                'option_c' => 'Scottish Terrier',
                'option_d' => 'Bull Terrier',
                'correct_answer' => 'B',
                'explanation' => 'The Airedale Terrier is called the "King of Terriers" because it is the largest of all terrier breeds.',
                'prize_amount' => 10.00,
                'scheduled_for' => now()->addDays(2)->setHour(11)->setMinute(30),
                'is_active' => false,
                'category' => 'Dog Breeds',
                'difficulty' => 'hard',
            ],
        ];

        $createdQuestions = collect();
        foreach ($questions as $q) {
            $question = DailyQuestion::create($q);
            $createdQuestions->push($question);
        }

        $this->command->info('Seeding Submissions and Winners...');
        // Create submissions for past questions and award winners
        $pastQuestions = $createdQuestions->filter(fn($q) => $q->scheduled_for->isPast() && !$q->is_active);

        foreach ($pastQuestions as $question) {
            // Create multiple submissions
            $submissionUsers = $allUsers->random(rand(5, 12));
            $firstCorrect = null;

            foreach ($submissionUsers as $index => $user) {
                $isCorrect = fake()->boolean(40); // 40% chance of correct answer

                // First correct submission wins
                if ($isCorrect && !$firstCorrect) {
                    $firstCorrect = $user;
                }

                Submission::create([
                    'user_id' => $user->id,
                    'daily_question_id' => $question->id,
                    'sticker_id' => $stickers->random()->id,
                    'selected_answer' => $isCorrect ? $question->correct_answer : ['A', 'B', 'C', 'D'][rand(0, 3)],
                    'is_correct' => $isCorrect,
                    'submitted_at' => $question->scheduled_for->addMinutes(rand(1, 180)),
                    'ip_address' => fake()->ipv4(),
                    'latitude' => fake()->latitude(34, 35),
                    'longitude' => fake()->longitude(-118, -117),
                    'random_tiebreaker' => rand(1, 1000000),
                ]);
            }

            // Create winner record if there was a correct submission
            if ($firstCorrect) {
                // Get the first correct submission for this question
                $winningSubmission = Submission::where('daily_question_id', $question->id)
                    ->where('user_id', $firstCorrect->id)
                    ->where('is_correct', true)
                    ->first();

                $winner = Winner::create([
                    'user_id' => $firstCorrect->id,
                    'daily_question_id' => $question->id,
                    'submission_id' => $winningSubmission?->id,
                    'prize_amount' => $question->prize_amount,
                ]);

                // Create gift card
                GiftCard::create([
                    'user_id' => $firstCorrect->id,
                    'winner_id' => $winner->id,
                    'order_id' => 'ORD-' . strtoupper(uniqid()),
                    'reward_id' => 'RWD-' . strtoupper(uniqid()),
                    'amount' => $question->prize_amount,
                    'provider' => 'tremendous',
                    'redemption_link' => 'https://tremendous.com/redeem/' . uniqid(),
                    'status' => 'delivered',
                    'delivered_at' => $question->scheduled_for->addMinutes(rand(10, 60)),
                ]);

                $question->update(['winner_id' => $firstCorrect->id]);
            }
        }

        // Create submissions for today's active question (no winner yet)
        $activeQuestion = $createdQuestions->firstWhere('is_active', true);
        if ($activeQuestion) {
            $todayUsers = $allUsers->random(rand(3, 8));
            foreach ($todayUsers as $user) {
                Submission::create([
                    'user_id' => $user->id,
                    'daily_question_id' => $activeQuestion->id,
                    'sticker_id' => $stickers->random()->id,
                    'selected_answer' => ['A', 'C', 'D'][rand(0, 2)], // Wrong answers only
                    'is_correct' => false,
                    'submitted_at' => $activeQuestion->scheduled_for->addMinutes(rand(5, 120)),
                    'ip_address' => fake()->ipv4(),
                    'latitude' => fake()->latitude(34, 35),
                    'longitude' => fake()->longitude(-118, -117),
                    'random_tiebreaker' => rand(1, 1000000),
                ]);
            }
        }

        $this->command->info('Seeding Sticker Scans...');
        // Create sticker scans
        foreach ($stickers as $sticker) {
            for ($i = 0; $i < rand(10, 50); $i++) {
                StickerScan::create([
                    'sticker_id' => $sticker->id,
                    'user_id' => rand(0, 1) ? $allUsers->random()->id : null,
                    'scanned_at' => now()->subDays(rand(1, 30)),
                    'latitude' => $sticker->latitude + (rand(-10, 10) / 10000),
                    'longitude' => $sticker->longitude + (rand(-10, 10) / 10000),
                    'ip_address' => fake()->ipv4(),
                    'user_agent' => fake()->userAgent(),
                ]);
            }
        }

        $this->command->info('Seeding Ad Boxes...');
        // Create advertisement boxes
        AdBox::create([
            'title' => 'Premium Dog Food',
            'url' => 'https://example.com/dog-food',
            'html_content' => '<div class="ad-banner"><h3>Premium Dog Food</h3><p>Your best friend deserves the best nutrition!</p></div>',
            'is_active' => true,
            'order' => 1,
            'click_count' => rand(50, 200),
        ]);

        AdBox::create([
            'title' => 'Local Pet Services',
            'url' => 'https://example.com/pet-services',
            'html_content' => '<div class="ad-banner"><h3>Professional Pet Care</h3><p>Grooming, Training, and Boarding Services</p></div>',
            'is_active' => true,
            'order' => 2,
            'click_count' => rand(30, 150),
        ]);

        $this->command->info('Seeding Trivia Codes (Old System)...');
        // Create trivia codes for the old bag system
        $triviaCode = TriviaCode::create([
            'code' => '1234',
            'title' => 'Dog Care Basics',
            'description' => 'Essential tips for new dog owners',
            'is_active' => true,
        ]);

        Answer::create([
            'trivia_code_id' => $triviaCode->id,
            'answer' => 'Dogs need daily exercise and mental stimulation',
            'order' => 1,
        ]);

        Answer::create([
            'trivia_code_id' => $triviaCode->id,
            'answer' => 'Always provide fresh water and quality food',
            'order' => 2,
        ]);

        Answer::create([
            'trivia_code_id' => $triviaCode->id,
            'answer' => 'Regular vet check-ups are essential for health',
            'order' => 3,
        ]);

        $this->command->info('✅ Database seeded successfully with comprehensive test data!');
        $this->command->newLine();
        $this->command->info('Summary:');
        $this->command->info('- Users: ' . User::count());
        $this->command->info('- Daily Questions: ' . DailyQuestion::count());
        $this->command->info('- Stickers: ' . Sticker::count());
        $this->command->info('- Submissions: ' . Submission::count());
        $this->command->info('- Winners: ' . Winner::count());
        $this->command->info('- Gift Cards: ' . GiftCard::count());
        $this->command->info('- Prize Pools: ' . PrizePool::count());
        $this->command->info('- Sticker Scans: ' . StickerScan::count());
    }
}
