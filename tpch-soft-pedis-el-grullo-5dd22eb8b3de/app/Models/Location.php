<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use hasFactory, SoftDeletes;

    protected $fillable = [

        'citizen_id', //got to know who's calling
        'latitude', //latitude in ddmm.mmmmmm format
        'latitude_direction', // N/S indicator
        'longitude', // longitude in ddmm.mmmmmm
        'longitude_direction', // W/E indicator
        'date', //date
        'time', //<UTC TIME>
        'altitude', //MSL altitude
        'speed', //speed over ground in knots
        'course', //course in degrees
        'time_interval' // range is from 0-255, unit is second
    ];

    public function citizen(){
        //associate Citizens table with location table
        //Logic being that very citizen has a location
        return $this->belongsTo(Citizen::class);
    }

}
