<?php

use Illuminate\Support\Facades\Route;

Route::get('/api/device-status', [InfluxController::class, 'getLatestData']);

Route::get('/', function () {
    return view('dashboard');
});
