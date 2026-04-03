<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InfluxDB2\Client;

class InfluxController extends Controller
{
    public function getLatestData()
    {
        $client = new \InfluxDB2\Client([
            "url" => env('INFLUXDB_URL'),
            "token" => env('INFLUXDB_TOKEN'),
            "bucket" => env('INFLUXDB_BUCKET'),
            "org" => env('INFLUXDB_ORG'),
        ]);

        $queryApi = $client->createQueryApi();

        // Zapytanie do odpowiedniego measurementu: "temperature"
        $query = 'from(bucket: "'.env('INFLUXDB_BUCKET').'")
            |> range(start: -1h)
            |> filter(fn: (r) => r["_measurement"] == "temperature")
            |> last()';

        $tables = $queryApi->query($query);
        
        $data = [
            'temperature' => 0,
            'humidity' => 0,
            'target' => 0,
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
                if ($field == 'heater') $data['heater'] = ($value == 1) ? 'ON' : 'OFF';
                if ($field == 'fan') $data['fan'] = ($value == 1) ? 'ON' : 'OFF';
            }
        }

        return response()->json($data);
    }
}