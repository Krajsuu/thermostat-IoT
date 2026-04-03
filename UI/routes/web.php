<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{HomeController , ControlPanelController, InfluxController, AuthController , DeviceController , ProfileController};

Route::get('/', function () {
    return view('home');
})->name('home');

// Rejstracja
Route::get('/login',[AuthController::class, 'view'])->name('auth');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Urządzenia
Route::get('/profile', [ProfileController::class, 'index'])
    ->name('profile');

Route::post('/devices', [DeviceController::class, 'store'])->name('device.store');

Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

Route::get('/control-panel/{room}', [ControlPanelController::class, 'index'])->name('control.panel');
Route::get('/fetch-status', [InfluxController::class, 'getLatestData']);
