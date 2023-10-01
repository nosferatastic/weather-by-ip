<?php

namespace Paule\WeatherByIP\Models;

use Illuminate\Database\Eloquent\Model;

use PaulE\WeatherByIP\Models\Forecast;

class LocationLookup extends Model
{

    protected $fillable = [
        'ip',
        'location',
        'lat',
        'lon',
        'lookup_time'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        //Set table based on config
        $this->setTable(config('weatherbyip.tables.location_lookups', 'location_lookups'));
    }

    //Relations

    public function forecasts()
    {
        return $this->hasMany(Forecast::class);
    }
}