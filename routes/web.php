<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use PaulE\WeatherByIP\Http\Controllers\ForecastViewController;

Route::get('/forecast/{ipAddress?}', [ForecastViewController::class, 'show'])->name('forecast.show');
Route::get('/get/forecast/{ipAddress?}', [ForecastViewController::class, 'getForecastView'])->name('forecast.get');
