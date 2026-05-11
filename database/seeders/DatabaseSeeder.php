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
        // 1. Create Admins and Users
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

        $testUser = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
                'api_token' => hash('sha256', 'test-token-for-seeding'),
                'role' => 'user',
            ]
        );

        $jane = User::updateOrCreate(
            ['email' => 'jane@example.com'],
            [
                'name' => 'Jane Trekker',
                'password' => bcrypt('password'),
                'api_token' => hash('sha256', 'jane-token-for-seeding'),
                'role' => 'user',
            ]
        );

        // ---------------------------------------------------------
        // 2. Create Guides
        // ---------------------------------------------------------
        $guides = [
            [
                'name' => 'Tenzing Norgay',
                'experience_years' => 15,
                'bio' => 'Veteran Himalayan guide with over 20 successful Everest summits.',
                'contact_number' => '+977-9841234567',
                'languages_spoken' => 'English, Nepali, Tibetan',
                'profile_image_url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=200&h=200&fit=crop'
            ],
            [
                'name' => 'Pasang Lhamu',
                'experience_years' => 8,
                'bio' => 'Expert in Annapurna and Langtang regions, specialized in flora and fauna.',
                'contact_number' => '+977-9851234568',
                'languages_spoken' => 'English, Nepali, French',
                'profile_image_url' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=200&h=200&fit=crop'
            ],
            [
                'name' => 'Kami Rita',
                'experience_years' => 20,
                'bio' => 'Record holder for most Everest ascents. Safety is my priority.',
                'contact_number' => '+977-9861234569',
                'languages_spoken' => 'English, Nepali, Hindi',
                'profile_image_url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=200&h=200&fit=crop'
            ]
        ];

        $guideModels = [];
        foreach ($guides as $g) {
            $guideModels[] = \App\Models\Guide::create($g);
        }

        // ---------------------------------------------------------
        // 3. Create Vehicles
        // ---------------------------------------------------------
        $vehicles = [
            [
                'type' => '4x4 Jeep',
                'capacity' => 7,
                'plate_number' => 'BA 3 PA 1234',
                'driver_name' => 'Ram Bahadur',
                'driver_contact' => '+977-9801234567'
            ],
            [
                'type' => 'Mini Bus',
                'capacity' => 15,
                'plate_number' => 'LU 2 CHA 5678',
                'driver_name' => 'Shyam Thapa',
                'driver_contact' => '+977-9811234567'
            ],
            [
                'type' => 'Private Car',
                'capacity' => 4,
                'plate_number' => 'BA 1 YA 9012',
                'driver_name' => 'Hari Rai',
                'driver_contact' => '+977-9821234567'
            ]
        ];

        $vehicleModels = [];
        foreach ($vehicles as $v) {
            $vehicleModels[] = \App\Models\Vehicle::create($v);
        }

        // ---------------------------------------------------------
        // 4. Create Treks and associate Guides/Vehicles
        // ---------------------------------------------------------
        $treks = [
            [
                'title' => 'Everest Base Camp Trek',
                'description' => 'The classic trek to the foot of the world\'s highest peak. Experience Sherpa culture and stunning mountain vistas.',
                'price' => 1450.00,
                'location' => 'Solu-Khumbu, Nepal',
                'duration_days' => 14,
                'difficulty' => 'difficult',
                'max_altitude' => 5364,
                'image_url' => 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?q=80&w=800'
            ],
            [
                'title' => 'Annapurna Circuit',
                'description' => 'A diverse trek crossing the Thorong La pass. Beautiful landscapes from lush forests to arid high plateaus.',
                'price' => 1200.00,
                'location' => 'Annapurna Region, Nepal',
                'duration_days' => 18,
                'difficulty' => 'difficult',
                'max_altitude' => 5416,
                'image_url' => 'https://images.unsplash.com/photo-1585016495481-91613a3f5841?q=80&w=800'
            ],
            [
                'title' => 'Poon Hill Sunrise Trek',
                'description' => 'A short and accessible trek offering one of the best sunrise views in the Himalayas.',
                'price' => 450.00,
                'location' => 'Ghorepani, Nepal',
                'duration_days' => 5,
                'difficulty' => 'easy',
                'max_altitude' => 3210,
                'image_url' => 'https://images.unsplash.com/photo-1520209268518-aec60b8bb5cb?q=80&w=800'
            ],
            [
                'title' => 'Langtang Valley Trek',
                'description' => 'Explore the "Valley of Glaciers" near Kathmandu. Beautiful Tamang culture and mountain views.',
                'price' => 850.00,
                'location' => 'Langtang, Nepal',
                'duration_days' => 10,
                'difficulty' => 'moderate',
                'max_altitude' => 4984,
                'image_url' => 'https://images.unsplash.com/photo-1582234372722-50d7ccc30e5a?q=80&w=800'
            ]
        ];

        foreach ($treks as $index => $t) {
            $trek = $admin->treks()->create($t);
            
            // Assign some guides and vehicles
            $trek->guides()->attach([$guideModels[$index % 3]->id]);
            $trek->vehicles()->attach([$vehicleModels[$index % 3]->id]);

            // Add some itineraries
            for ($i = 1; $i <= 3; $i++) {
                $trek->itineraries()->create([
                    'day_number' => $i,
                    'title' => "Day $i: Adventure Starts",
                    'description' => "Hiking through beautiful trails to the next stop of our journey.",
                    'accommodation' => "Local Tea House",
                    'meals' => "Breakfast, Lunch, Dinner"
                ]);
            }

            // Add some reviews
            \App\Models\Review::create([
                'user_id' => $testUser->id,
                'trek_id' => $trek->id,
                'rating' => 5,
                'comment' => "Amazing experience! The guide was very knowledgeable."
            ]);

            \App\Models\Review::create([
                'user_id' => $jane->id,
                'trek_id' => $trek->id,
                'rating' => 4,
                'comment' => "Beautiful views, but quite challenging."
            ]);
        }

        // ---------------------------------------------------------
        // 5. Create Bookings
        // ---------------------------------------------------------
        $ebc = \App\Models\Trek::where('title', 'Everest Base Camp Trek')->first();
        $annapurna = \App\Models\Trek::where('title', 'Annapurna Circuit')->first();

        \App\Models\Booking::create([
            'user_id' => $testUser->id,
            'trek_id' => $ebc->id,
            'booking_date' => now()->addDays(20)->toDateString(),
            'number_of_people' => 2,
            'total_price' => $ebc->price * 2,
            'status' => 'confirmed'
        ]);

        \App\Models\Booking::create([
            'user_id' => $jane->id,
            'trek_id' => $annapurna->id,
            'booking_date' => now()->addDays(15)->toDateString(),
            'number_of_people' => 1,
            'total_price' => $annapurna->price,
            'status' => 'pending'
        ]);

        \App\Models\Booking::create([
            'user_id' => $testUser->id,
            'trek_id' => $annapurna->id,
            'booking_date' => now()->subDays(5)->toDateString(),
            'number_of_people' => 3,
            'total_price' => $annapurna->price * 3,
            'status' => 'confirmed'
        ]);
    }
}
