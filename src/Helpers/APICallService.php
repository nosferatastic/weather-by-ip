<?php

namespace PaulE\WeatherByIP\Helpers;

/*
 * This Helper file provides static functions which handle API calls according to set http_driver in config.
 * At present, only used for GET call for the two APIs.
 */
class APICallService
{

    /*
     * Helper function to perform an API GET call on the requested URL.
     * Returns null if an exception occured
     * 
     * @param string $url : URL to be requested via GET
     */
    public static function httpGet($url) : ?array
    {
        try {
            //Based on http_driver config we either use guzzlehttp or curl. Default is guzzle
            if(config('weatherbyip.http_driver','guzzle') == "guzzle") {
                // Create a Guzzle client instance
                $client = new \GuzzleHttp\Client();
                
                //Perform request
                $response = $client->get($url);
                $output = json_decode($response->getBody(), true);
            } else {
                // Create a curl instance
                $curl = curl_init();
                
                //Perform request
                curl_setopt($curl, CURLOPT_URL, $url);
                //Set to return contents
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $output = json_decode(curl_exec($curl), true);
            }
        } catch (\Exception $e) {
            //Null means a failed API call
            return null;
        }
        return $output;
    }
}