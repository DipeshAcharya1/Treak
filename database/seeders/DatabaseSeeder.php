<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $token = Str::random(60);

        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'api_token' => hash('sha256', $token),
                'role' => 'user',
            ]
        );

        $adminToken = Str::random(60);
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'),
                'api_token' => hash('sha256', $adminToken),
                'role' => 'admin',
            ]
        );

        $user->treks()->updateOrCreate(
            ['title' => 'First Trek'],
            [
                'description' => 'A sample trek to get started with the Treak backend.',
                'date' => now()->addDays(7)->toDateString(),
            ]
        );
    }
}
