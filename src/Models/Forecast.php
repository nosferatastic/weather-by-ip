<?php

namespace Paule\WeatherByIP\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forecast extends Model
{

    protected $fillable = [
        'location_lookup_id',
        'forecast_date',
        'weather_code',
        'temp_high',
        'temp_low'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setTable(config('weatherbyip.tables.forecasts', 'forecasts')); // valid method call
    }
}
