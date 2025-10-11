<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'rick@trivia.test'],
            [
                'name' => 'Rick (Admin)',
                'password' => Hash::make('password'),
                'birthdate' => '1980-01-01', // Rick is 45, well over 18
                'is_admin' => true,
                'show_name_publicly' => false,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user created/updated:');
        $this->command->info('   Email: rick@trivia.test');
        $this->command->info('   Password: password');
        $this->command->info('   Panel URL: /admin');
    }
}
