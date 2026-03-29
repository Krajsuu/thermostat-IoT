<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $devices = [
            [
                'slug' => 'salon',
                'name' => 'Salon',
                'is_online' => true,
                'temperature' => '22.4°C',
                'humidity' => '48%',
                'mode' => 'AUTO',
                'heating' => 'ON',
            ],
            [
                'slug' => 'pokoj',
                'name' => 'Pokój',
                'is_online' => true,
                'temperature' => '25°C',
                'humidity' => '42%',
                'mode' => 'MANUAL',
                'heating' => 'OFF',
            ],
            [
                'slug' => 'biuro',
                'name' => 'Biuro',
                'is_online' => false,
                'temperature' => '--- °C',
                'humidity' => '---%',
                'mode' => 'MANUAL',
                'heating' => 'OFF',
            ],
        ];

        return view('dashboard', compact('devices'));
    }
}
