<?php

namespace App\Http\Controllers;

use App\Services\InfluxHistoryService;
use Illuminate\Support\Str;

class ControlPanelController extends Controller
{
    public function index(string $room, InfluxHistoryService $influxHistory)
    {
        $device = auth()->user()->devices->first(function ($device) use ($room) {
            return Str::slug($device->room_name) === $room;
        });

        abort_unless($device, 404);

        if (! $device->is_active) {
            return redirect()->route('dashboard');
        }

        $history = $influxHistory->forDevice($device->device_uid, (int) auth()->id());

        return view('control', [
            'room' => [
                'name' => $device->room_name,
                'is_online' => $device->is_active,
                'temperature' => 0,
                'humidity' => 0,
                'device_uid' => $device->device_uid,
                'device_name' => $device->name,
            ],
            'temperature' => 0,
            'humidity' => 0,

            'historyPoints' => $history['historyPoints'],
            'historyPoints24h' => $history['historyPoints24h'],
            'historyPoints7d' => $history['historyPoints7d'],
            'historyPoints30d' => $history['historyPoints30d'],
            'historyLastUpdated' => $history['lastUpdatedLabel'],
        ]);
    }
}
