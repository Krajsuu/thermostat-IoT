<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ControlPanelController extends Controller
{
    public function index(string $room)
    {
        $rooms = [
            'salon' => [
                'name' => 'Salon',
                'is_online' => true,
                'temperature' => 22.4,
                'humidity' => 48,
            ],
            'pokoj' => [
                'name' => 'Pokój',
                'is_online' => true,
                'temperature' => 25.0,
                'humidity' => 42,
            ],
            'biuro' => [
                'name' => 'Biuro',
                'is_online' => false,
                'temperature' => null,
                'humidity' => null,
            ],
        ];

        abort_unless(isset($rooms[$room]), 404);

        if (!$rooms[$room]['is_online']) {
            return redirect()->route('dashboard');
        }

        return view('control', [
            'room' => $rooms[$room],
            'temperature' => $rooms[$room]['temperature'] ?? 0,
            'humidity' => $rooms[$room]['humidity'] ?? 0,

            'historyPoints' => [
                ['label' => '10:00', 'temp' => 22.1],
                ['label' => '11:00', 'temp' => 22.2],
                ['label' => '12:00', 'temp' => 22.0],
                ['label' => '13:00', 'temp' => 22.3],
                ['label' => '14:00', 'temp' => 22.1],
            ],

            'historyPoints24h' => [
                ['label' => '1:00', 'temp' => 13,4],
                ['label' => '3:00', 'temp' => 13,4],
                ['label' => '5:00', 'temp' => 13,4],
                ['label' => '7:00', 'temp' => 17.0],
                ['label' => '9:00', 'temp' => 20.8],
                ['label' => '11:00', 'temp' => 21.8],
                ['label' => '13:00', 'temp' => 23.0],
                ['label' => '15:00', 'temp' => 22.9],
                ['label' => '17:00', 'temp' => 18.4],
                ['label' => '19:00', 'temp' => 23.1],
                ['label' => '21:00', 'temp' => 23.0],
                ['label' => '23:00', 'temp' => 22.9],
            ],

            'historyPoints7d' => [
                ['label' => 'Pn', 'temp' => 21.6],
                ['label' => 'Wt', 'temp' => 22.8],
                ['label' => 'Śr', 'temp' => 22.1],
                ['label' => 'Czw', 'temp' => 23.0],
                ['label' => 'Pt', 'temp' => 22.4],
                ['label' => 'Sb', 'temp' => 22.9],
                ['label' => 'Nd', 'temp' => 22.2],
            ],

            'historyPoints30d' => [
                ['label' => '1', 'temp' => 21.5],
                ['label' => '5', 'temp' => 22.0],
                ['label' => '10', 'temp' => 23.1],
                ['label' => '15', 'temp' => 22.7],
                ['label' => '20', 'temp' => 22.2],
                ['label' => '25', 'temp' => 22.9],
                ['label' => '30', 'temp' => 22.4],
            ],
        ]);
    }
}