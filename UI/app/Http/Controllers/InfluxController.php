<?php

namespace App\Http\Controllers;

use InfluxDB2\Client;
use Throwable;

class InfluxController extends Controller
{
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

        foreach ($tables as $table) {
            foreach ($table->records as $record) {
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

        return response()->json($data);
    }
}