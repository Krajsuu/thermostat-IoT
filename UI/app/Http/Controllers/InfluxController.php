<?php

namespace App\Http\Controllers;

use App\Services\InfluxHistoryService;
use InfluxDB2\Client;
use Throwable;

class InfluxController extends Controller
{
    public function getHistoryData(string $device_uid, InfluxHistoryService $historyService)
    {
        $device = auth()->user()->devices()->where('device_uid', $device_uid)->first();

        if (! $device) {
            return response()->json(['error' => 'Urządzenie nie znalezione'], 404);
        }

        $history = $historyService->forDevice($device_uid, (int) auth()->id());

        return response()->json([
            'historyPoints' => $history['historyPoints'],
            'historyPoints24h' => $history['historyPoints24h'],
            'historyPoints7d' => $history['historyPoints7d'],
            'historyPoints30d' => $history['historyPoints30d'],
            'historyLastUpdated' => $history['lastUpdatedLabel'],
        ]);
    }

    public function getLatestData(string $device_uid)
    {
        $device = auth()->user()->devices()->where('device_uid', $device_uid)->first();

        if (!$device) {
            return response()->json(['error' => 'Urządzenie nie znalezione'], 404);
        }

        try {
            $client = new Client([
            "url" => env('INFLUXDB_URL'),
            "token" => env('INFLUXDB_TOKEN'),
            "bucket" => env('INFLUXDB_BUCKET'),
            "org" => env('INFLUXDB_ORG'),
            ]);

            $queryApi = $client->createQueryApi();

            // Pobieramy dane z InfluxDB, filtrując po user_id oraz device_id.
            $query = 'from(bucket: "'.env('INFLUXDB_BUCKET').'")
                |> range(start: -1h)
                |> filter(fn: (r) => r["_measurement"] == "device_status")
                |> filter(fn: (r) => r["user_id"] == "user_' . auth()->id() . '")
                |> filter(fn: (r) => r["device_id"] == "' . $device_uid . '")
                |> last()';

            $tables = $queryApi->query($query);
        } catch (Throwable $e) {
            return response()->json(['error' => 'Błąd połączenia z InfluxDB'], 500);
        }
        
        $data = [
            'temperature' => 0,
            'humidity' => 0,
            'target' => 0,
            'state' => 3, // Domyślnie AUTO
            'heater' => 'OFF',
            'fan' => 'OFF'
        ];

        $hasRecords = false;
        foreach ($tables as $table) {
            foreach ($table->records as $record) {
                $hasRecords=true;
                $field = $record->getField();
                $value = $record->getValue();

                if ($field == 'temp') $data['temperature'] = round($value, 1);
                if ($field == 'hum') $data['humidity'] = round($value, 0);
                if ($field == 'target') $data['target'] = round($value, 1);
                if ($field == 'state') $data['state'] = (int)$value;
                if ($field == 'heater') $data['heater'] = ($value == 1) ? 'ON' : 'OFF';
                if ($field == 'fan') $data['fan'] = ($value == 1) ? 'ON' : 'OFF';
            }
        }
        if ($hasRecords) {
            $device->update([
                'last_seen_at' => now('Europe/Warsaw'),
                'is_active' => true,
            ]);
        } else {
            $device->update([
                'is_active' => false,
            ]);
        }
        return response()->json($data);
    }
}