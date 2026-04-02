<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{HomeController , ControlPanelController, InfluxController};

Route::get('/', function () {
    return view('home');
})->name('home');

Route::get('/login', function () {
    return view('auth');
})->name('auth');
Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');
Route::get('/profile', function () {
    return view('profile');
})->name('profile');
Route::get('/control-panel/{room}', [ControlPanelController::class, 'index'])->name('control.panel');
Route::get('/fetch-status', [InfluxController::class, 'getLatestData']);
