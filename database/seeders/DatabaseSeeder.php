<?php

namespace Database\Seeders;

use App\Models\Trek;
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
        // ---------------------------------------------------------
        // 1. Regular test user (test@example.com / password)
        // ---------------------------------------------------------
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'api_token' => hash('sha256', 'test-token-for-seeding'),
                'role' => 'user',
            ]
        );

        $user->treks()->updateOrCreate(
            ['title' => 'Everest Base Camp Trek'],
            [
                'description' => 'The classic trek to the foot of the world\'s highest peak. A journey through Sherpa villages, ancient monasteries, and breathtaking Himalayan scenery.',
                'date' => now()->addDays(14)->toDateString(),
            ]
        );

        $user->treks()->updateOrCreate(
            ['title' => 'Annapurna Circuit'],
            [
                'description' => 'A legendary trail circling the Annapurna massif, crossing the Thorong La pass at 5,416m with stunning views of Dhaulagiri and Manaslu.',
                'date' => now()->addDays(30)->toDateString(),
            ]
        );

        $user->treks()->updateOrCreate(
            ['title' => 'Poon Hill Sunrise Trek'],
            [
                'description' => 'A short and rewarding trek to one of the best sunrise viewpoints in Nepal, with panoramic views of Annapurna and Dhaulagiri ranges.',
                'date' => now()->addDays(7)->toDateString(),
            ]
        );

        // ---------------------------------------------------------
        // 2. Admin user (admin@example.com / admin123)
        // ---------------------------------------------------------
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('admin123'),
                'api_token' => hash('sha256', 'admin-token-for-seeding'),
                'role' => 'admin',
            ]
        );

        $admin->treks()->updateOrCreate(
            ['title' => 'Langtang Valley Trek'],
            [
                'description' => 'Explore the beautiful Langtang Valley, known as the valley of glaciers, with its rich biodiversity and Tamang heritage.',
                'date' => now()->addDays(21)->toDateString(),
            ]
        );

        // ---------------------------------------------------------
        // 3. Extra regular user for testing multi-user scenarios
        // ---------------------------------------------------------
        $user2 = User::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Trekker',
                'password' => bcrypt('password'),
                'api_token' => hash('sha256', 'jane-token-for-seeding'),
                'role' => 'user',
            ]
        );

        $user2->treks()->updateOrCreate(
            ['title' => 'Mardi Himal Trek'],
            [
                'description' => 'A hidden gem offering stunning close-up views of Mardi Himal and Machhapuchhre (Fishtail) through pristine rhododendron forests.',
                'date' => now()->addDays(10)->toDateString(),
            ]
        );

        $user2->treks()->updateOrCreate(
            ['title' => 'Upper Mustang Trek'],
            [
                'description' => 'Journey into the former Kingdom of Lo, a restricted area with dramatic desert-like landscapes, ancient caves, and Tibetan Buddhist culture.',
                'date' => now()->addDays(45)->toDateString(),
            ]
        );
    }
}
