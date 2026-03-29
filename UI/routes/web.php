<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ControlPanelController;

Route::get('/', function () {
    return view('dashboard');
});

Route::get('/control-panel', [ControlPanelController::class, 'index']);
