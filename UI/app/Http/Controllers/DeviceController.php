<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'device_uid' => ['required', 'string', 'max:255', 'unique:devices,device_uid'],
            'room_name' => ['required', 'string', 'max:255'],
        ]);

        Device::create([
            'user_id' => auth()->id(),
            'name' => $validated['name'],
            'device_uid' => $validated['device_uid'],
            'room_name' => $validated['room_name'],
            'is_active' => false,
            'last_seen_at' => now('Europe/Warsaw'),
        ]);

        return back()->with('success', 'Urządzenie zostało dodane.');
    }
}
