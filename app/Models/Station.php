<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city',
        'code',
    ];

    public function departingTrains()
    {
        return $this->hasMany(Train::class, 'origin_station_id');
    }

    public function arrivingTrains()
    {
        return $this->hasMany(Train::class, 'destination_station_id');
    }
}