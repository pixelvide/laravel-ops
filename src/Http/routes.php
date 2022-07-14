<?php

use Illuminate\Support\Facades\Route;

// Device (DFP) URL
Route::get('/device', 'DeviceController@index');
Route::post('/device', 'DeviceController@index');
Route::get('/device/verify', 'DeviceController@verify');

// Health URL
Route::get('/health', 'HealthController@health');