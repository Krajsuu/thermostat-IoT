<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{HomeController , ControlPanelController, InfluxController};

Route::get('/', [HomeController::class, 'index'])->name('dashboard');

Route::get('/control-panel/{room}', [ControlPanelController::class, 'index'])->name('control.panel');
Route::get('/fetch-status', [InfluxController::class, 'getLatestData']);
