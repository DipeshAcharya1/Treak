<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trek extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'price',
        'duration_days',
        'difficulty',
        'location',
        'image_url',
        'max_altitude',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function itineraries()
    {
        return $this->hasMany(Itinerary::class);
    }

    public function guides()
    {
        return $this->belongsToMany(Guide::class);
    }

    public function vehicles()
    {
        return $this->belongsToMany(Vehicle::class);
    }
}
