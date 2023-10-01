<?php

namespace PaulE\WeatherByIP\Helpers;

use PaulE\WeatherByIP\Models\LocationLookup;

/*
 * This Helper file provides static functions which handle the retrieval of location from the user IP address
 * (& datetime if provided).
 */
class IPLocationService
{

    /* Given an IP address (or, if one not provided, use user's current location)
     * retrieve their location from IPInfo and use this to create a LocationLookup object.
     * If a LocationLookup has been made for this location recently (last hour), return that
     * so we don't have to look up their IP (and forecast) again.
     * 
     * @param ?string $ipAddress : IP Address to be looked up. Null means use current user IP.
     * @param ?string $datetime : datetime to be used as lookup time. Null means use current datetime.
     */
    public static function getLocation(?string $ipAddress = null, ?string $datetime = null) : ?LocationLookup
    {
        $ipAddress = IPLocationService::getIP($ipAddress);
        //We start by looking for an existing lookup, whose datetime is within an hour before now
        $compareDate = $datetime ? \DateTime::createFromFormat('Y-m-d H:i:s', $datetime) : now();
        $earliestDate = ($datetime ? \DateTime::createFromFormat('Y-m-d H:i:s', $datetime) : now())->add(\DateInterval::createFromDateString('-1 Hour'));
        $existingLookup = LocationLookup::where('ip','=',$ipAddress)
                                         ->where('lookup_time', '<=', $compareDate)
                                        ->where('lookup_time', '>=', $earliestDate)
                                        ->first();
        //If it exists, just return it.
        if($existingLookup) {
            return $existingLookup;
        }
    
        // Request location info from FREEIPAPI for the retrieved/submitted IP address
        $location = APICallService::httpGet("https://freeipapi.com/api/json/".$ipAddress);
        //if null, return null (this means the API call did not succeed)
        if($location == null) {
            return null;
        }

        $location = [
            'ip' => $ipAddress,
            'lat' => $location['latitude'],
            'lon' => $location['longitude'], 
            'locationName' => $location['cityName'].", ".$location['regionName'].", ".$location['countryCode']
        ];
        return IPLocationService::storeLocationLookup($location, $datetime);
    }

    /*
     * Retrieve the IP address to be used. If null or invalid, substitute with user IP address.
     * 
     * @param ?string $ipAddress : IP Address to be looked up. Null means use current user IP.
     */
    public static function getIP(?string $ipAddress) : string
    {
        //If IP not set, use user location
        if(!isset($ipAddress)) {
            $ipAddress = request()->ip();
        }
        //If the requested/current IP is bogon/private, pick something else...
        if(!filter_var(
            $ipAddress, 
            FILTER_VALIDATE_IP, 
            FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE
        )) {
            //Default to a suitable reference IP (Gold Coast, Queensland, AU)
            $ipAddress = "123.211.61.50";
        }

        return $ipAddress;
    }

    /*
     * Given location array and datetime (may be null,then use current time), store a LocationLookup object
     */
    private static function storeLocationLookup($location, $datetime) : LocationLookup
    {   
        return LocationLookup::create([
            'ip' => $location['ip'],
            'location' => $location['locationName'],
            'lat' => $location['lat'],
            'lon' => $location['lon'],
            'lookup_time' => $datetime ?? now()
        ]);
    }
}
