<?php

use Illuminate\Support\Facades\Route;

// DFP Device URL
Route::get('/device', 'DFPController@index');

// Health URL
Route::get('/health', 'HealthController@health');