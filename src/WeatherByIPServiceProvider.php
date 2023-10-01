<?php

namespace PaulE\WeatherByIP;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

use \PaulE\WeatherByIP\Console\{GetForecast, DeleteForecasts};

class WeatherByIPServiceProvider extends ServiceProvider
{
  public function register()
  {
    //
    //Register facade
    $this->app->bind('weatherbyip', function($app) {
        return new WeatherByIP();
    });
    //Register config file
    $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'weatherbyip');
  

  }

  public function boot()
  {
    //Load in routes, migrations, views
    $this->registerRoutes();
    $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    $this->loadViewsFrom(__DIR__.'/../resources/views', 'weatherbyip');
    //Load translations and publish translations to application lang folder (so user can override if they wish)
    $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'weatherbyip');
    $this->publishes([
        __DIR__.'/../resources/lang' => resource_path('lang/vendor/weatherbyip'),
    ], 'wbip_lang');
    //Export config file to be overridden if desired
    $this->publishes([
      __DIR__.'/../config/config.php' => config_path('weatherbyip.php'),
    ], 'wbip_config');
    // Register the command for running in CLI
    if ($this->app->runningInConsole()) {
        $this->commands([
            GetForecast::class,
            DeleteForecasts::class,
        ], 'wbip_commands');
        $this->publishes([
          __DIR__.'/../resources/assets' => public_path('packages/weatherbyip'),
        ], 'wbip_assets');
      
    }
  }

  protected function registerRoutes()
  {
      Route::group($this->routeConfiguration(), function () {
          $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
      });
  }
  
  protected function routeConfiguration()
  {
      return [
          'prefix' => config('weatherbyip.url_prefix'),
          'middleware' => config('weatherbyip.middleware'),
      ];
  }
  
}
