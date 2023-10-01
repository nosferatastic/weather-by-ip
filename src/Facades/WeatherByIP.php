<?php

namespace PaulE\WeatherByIP\Facades;

use Illuminate\Support\Facades\Facade;

class WeatherByIP extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'weatherbyip';
    }
}
