<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    protected $fillable = [
        'trek_id',
        'day_number',
        'title',
        'description',
    ];

    public function trek()
    {
        return $this->belongsTo(Trek::class);
    }
}
