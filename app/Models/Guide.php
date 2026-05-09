<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guide extends Model
{
    protected $fillable = [
        'name',
        'experience_years',
        'bio',
        'contact_number',
        'profile_image_url',
        'languages_spoken',
    ];

    public function treks()
    {
        return $this->belongsToMany(Trek::class);
    }
}
