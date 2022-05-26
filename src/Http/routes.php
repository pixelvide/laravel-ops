<?php

use Illuminate\Support\Facades\Route;

// Device (DFP) URL
Route::get('/device', 'DeviceController@index');
Route::post('/device', 'DeviceController@index');

// Health URL
Route::get('/health', 'HealthController@health');