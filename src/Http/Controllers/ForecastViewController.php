<?php

namespace PaulE\WeatherByIP\Http\Controllers;

use Illuminate\Support\Facades\File;

use \PaulE\WeatherByIP\Helpers\WeatherService;
use \PaulE\WeatherByIP\Helpers\IPLocationService;

class ForecastViewController extends Controller
{

    /*
     * Display the Forecast front-end page which the user can use to input an IP and retrieve a forecast.
     * 
     * @param ?string $ipAddress : If valid, the default IP address. If invalid or null, user IP will be displayed on load.
     */
    public function show(?string $ipAddress = null)
    {
        $ipAddress = IPLocationService::getIP($ipAddress);

        return view('weatherbyip::forecast.home',
        [
            'ip' => $ipAddress
        ]);
    }

    /*
     * AJAX-run function used to retrieve forecast view for the IP address provided (or user IP if it is not).
     * 
     * @param ?string $ipAddress : If valid, the IP address for which forecast is loaded. If invalid or null, user IP will be displayed on load.
     */
    public function getForecastView(?string $ipAddress = null)
    {
        if($ipAddress != null && !filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            return "<br><i style='color:red;'>Invalid IP address format.</i>";
        }
        $ipAddress = IPLocationService::getIP($ipAddress);

        //Get location & weather forecast from lat/lon
        $location = WeatherService::getForecastFromIP($ipAddress);

        return view('weatherbyip::forecast.show',
        [
            'ip' => $ipAddress,
            'forecast' => $location?->forecasts,
            'location' => $location
        ]);
    }
}