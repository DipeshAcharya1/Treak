<?php

namespace Database\Factories;

use App\Models\Trek;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trek>
 */
class TrekFactory extends Factory
{
    protected $model = Trek::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $trekNames = [
            'Everest Base Camp Trek',
            'Annapurna Circuit',
            'Langtang Valley Trek',
            'Manaslu Circuit Trek',
            'Upper Mustang Trek',
            'Gokyo Lakes Trek',
            'Poon Hill Sunrise Trek',
            'Mardi Himal Trek',
            'Kanchenjunga Base Camp',
            'Makalu Base Camp Trek',
            'Rara Lake Trek',
            'Dhaulagiri Circuit',
            'Tilicho Lake Trek',
            'Pikey Peak Trek',
            'Tamang Heritage Trail',
            'Three Passes Trek',
            'Island Peak Climb',
            'Mohare Danda Trek',
            'Khopra Ridge Trek',
            'Sandakphu Trek',
        ];

        $descriptions = [
            'A breathtaking journey through towering peaks and serene valleys.',
            'Experience the raw beauty of the Himalayan wilderness on this iconic trail.',
            'Walk through ancient villages, lush forests, and dramatic mountain passes.',
            'A challenging adventure rewarded with panoramic views of snow-capped giants.',
            'Discover hidden monasteries and untouched landscapes on this remote route.',
            'Trek through rhododendron forests to reach crystal-clear alpine lakes.',
            'An unforgettable sunrise over the majestic Annapurna and Dhaulagiri ranges.',
            'Cross suspension bridges and glacial moraines on this epic high-altitude trail.',
            'A moderate trek perfect for those seeking mountain views without extreme difficulty.',
            'Immerse yourself in local culture while trekking through terraced hillsides.',
        ];

        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement($trekNames),
            'description' => fake()->randomElement($descriptions),
            'date' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
        ];
    }
}
