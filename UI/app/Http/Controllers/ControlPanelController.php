<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ControlPanelController extends Controller
{
    public function index()
    {
        $temperature = 22.4;
        $humidity = 48;

        $historyPoints = [
            ['label' => '10:00', 'temp' => 22.1],
            ['label' => '11:00', 'temp' => 22.2],
            ['label' => '12:00', 'temp' => 22.0],
            ['label' => '13:00', 'temp' => 22.0],
            ['label' => '14:00', 'temp' => 22.1],
        ];

        return view('control', [
            'temperature' => $temperature,
            'humidity' => $humidity,
            'historyPoints' => $historyPoints,
        ]);
    }


}
