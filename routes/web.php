<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;


Route::get('/',function(){
    return redirect('/weather');
});
Route::get('/weather', [WeatherController::class, 'fetchWeather']);
Route::get('/searchCity', [WeatherController::class, 'fetchWeather']); 
Route::get('/api/autocomplete', [WeatherController::class, 'autocomplete']);

