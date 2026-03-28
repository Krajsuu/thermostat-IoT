<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use InfluxDB2\Client;

class InfluxController extends Controller
{
    public function getLatestData()
    {
        $client = new Client([
            "url" => env('INFLUXDB_URL'),
            "token" => env('INFLUXDB_TOKEN'),
            "bucket" => env('INFLUXDB_BUCKET'),
            "org" => env('INFLUXDB_ORG'),
            "precision" => \InfluxDB2\Model\WritePrecision::S
        ]);

        $queryApi = $client->createQueryApi();

        $query = 'from(bucket: "'.env('INFLUXDB_BUCKET').'")
            |> range(start: -1h)
            |> filter(fn: (r) => r["_measurement"] == "temperature")
            |> filter(fn: (r) => r["sensor_id"] == "704BCA46BA4C")
            |> filter(fn: (r) => r["_field"] == "temp")
            |> last()';

        $result = $queryApi->query($query);
        
        // Wyciągamy samą wartość
        $temp = 0;
        if (isset($result[0]->records[0])) {
            $temp = $result[0]->records[0]->getValue();
        }

        return response()->json([
            'temperature' => $temp,
            'unit' => '°C',
            'device_id' => '704BCA46BA4C'
        ]);
    }
}