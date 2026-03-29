<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InfluxController;
Route::get('/fetch-status', [InfluxController::class, 'getLatestData']);

Route::get('/', function () {
    return view('dashboard');
});
