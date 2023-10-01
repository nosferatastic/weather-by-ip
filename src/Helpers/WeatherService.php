<?php

namespace PaulE\WeatherByIP\Helpers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Collection;

use PaulE\WeatherByIP\Models\LocationLookup;
use PaulE\WeatherByIP\Models\Forecast;

class WeatherService
{

    /*
     * Given an IP address (and datetime), perform location and forecast lookup, store associated values, and return lookup object (null if error)
     * 
     * @param string $ipAddress : IP address submitted by user. If null, we will use the user's current IP
     * @param string $datetime : datetime submitted by user. If null, we will use the current datetime as the request time
     */
    public static function getForecastFromIP(?string $ipAddress = null, ?string $datetime = null) : ?LocationLookup
    {
        //Retrieve location and forecast using API services
        $locationLookup = IPLocationService::getLocation($ipAddress, $datetime);
        //If locationLookup was null, there was an issue getting the location
        if($locationLookup) {
            $weather = WeatherService::getForecast($locationLookup);
            if($weather) {
                //Then return location lookup object, which can be used to retrieve forecasts
                return $locationLookup;
            }
            //If the weather object did not exist, it was not retrieved properly so we can get rid of the lookup object
            $locationLookup->delete();
        }
        //null here implies there was an issue
        return null;
    }

    /*
     * Given a LocationLookup object, either retrieve, store, and return the forecasts from OPENMETEO, 
     * or - if it already has them - return the existing forecast objects. Returns null if API error.
     * 
     * @param LocationLookup $locationLookup : LocationLookup class object for which to retrieve/store forecast info
     */
    public static function getForecast(LocationLookup $locationLookup) : ?Collection
    {
        if($locationLookup->forecasts()->first()) {
            //If it already has forecasts, just return those
            return $locationLookup->forecasts;
        }

        $lat = $locationLookup->lat;
        $lon = $locationLookup->lon;
    
        // Request weather info from OPENMETEO
        $weatherResponse = APICallService::httpGet("https://api.open-meteo.com/v1/forecast?latitude=".$lat."&longitude=".$lon."&daily=weathercode,temperature_2m_max,temperature_2m_min&current_weather=true&timezone=auto");

        //We only need the daily weather
        $forecast = $weatherResponse['daily'];
        //if null, return null (this means the API call did not succeed)
        if($forecast == null) {
            return null;
        }

        //Interpret and arrange returned data into five day forecast
        $forecasts = array();
        foreach($forecast['time'] as $key => $forecastDate) {
            if($key < 5) {
                $newForecast = array();
                $newForecast['date'] = $forecastDate;
                $newForecast['min'] = $forecast['temperature_2m_min'][$key];
                $newForecast['max'] = $forecast['temperature_2m_max'][$key];
                $newForecast['weather_code'] = $forecast['weathercode'][$key];
                $forecasts[] = $newForecast;
            }
        }

        return WeatherService::storeForecast($locationLookup, $forecasts);
    }

    /*
     * Given a LocationLookup object and arrays representing forecasts returned from OPENMETEO,
     * store the forecasts in relation to the LocationLookup.
     * 
     * @param LocationLookup $locationLookup : Object for which the forecasts will be related to.
     * @param array $forecasts : Returned/manipulated API data from OPENMETEO.
     */ 
    private static function storeForecast(LocationLookup $locationLookup, array $forecasts) : Collection
    {
        foreach($forecasts as $forecast) {
            $locationLookup->forecasts()->create([
                'forecast_date' => $forecast['date'],
                'weather_code' => $forecast['weather_code'],
                'temp_high' => $forecast['max'],
                'temp_low' => $forecast['min']
            ]);
        }
        return $locationLookup->forecasts;
    }
}