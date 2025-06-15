<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'description',
        'price_per_night',
        'image_url',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}