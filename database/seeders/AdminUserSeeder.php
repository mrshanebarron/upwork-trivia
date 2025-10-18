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
            ['email' => 'admin@trivia.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('PassW0rd'),
                'birthdate' => '1980-01-01',
                'is_admin' => true,
                'show_name_publicly' => false,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Admin user created/updated:');
        $this->command->info('   Email: admin@trivia.test');
        $this->command->info('   Password: PassW0rd');
        $this->command->info('   Panel URL: /admin');
    }
}
