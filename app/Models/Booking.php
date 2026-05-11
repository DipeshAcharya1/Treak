<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'trek_id',
        'booking_date',
        'number_of_people',
        'total_price',
        'status',
        'payment_status',
        'payment_method',
        'transaction_id',
    ];

    protected $casts = [
        'booking_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trek()
    {
        return $this->belongsTo(Trek::class);
    }
}
