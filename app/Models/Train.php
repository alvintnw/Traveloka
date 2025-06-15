<?php

 namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    use HasFactory;

    protected $fillable = [
        'origin_station_id',
        'destination_station_id',
        'train_name',
        'train_number',
        'departure_time',
        'arrival_time',
        'price',
        'available_seats',
    ];

    protected $casts = [
        'departure_time' => 'datetime',
        'arrival_time' => 'datetime',
    ];

    public function originStation()
    {
        return $this->belongsTo(Station::class, 'origin_station_id');
    }

    public function destinationStation()
    {
        return $this->belongsTo(Station::class, 'destination_station_id');
    }
}