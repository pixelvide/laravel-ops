<?php

use Illuminate\Support\Facades\Route;

// Health URL
Route::get('/health', 'HealthController@health');