# Weather By IP

This Laravel Package enables the retrieval of five-day weather forecasts from provided or identified user IP address. This can be performed via a web view, a Facade, or within the CLI via artisan command.

## Installation & Setup
You can install the package via composer as it is hosted on Packagist:

`composer require paule/weather-by-ip`

The package will automatically register itself using Laravel’s package discovery feature.

You also need to publish the package’s resource, config and lang files to your application filesystem with:

```bash
php artisan vendor:publish --provider="PaulE\WeatherByIP\WeatherByIPServiceProvider"
```

Unless this is done, proper styling and assets will not load on the web view.

After this, please run the command

```bash
php artisan migrate
```

to will set up the database tables for the package. (See below for config information if you wish to customise the table names)

The package's config file contains the following by default:

```php
return [
  'url_prefix' => 'weatherbyip',
  'middleware' => ['web'],
  'http_driver' => 'guzzle',
  // Set above to anything but guzzle to use cURL
  'tables' => [
    'location_lookups' => 'location_lookups',
    'forecasts' => 'forecasts'
  ],
];
```

If desired, the `url_prefix`, `http_driver`, and `tables` values can be overwritten to easily adjust the functionality of the package, as can the `middleware` for web view routes as needed.


## Usage

The package provides three access routes for the functionality:

### Facade

The package provides a facade that you can use to access the package’s functionality. The facade’s class name is `WeatherByIP` and its alias is `WeatherByIP`.

The function for retrieving location/forecast information is `getForecast(array $payload)` and requires to be declared with an array input following the pattern below:

```php
 $output = WeatherByIP::getForecast(['ip'=> '212.49.244.121','datetime' => '2017-06-01 17:30:00']);
```

This will then return either an error, i in format

```php
['error' => 'Error message']
```

or a successful request will return in a format eg.

```json
{"ip":"123.211.61.50","location":{"id":11,"location":"Brisbane, Queensland, AU","lookup_time":"2023-09-01 12:10:18"},"forecast":[{"forecast_date":"2023-09-01","weather_code":45,"temp_high":26.9,"temp_low":15.3,"conditions":"Foggy"},{"forecast_date":"2023-09-02","weather_code":2,"temp_high":26.1,"temp_low":14.6,"conditions":"Partly Cloudy"},{"forecast_date":"2023-09-03","weather_code":45,"temp_high":28.7,"temp_low":12.6,"conditions":"Foggy"},{"forecast_date":"2023-09-04","weather_code":45,"temp_high":26.7,"temp_low":15,"conditions":"Foggy"},{"forecast_date":"2023-09-05","weather_code":3,"temp_high":26.7,"temp_low":17.4,"conditions":"Cloudy"}]}
```

### Web View

The package also provides a web view that you can access, by default, at the `/weatherbyip/forecast` route. If the `url_prefix` config is changed, the routes will be changed accordingly.
The web view allows the user to input an IP address and retrieve the upcoming five-day forecast for the associated location.

### Artisan Commands

The package also provides an artisan command that you can use to retrieve a five-day forecast from an IP address. The command’s name is `weatherbyip:forecast` and it has one conditional argument, the IP address. If an IP address is not provided, it will retrieve for the user's request IP (or, as a fallback, `123.211.61.50` if the user's request IP is private/reserved).

For example, running the command as below:

```bash
php artisan weatherbyip:forecast 122.62.248.72
```

will return in the following format:

```bash
Retrieving Weather By IP...
122.62.248.72: located at Gore, Southland, NZ
Weather Forecast:
2023-09-01: Cloudy, Temperature 4.3 - 16.9 C
2023-09-02: Light Rain, Temperature 4.1 - 14.5 C
2023-09-03: Partly Cloudy, Temperature 1.1 - 15 C
2023-09-04: Cloudy, Temperature 5.3 - 18.8 C
2023-09-05: Cloudy, Temperature 11.6 - 21.6 C
```

You can see the list of available options and arguments by running:

```bash
php artisan help weatherbyip:forecast
```

There is a second artisan command, `weatherbyip:delete` which will take the user through a flow to delete all existing location lookups and forecasts from the database.

#### Settings

Within the config file, if the value of `http_driver` is set to anything but `guzzle`, the package will switch to using cURL for API endpoint retrieval instead of GuzzleHttp.

Also within the config file, the default database table names are set in the `tables` array. These can be modified to different values to ensure that there are no conflicts.

Icon images are published when the `vendor:publish` command is executed, to your applications `public/packages/weatherbyip/forecast_icons` folder, with the file name of each image corresponding to the Open Meteo weather code corresponding to the icon. These can be replaced if desired but must remain in `.png` format, preferably at the same size. If you'd like a reference of what each weather code means, I recommend checking the `lang/.../forecasts.php` file, or visiting Open Meteo's API documentation.

This package contains English and French translations, and language files are published to your application's `lang` folder when the `vendor:publish` command is executed. Additional translations can then be added if needed.

## Credits

Thanks to Materialize, Free IP API, Open Meteo, and Open Weather Map for providing styling, API endpoints, and icons.