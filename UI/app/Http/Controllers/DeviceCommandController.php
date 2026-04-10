<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use PhpMqtt\Client\Facades\MQTT;

class DeviceCommandController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'device_uid' => 'required|string',
            'state' => 'required|integer|in:1,2,3',
            'target' => 'nullable|numeric|between:10,30',
        ]);

        $device = Device::where('device_uid', $request->device_uid)
            ->where('user_id', auth()->id())
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Brak dostępu do urządzenia'], 403);
        }

        // Temat MQTT thermio/user_X/devices/MAC/cmd
        $userId = auth()->id();
        $topic = "thermio/user_{$userId}/devices/{$request->device_uid}/cmd";

        $payload = [
            'state' => $request->state,
            'target' => $request->target
        ];

        // Wysyłka do AWS IoT
        MQTT::publish($topic, json_encode($payload));

        return response()->json(['status' => 'Command published to MQTT']);
    }
}