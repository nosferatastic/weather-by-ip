<?php

namespace PaulE\WeatherByIP;

use \PaulE\WeatherByIP\Helpers\WeatherService;
use \PaulE\WeatherByIP\Helpers\IPLocationService;

/*
 * This class contains the function used by the WeatherByIP facade to retrieve location/forecast data given
 * user input in the required format.
 */
class WeatherByIP
{

    /*
     * Given a data payload in the required pattern (array of size 2, containing ip and datetime)
     * Perform location and weather forecast lookup (and storage of same)
     * Can be used from anywhere in application including this package
     * 
     * @param array $payload : Array matching format following pattern 
     * [‘ip’=> ‘212.49.244.121’,’datetime’ => ‘2017-06-01 17:30:00’]
     */ 
    public function getForecast(array $payload)
    {
        if(count($payload) != 2 
            || !filter_var($payload['ip'], FILTER_VALIDATE_IP)
            || \DateTime::createFromFormat('Y-m-d H:i:s', $payload['datetime']) == false
        ) {
            return ['error' => 'Invalid request format.'];
        }
        //Get the IP to be checked (current user IP if one declared is null)
        $ipAddress = IPLocationService::getIP($payload['ip']);

        //Get location/weather forecast from lat/lon
        $locationLookup = WeatherService::getForecastFromIP($ipAddress, $payload['datetime']);

        if(!$locationLookup) {
            //Error
            return ['error' => 'There was a problem retrieving the location & weather forecast. Please try again later.'];
        }
        $forecasts = $locationLookup->forecasts()->select('forecast_date','weather_code','temp_high','temp_low')->get();
        //Retrieve translated forecast conditions value
        foreach($forecasts as $forecast) {
            $forecast->conditions = __('weatherbyip::forecasts.'.$forecast->weather_code);
        }

        return [
            'ip' => $ipAddress, 
            'location' => $locationLookup->only('id','location','lookup_time'), 
            'forecast' => $forecasts
        ];
    }
}
