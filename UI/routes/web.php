<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControlPanelController;
use App\Http\Controllers\InfluxController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/control-panel', [ControlPanelController::class, 'index']);
Route::get('/fetch-status', [InfluxController::class, 'getLatestData']);
