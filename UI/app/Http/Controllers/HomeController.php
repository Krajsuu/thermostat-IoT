<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HomeController extends Controller
{
    public function index()
    {
        $devices = auth()->user()->devices->map(function ($device) {
            return [
                'slug' => Str::slug($device->room_name),
                'name' => $device->room_name,
                'device_name' => $device->name,
                'device_uid' => $device->device_uid,
                'is_online' => $device->is_active,
                'temperature' => $device->is_active ? 'Ładowanie...' : '--- °C',
                'humidity' => $device->is_active ? 'Ładowanie...' : '---%',
                'mode' => $device->is_active ? 'AUTO' : '---',
                'heating' => $device->is_active ? 'Ładowanie...' : '---',
            ];
        });

        return view('dashboard', compact('devices'));
    }
}