<?php

namespace PaulE\WeatherByIP\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

use \PaulE\WeatherByIP\Helpers\WeatherService;
use \PaulE\WeatherByIP\Helpers\IPLocationService;

/*
 * Given an IP Address, this command will retrieve and display 
 * the five-day weather forecast and location for that IP.
 * If no address is provided, will default to the current user IP.
 */
class GetForecast extends Command
{
    protected $signature = 'weatherbyip:forecast {ipAddress?  : The IP address to get location/forecast for. If left blank, will attempt to use user IP.}';

    protected $description = 'Get a five-day weather forecast for a given IP address. 
    If no IP address is provided, will default to the current IP address.';

    public function handle()
    {
        $this->info(__('weatherbyip::cli.cli_load'));

        //Get input IP (if it exists), validate, and use IPLocationService to either use that or to retrieve request IP
        $ipAddress = $this->argument('ipAddress');
        if($ipAddress != null 
        && !filter_var(
            $ipAddress, 
            FILTER_VALIDATE_IP, 
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
        )) {
            $this->info(__('weatherbyip::messages.error').": ".__('weatherbyip::cli.invalid_ip', ['ip' => $ipAddress]));
            return;
        }
        $ipAddress = IPLocationService::getIP($ipAddress);

        //Get location lookup object from IP
        $locationLookup = WeatherService::getForecastFromIP($ipAddress);
        
        if(!$locationLookup || empty($locationLookup->forecasts)) {
            $this->info(__('weatherbyip::messages.lookup_fail'));
        }
        //Output IP and location
        $this->info($ipAddress.": ".__('weatherbyip::cli.located_at')." ".$locationLookup->location);

        //Output forecasts from their lookup
        $this->info(__('weatherbyip::cli.weather_forecast').":");
        foreach($locationLookup->forecasts as $forecast) {
            $this->info(__('weatherbyip::cli.forecast_result',['date' => date('Y-m-d',strtotime($forecast->forecast_date)), 'weather' => __('weatherbyip::forecasts.'.$forecast->weather_code), 'min' => $forecast->temp_low, 'max' => $forecast->temp_high]));
        }
    }
}
