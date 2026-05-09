<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $fillable = [
        'type',
        'capacity',
        'plate_number',
        'driver_name',
        'driver_contact',
    ];

    public function treks()
    {
        return $this->belongsToMany(Trek::class);
    }
}
